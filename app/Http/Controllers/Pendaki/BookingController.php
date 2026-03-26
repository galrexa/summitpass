<?php

namespace App\Http\Controllers\Pendaki;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Mountain;
use App\Models\Payment;
use App\Models\QrPass;
use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['mountain', 'trail', 'participants'])
            ->where('leader_user_id', Auth::id())
            ->latest()
            ->get();

        return view('pendaki.bookings', compact('bookings'));
    }

    public function create()
    {
        $user = Auth::user();

        if (! $user->nik && ! $user->passport_number) {
            return redirect()->route('profile.setup')
                ->with('warning', 'Lengkapi identitas (NIK/Paspor) terlebih dahulu sebelum booking.');
        }

        $mountains = Mountain::active()->with(['regulation', 'trails' => fn($q) => $q->active()])->get();

        return view('pendaki.bookings.create', compact('mountains', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->nik && ! $user->passport_number) {
            return back()->withErrors(['profile' => 'Lengkapi profil terlebih dahulu.']);
        }

        $validated = $request->validate([
            'mountain_id'    => 'required|exists:mountains,id',
            'trail_id'       => 'required|exists:trails,id',
            'start_date'     => 'required|date|after_or_equal:today',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'guide_requested'=> 'boolean',
            'tos_accepted'   => 'accepted',
            'participants'   => 'required|array|min:1',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.nik'  => 'required|string|size:16|regex:/^[0-9]{16}$/',
        ]);

        $trail    = Trail::with('mountain.regulation')->findOrFail($validated['trail_id']);
        $reg      = $trail->mountain->regulation;
        $days     = (int) \Carbon\Carbon::parse($validated['start_date'])
                        ->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;
        $paxCount = count($validated['participants']);

        // Check max participants per account
        if ($reg && $reg->max_participants_per_account && $paxCount > $reg->max_participants_per_account) {
            return back()->withInput()
                ->withErrors(['participants' => "Maksimal {$reg->max_participants_per_account} peserta per booking."]);
        }

        // Check max hiking days
        if ($reg && $reg->max_hiking_days && $days > $reg->max_hiking_days) {
            return back()->withInput()
                ->withErrors(['end_date' => "Maksimal {$reg->max_hiking_days} hari pendakian."]);
        }

        // Check elevation experience requirement
        if ($reg && $reg->min_elevation_experience) {
            $highestCompleted = Booking::where('leader_user_id', $user->id)
                ->where('status', 'completed')
                ->join('mountains', 'mountains.id', '=', 'bookings.mountain_id')
                ->max('mountains.height_mdpl') ?? 0;

            if ($highestCompleted < $reg->min_elevation_experience) {
                return back()->withInput()
                    ->withErrors([
                        'mountain_id' => "Gunung ini mensyaratkan pengalaman mendaki gunung minimal {$reg->min_elevation_experience} MDPL. "
                            . "Pendakian tertinggi yang pernah kamu selesaikan: " . ($highestCompleted > 0 ? "{$highestCompleted} MDPL" : "belum ada") . ".",
                    ]);
            }
        }

        // Determine visitor type & day
        $isForeign = ! $user->nik && $user->passport_number;
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $isWeekend = $startDate->isWeekend();

        // Parse umur setiap peserta dari NIK
        $participantAges = collect($validated['participants'])->map(function ($pax) {
            $age = \App\Models\MountainRegulation::ageFromNik($pax['nik'] ?? '');
            return ['name' => $pax['name'], 'nik' => $pax['nik'], 'age' => $age];
        });

        $minorCount  = $participantAges->filter(fn($p) => $p['age'] !== null && $p['age'] < 17)->count();
        $adultCount  = $participantAges->filter(fn($p) => $p['age'] === null || $p['age'] >= 17)->count();

        // Leader (peserta[0]) tidak boleh di bawah 17 tahun
        $leaderAge = $participantAges->first()['age'];
        if ($leaderAge !== null && $leaderAge < 17) {
            return back()->withInput()
                ->withErrors(['participants' => 'Pemesan/ketua kelompok harus berumur minimal 17 tahun.']);
        }

        // Peserta di bawah 17 tahun wajib didampingi pendaki dewasa
        if ($reg && $reg->minor_must_be_accompanied && $minorCount > 0 && $adultCount < 1) {
            return back()->withInput()
                ->withErrors(['participants' => 'Peserta di bawah 17 tahun wajib didampingi minimal 1 pendaki dewasa (≥ 17 tahun) dalam booking yang sama.']);
        }

        // Validasi kewajiban guide berdasarkan level Permen LHK 13/2024
        $guideRequested = $request->boolean('guide_requested');
        if ($reg) {
            $guideLevel = $reg->guide_requirement_level ?? 'none';

            if (in_array($guideLevel, ['mandatory', 'expert_only']) && ! $guideRequested) {
                $label = $guideLevel === 'expert_only'
                    ? 'Jalur ini wajib menggunakan tenaga ahli/pemandu bersertifikasi khusus (Grade V).'
                    : 'Jalur ini wajib menggunakan pemandu bersertifikat (Grade IV).';
                return back()->withInput()->withErrors(['guide_requested' => $label]);
            }
        }

        // Hitung total harga per-peserta (dewasa vs pelajar/anak)
        $totalPrice = 0;
        if ($reg) {
            foreach ($participantAges as $pax) {
                $isStudent = ($pax['age'] !== null && $pax['age'] < 17);
                $totalPrice += $reg->priceFor((bool) $isForeign, $isWeekend, $isStudent);
            }

            // Tambah biaya guide jika diminta (guide_price_per_day × jumlah hari)
            if ($guideRequested && $reg->guide_price_per_day) {
                $totalPrice += (float) $reg->guide_price_per_day * $days;
            }
        }

        DB::transaction(function () use ($validated, $user, $totalPrice, $days) {
            $booking = Booking::create([
                'leader_user_id'  => $user->id,
                'mountain_id'     => $validated['mountain_id'],
                'trail_id'        => $validated['trail_id'],
                'start_date'      => $validated['start_date'],
                'end_date'        => $validated['end_date'],
                'guide_requested' => $validated['guide_requested'] ?? false,
                'tos_accepted_at' => now(),
                'status'          => 'pending_payment',
                'total_price'     => $totalPrice,
            ]);

            foreach ($validated['participants'] as $idx => $pax) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'user_id'    => ($idx === 0) ? $user->id : null,
                    'nik'        => $pax['nik'],
                    'name'       => $pax['name'],
                    'role'       => ($idx === 0) ? 'leader' : 'member',
                ]);
            }

            session(['new_booking_id' => $booking->id]);
        });

        $bookingId = session()->pull('new_booking_id');

        return redirect()->route('pendaki.bookings.show', $bookingId)
            ->with('success', 'Booking berhasil dibuat! Selesaikan pembayaran untuk mengaktifkan SIMAKSI.');
    }

    public function show($id)
    {
        $booking = Booking::with(['mountain', 'trail', 'participants', 'payment'])
            ->where('leader_user_id', Auth::id())
            ->findOrFail($id);

        return view('pendaki.bookings.show', compact('booking'));
    }

    public function simulatePay($id)
    {
        $booking = Booking::with('participants')
            ->where('leader_user_id', Auth::id())
            ->where('status', 'pending_payment')
            ->findOrFail($id);

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status'       => 'paid',
                'booking_code' => Booking::generateBookingCode(),
            ]);

            $now = now();

            Payment::create([
                'booking_id'     => $booking->id,
                'gateway'        => 'simulate',
                'transaction_id' => 'SIM-' . strtoupper(uniqid()),
                'status'         => 'paid',
                'amount'         => $booking->total_price,
                'paid_at'        => $now,
            ]);

            foreach ($booking->participants as $participant) {
                QrPass::firstOrCreate(
                    ['booking_participant_id' => $participant->id],
                    [
                        'qr_token'    => QrPass::generateToken(),
                        'valid_from'  => $booking->start_date->startOfDay(),
                        'valid_until' => $booking->end_date->endOfDay(),
                        'status'      => $now->between($booking->start_date, $booking->end_date->endOfDay())
                                            ? 'active' : 'inactive',
                    ]
                );
            }
        });

        return redirect()->route('pendaki.bookings.show', $id)
            ->with('success', 'Pembayaran berhasil! QR SummitPass telah diterbitkan untuk semua peserta.');
    }

    /** AJAX: return active trails for a mountain */
    public function trails($mountainId)
    {
        $trails = Trail::active()
            ->where('mountain_id', $mountainId)
            ->select('id', 'name', 'description', 'grade')
            ->orderBy('route_order')
            ->get();

        return response()->json($trails);
    }
}

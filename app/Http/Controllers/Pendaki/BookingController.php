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
        $bookings = Booking::with(['mountain', 'trail', 'trailOut', 'participants'])
            ->where('leader_user_id', Auth::id())
            ->latest()
            ->get();

        return view('pendaki.bookings', compact('bookings'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->nik && !$user->passport_number) {
            return redirect()->route('profile.setup')
                ->with('warning', 'Lengkapi identitas (NIK/Paspor) terlebih dahulu sebelum booking.');
        }

        $mountains = Mountain::active()
            ->with(['regulation', 'trails' => fn($q) => $q->active()])
            ->get();

        return view('pendaki.bookings.create', compact('mountains', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->nik && !$user->passport_number) {
            return back()->withErrors(['profile' => 'Lengkapi profil terlebih dahulu.']);
        }

        $validated = $request->validate([
            'mountain_id'     => 'required|exists:mountains,id',
            'trail_id'        => 'required|exists:trails,id',
            // trail_out_id: wajib ada jika is_cross_trail = true
            'trail_out_id'    => 'nullable|exists:trails,id',
            'start_date'      => 'required|date|after_or_equal:today',
            'end_date'        => 'required|date|after_or_equal:start_date',
            'guide_requested' => 'boolean',
            'tos_accepted'    => 'accepted',
            'participants'    => 'required|array|min:1',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.nik'  => 'required|string|size:16|regex:/^[0-9]{16}$/',
        ]);

        // ── Validasi lintas jalur ─────────────────────────────────────────
        $isCrossTrail = isset($validated['trail_out_id'])
            && $validated['trail_out_id'] != $validated['trail_id'];

        if ($isCrossTrail) {
            // trail_out_id harus milik gunung yang sama
            $trailOut = Trail::findOrFail($validated['trail_out_id']);
            if ((int) $trailOut->mountain_id !== (int) $validated['mountain_id']) {
                return back()->withInput()
                    ->withErrors(['trail_out_id' => 'Jalur turun harus berada di gunung yang sama.']);
            }
        }

        $trail = Trail::with('mountain.regulation')->findOrFail($validated['trail_id']);
        $reg   = $trail->mountain->regulation;
        $days  = (int) \Carbon\Carbon::parse($validated['start_date'])
                    ->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;
        $paxCount = count($validated['participants']);

        if ($reg && $reg->max_participants_per_account && $paxCount > $reg->max_participants_per_account) {
            return back()->withInput()
                ->withErrors(['participants' => "Maksimal {$reg->max_participants_per_account} peserta per booking."]);
        }

        if ($reg && $reg->max_hiking_days && $days > $reg->max_hiking_days) {
            return back()->withInput()
                ->withErrors(['end_date' => "Maksimal {$reg->max_hiking_days} hari pendakian."]);
        }

        // Hitung harga
        $trailFee = $reg ? ($reg->base_price ?? 0) : 0;
        $guidePrice = ($validated['guide_requested'] ?? false) && $reg
            ? ($reg->guide_price_per_day ?? 0) * $days
            : 0;
        $totalPrice = ($trailFee * $paxCount) + $guidePrice;

        DB::transaction(function () use ($validated, $user, $isCrossTrail, $totalPrice, $days, $reg) {
            $booking = Booking::create([
                'leader_user_id'  => $user->id,
                'mountain_id'     => $validated['mountain_id'],
                'trail_id'        => $validated['trail_id'],
                'trail_out_id'    => $isCrossTrail ? $validated['trail_out_id'] : null, // ← BARU
                'is_cross_trail'  => $isCrossTrail,                                      // ← BARU
                'start_date'      => $validated['start_date'],
                'end_date'        => $validated['end_date'],
                'guide_requested' => $validated['guide_requested'] ?? false,
                'tos_accepted_at' => now(),
                'status'          => 'pending_payment',
                'total_price'     => $totalPrice,
            ]);

            foreach ($validated['participants'] as $idx => $pax) {
                if ($idx === 0) {
                    $linkedUserId = $user->id;
                } else {
                    // Auto-link ke akun SummitPass jika NIK cocok
                    $linkedUserId = \App\Models\User::where('nik', $pax['nik'])->value('id');
                }

                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'user_id'    => $linkedUserId,
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
        $booking = Booking::with(['mountain', 'trail', 'trailOut', 'participants', 'payment']) // ← tambah trailOut
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

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\Mountain;
use App\Models\Trail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Daftar booking milik user (sebagai leader)
     */
    public function index(Request $request)
    {
        $query = Booking::with(['mountain', 'trail'])
            ->byLeader($request->user()->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('start_date', 'desc')->paginate(10);

        return response()->json([
            'data' => $bookings->items(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'per_page'     => $bookings->perPage(),
                'total'        => $bookings->total(),
            ],
        ], 200);
    }

    /**
     * Detail booking
     */
    public function show(Request $request, $id)
    {
        $booking = Booking::with(['mountain.regulation', 'trail', 'participants', 'payment'])
            ->byLeader($request->user()->id)
            ->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        return response()->json(['data' => $booking], 200);
    }

    /**
     * Buat booking baru (SIMAKSI Digital)
     *
     * Alur:
     * 1. Validasi regulasi gunung (max hari, kuota jalur, guide required)
     * 2. Validasi jumlah peserta (max_participants_per_account)
     * 3. Buat Booking + BookingParticipant
     * 4. Status: pending_payment (booking_code diterbitkan setelah bayar)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mountain_id'          => 'required|integer|exists:mountains,id',
            'trail_id'             => 'required|integer|exists:trails,id',
            'start_date'           => 'required|date|after_or_equal:today',
            'end_date'             => 'required|date|after:start_date',
            'guide_requested'      => 'required|boolean',
            'tos_accepted'         => 'required|accepted',
            'participants'         => 'required|array|min:1',
            'participants.*.name'  => 'required|string|max:255',
            'participants.*.nik'   => 'required|string|size:16|regex:/^[0-9]{16}$/',
            'participants.*.role'  => 'required|in:leader,member',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $mountain = Mountain::with('regulation')->findOrFail($validated['mountain_id']);
        $regulation = $mountain->regulation;

        if (!$regulation) {
            return response()->json(['message' => 'Regulasi gunung belum dikonfigurasi'], 422);
        }

        // Validasi: trail harus milik gunung yang dipilih
        $trail = Trail::where('mountain_id', $mountain->id)
            ->where('is_active', true)
            ->find($validated['trail_id']);

        if (!$trail) {
            return response()->json(['message' => 'Jalur tidak valid untuk gunung ini'], 422);
        }

        // Validasi: batas maksimal hari pendakian
        $startDate = Carbon::parse($validated['start_date']);
        $endDate   = Carbon::parse($validated['end_date']);
        $hikingDays = $startDate->diffInDays($endDate) + 1;

        if ($hikingDays > $regulation->max_hiking_days) {
            return response()->json([
                'message' => "Batas maksimal pendakian di gunung ini adalah {$regulation->max_hiking_days} hari",
            ], 422);
        }

        // Validasi: jumlah peserta tidak melebihi batas per akun
        $participantCount = count($validated['participants']);
        if ($participantCount > $regulation->max_participants_per_account) {
            return response()->json([
                'message' => "Maksimal {$regulation->max_participants_per_account} peserta per booking untuk gunung ini",
            ], 422);
        }

        // Validasi: guide wajib jika guide_required = true
        if ($regulation->guide_required && !$validated['guide_requested']) {
            return response()->json([
                'message' => 'Guide wajib untuk pendakian di gunung ini',
            ], 422);
        }

        // Validasi: kuota jalur per hari (cek semua hari dalam range)
        for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
            $booked = Booking::where('trail_id', $trail->id)
                ->whereIn('status', ['paid', 'active'])
                ->whereDate('start_date', '<=', $d->toDateString())
                ->whereDate('end_date', '>=', $d->toDateString())
                ->withCount('participants')
                ->get()
                ->sum('participants_count');

            $remaining = $regulation->quota_per_trail_per_day - $booked;

            if ($participantCount > $remaining) {
                return response()->json([
                    'message' => "Kuota jalur penuh untuk tanggal {$d->toDateString()} (sisa: {$remaining})",
                ], 422);
            }
        }

        // Hitung total harga (peserta × harga dasar)
        $totalPrice = $regulation->base_price * $participantCount;

        $booking = DB::transaction(function () use ($validated, $mountain, $trail, $totalPrice, $participantCount) {
            $booking = Booking::create([
                'leader_user_id'  => request()->user()->id,
                'mountain_id'     => $mountain->id,
                'trail_id'        => $trail->id,
                'start_date'      => $validated['start_date'],
                'end_date'        => $validated['end_date'],
                'guide_requested' => $validated['guide_requested'],
                'tos_accepted_at' => now(),
                'status'          => 'pending_payment',
                'total_price'     => $totalPrice,
                'notes'           => $validated['notes'] ?? null,
            ]);

            foreach ($validated['participants'] as $p) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'nik'        => $p['nik'],
                    'name'       => $p['name'],
                    'role'       => $p['role'],
                ]);
            }

            return $booking;
        });

        $booking->load(['mountain', 'trail', 'participants']);

        return response()->json([
            'message' => 'Booking berhasil dibuat, lanjutkan ke pembayaran',
            'data'    => $booking,
        ], 201);
    }

    /**
     * Batalkan booking
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::byLeader($request->user()->id)->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        if (!in_array($booking->status, ['pending_payment', 'paid'])) {
            return response()->json(['message' => 'Booking tidak dapat dibatalkan'], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking berhasil dibatalkan',
            'data'    => $booking->fresh(),
        ], 200);
    }

    /**
     * Cek ketersediaan kuota jalur untuk 30 hari ke depan
     */
    public function getAvailableDates(Request $request, $mountainId, $trailId)
    {
        $mountain = Mountain::with('regulation')->find($mountainId);

        if (!$mountain || !$mountain->regulation) {
            return response()->json(['message' => 'Gunung tidak ditemukan'], 404);
        }

        $trail = Trail::where('mountain_id', $mountainId)->find($trailId);

        if (!$trail) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $quota = $mountain->regulation->quota_per_trail_per_day;
        $dates = [];
        $today = Carbon::today();

        for ($i = 1; $i <= 30; $i++) {
            $date = $today->copy()->addDays($i);

            $booked = Booking::where('trail_id', $trailId)
                ->whereIn('status', ['paid', 'active'])
                ->whereDate('start_date', '<=', $date->toDateString())
                ->whereDate('end_date', '>=', $date->toDateString())
                ->withCount('participants')
                ->get()
                ->sum('participants_count');

            $dates[] = [
                'date'      => $date->toDateString(),
                'quota'     => $quota,
                'booked'    => $booked,
                'remaining' => max(0, $quota - $booked),
                'available' => ($quota - $booked) > 0,
            ];
        }

        return response()->json(['data' => $dates], 200);
    }
}

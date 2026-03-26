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
    public function index(Request $request)
    {
        $query = Booking::with(['mountain', 'trail', 'trailOut']) // ← tambah trailOut
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

    public function show(Request $request, $id)
    {
        $booking = Booking::with(['mountain.regulation', 'trail', 'trailOut', 'participants', 'payment']) // ← trailOut
            ->byLeader($request->user()->id)
            ->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        return response()->json(['data' => $booking], 200);
    }

    /**
     * Buat booking baru — mendukung lintas jalur.
     *
     * PERUBAHAN:
     * - Tambah field trail_out_id (opsional)
     * - Validasi trail_out_id harus satu gunung dengan trail_id
     * - Set is_cross_trail = true jika trail_out_id berbeda dari trail_id
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mountain_id'          => 'required|integer|exists:mountains,id',
            'trail_id'             => 'required|integer|exists:trails,id',
            'trail_out_id'         => 'nullable|integer|exists:trails,id', // ← BARU
            'start_date'           => 'required|date|after_or_equal:today',
            'end_date'             => 'required|date|after:start_date',
            'guide_requested'      => 'required|boolean',
            'tos_accepted'         => 'required|accepted',
            'participants'         => 'required|array|min:1',
            'participants.*.name'  => 'required|string|max:255',
            'participants.*.nik'   => 'required|string|size:16|regex:/^[0-9]{16}$/',
        ]);

        // Lintas jalur: trail_out_id ada dan berbeda dari trail_id
        $isCrossTrail = isset($validated['trail_out_id'])
            && $validated['trail_out_id'] !== $validated['trail_id'];

        if ($isCrossTrail) {
            $trailOut = Trail::findOrFail($validated['trail_out_id']);
            if ((int) $trailOut->mountain_id !== (int) $validated['mountain_id']) {
                return response()->json([
                    'message' => 'Jalur turun harus berada di gunung yang sama dengan jalur naik.',
                ], 422);
            }
        }

        $trail    = Trail::with('mountain.regulation')->findOrFail($validated['trail_id']);
        $reg      = $trail->mountain->regulation;
        $days     = (int) Carbon::parse($validated['start_date'])
                        ->diffInDays(Carbon::parse($validated['end_date'])) + 1;
        $paxCount = count($validated['participants']);

        if ($reg && $reg->max_participants_per_account && $paxCount > $reg->max_participants_per_account) {
            return response()->json([
                'message' => "Maksimal {$reg->max_participants_per_account} peserta per booking.",
            ], 422);
        }

        if ($reg && $reg->max_hiking_days && $days > $reg->max_hiking_days) {
            return response()->json([
                'message' => "Maksimal {$reg->max_hiking_days} hari pendakian.",
            ], 422);
        }

        $trailFee   = $reg ? ($reg->base_price ?? 0) : 0;
        $guidePrice = $validated['guide_requested'] && $reg
            ? ($reg->guide_price_per_day ?? 0) * $days
            : 0;
        $totalPrice = ($trailFee * $paxCount) + $guidePrice;

        $booking = DB::transaction(function () use ($validated, $request, $isCrossTrail, $totalPrice) {
            $booking = Booking::create([
                'leader_user_id'  => $request->user()->id,
                'mountain_id'     => $validated['mountain_id'],
                'trail_id'        => $validated['trail_id'],
                'trail_out_id'    => $isCrossTrail ? $validated['trail_out_id'] : null, // ← BARU
                'is_cross_trail'  => $isCrossTrail,                                      // ← BARU
                'start_date'      => $validated['start_date'],
                'end_date'        => $validated['end_date'],
                'guide_requested' => $validated['guide_requested'],
                'tos_accepted_at' => now(),
                'status'          => 'pending_payment',
                'total_price'     => $totalPrice,
            ]);

            foreach ($validated['participants'] as $idx => $pax) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'user_id'    => null,
                    'nik'        => $pax['nik'],
                    'name'       => $pax['name'],
                    'role'       => ($idx === 0) ? 'leader' : 'member',
                ]);
            }

            return $booking;
        });

        return response()->json([
            'message' => 'Booking berhasil dibuat',
            'data'    => $booking->load(['mountain', 'trail', 'trailOut', 'participants']),
        ], 201);
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::byLeader($request->user()->id)
            ->whereIn('status', ['pending_payment'])
            ->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan atau tidak dapat dibatalkan'], 404);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Booking berhasil dibatalkan'], 200);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mountain;
use App\Models\Payment;
use App\Models\QrPass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['mountain', 'trail', 'leader', 'payment'])->withCount('participants');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('mountain_id')) {
            $query->where('mountain_id', $request->mountain_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', "%{$request->search}%")
                  ->orWhereHas('leader', fn ($u) =>
                      $u->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                  );
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(20)->withQueryString();
        $mountains = Mountain::orderBy('name')->get(['id', 'name']);

        $statusCounts = Booking::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.bookings.index', compact('bookings', 'mountains', 'statusCounts'));
    }

    public function show($id)
    {
        $booking = Booking::with([
            'mountain.regulation',
            'trail',
            'leader',
            'participants.qrPass',
            'payment',
        ])->findOrFail($id);

        return view('admin.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['pending_payment', 'paid'])) {
            return back()->with('error', 'Booking dengan status "'.$booking->status.'" tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking '.$booking->booking_code.' berhasil dibatalkan.');
    }

    public function confirmPayment($id)
    {
        $booking = Booking::with(['participants', 'mountain.regulation', 'payment'])->findOrFail($id);

        if (!$booking->payment || $booking->payment->status !== 'pending') {
            return back()->with('error', 'Tidak ada pembayaran pending untuk booking ini.');
        }

        if ($booking->status !== 'pending_payment') {
            return back()->with('error', 'Booking ini sudah diproses.');
        }

        $regulation = $booking->mountain->regulation;

        DB::transaction(function () use ($booking, $regulation) {
            $booking->payment->update([
                'status'  => 'paid',
                'paid_at' => now(),
                'gateway_response' => ['note' => 'Dikonfirmasi manual oleh admin', 'confirmed_at' => now()->toIso8601String()],
            ]);

            $booking->update([
                'booking_code' => Booking::generateBookingCode(),
                'status'       => 'paid',
            ]);

            $validFrom  = Carbon::parse($booking->start_date)->startOfDay();
            $validUntil = Carbon::parse($booking->end_date)
                ->setHour($regulation->checkout_deadline_hour)
                ->setMinute(0)->setSecond(0);

            foreach ($booking->participants as $participant) {
                $status = now()->gte($validFrom) ? 'active' : 'inactive';
                QrPass::firstOrCreate(
                    ['booking_participant_id' => $participant->id],
                    [
                        'qr_token'   => QrPass::generateToken(),
                        'valid_from'  => $validFrom,
                        'valid_until' => $validUntil,
                        'status'      => $status,
                    ]
                );
            }
        });

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi. QR Pass telah diterbitkan.');
    }
}

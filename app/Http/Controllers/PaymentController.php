<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\QrPass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Inisiasi pembayaran untuk booking
     *
     * Membuat record Payment status 'pending'.
     * Di production: kirim ke Midtrans/Xendit dan return payment_url.
     * Di dummy: langsung return instruksi konfirmasi manual.
     */
    public function initiate(Request $request, $bookingId)
    {
        $booking = Booking::with('participants')
            ->byLeader($request->user()->id)
            ->find($bookingId);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        if ($booking->status !== 'pending_payment') {
            return response()->json(['message' => 'Booking ini tidak dalam status menunggu pembayaran'], 422);
        }

        if ($booking->payment) {
            return response()->json([
                'message' => 'Pembayaran sudah pernah dibuat',
                'data'    => $booking->payment,
            ], 422);
        }

        $payment = Payment::create([
            'booking_id'     => $booking->id,
            'gateway'        => 'dummy',
            'status'         => 'pending',
            'amount'         => $booking->total_price,
            'gateway_response' => [
                'note' => 'Dummy payment — gunakan endpoint /confirm untuk mensimulasikan pembayaran berhasil',
            ],
        ]);

        return response()->json([
            'message'     => 'Pembayaran berhasil diinisiasi',
            'data'        => [
                'payment_id'  => $payment->id,
                'amount'      => $payment->amount,
                'status'      => $payment->status,
                'confirm_url' => url("/api/v1/bookings/{$booking->id}/payment/confirm"),
            ],
        ], 201);
    }

    /**
     * [DUMMY] Konfirmasi pembayaran berhasil
     *
     * Mensimulasikan callback dari payment gateway.
     * Di production: endpoint ini dipanggil oleh Midtrans/Xendit webhook (tidak perlu auth).
     *
     * Setelah pembayaran dikonfirmasi:
     * 1. Update Payment → status: paid
     * 2. Generate booking_code unik
     * 3. Update Booking → status: paid
     * 4. Buat QrPass untuk setiap peserta (inactive sampai hari H)
     */
    public function confirmDummy(Request $request, $bookingId)
    {
        $booking = Booking::with(['participants', 'mountain.regulation', 'payment'])
            ->find($bookingId);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        if (!$booking->payment || $booking->payment->status !== 'pending') {
            return response()->json(['message' => 'Tidak ada pembayaran pending untuk booking ini'], 422);
        }

        if ($booking->status !== 'pending_payment') {
            return response()->json(['message' => 'Booking ini sudah diproses'], 422);
        }

        $regulation = $booking->mountain->regulation;

        DB::transaction(function () use ($booking, $regulation) {
            // 1. Tandai pembayaran berhasil
            $booking->payment->update([
                'status'  => 'paid',
                'paid_at' => now(),
                'gateway_response' => ['note' => 'Dummy payment confirmed', 'confirmed_at' => now()->toIso8601String()],
            ]);

            // 2. Generate booking_code & update status booking
            $booking->update([
                'booking_code' => Booking::generateBookingCode(),
                'status'       => 'paid',
            ]);

            // 3. Buat QrPass per peserta
            $validFrom  = Carbon::parse($booking->start_date)->startOfDay();
            $validUntil = Carbon::parse($booking->end_date)
                ->setHour($regulation->checkout_deadline_hour)
                ->setMinute(0)
                ->setSecond(0);

            foreach ($booking->participants as $participant) {
                // Status active jika hari H sudah tiba, inactive jika belum
                $status = now()->gte($validFrom) ? 'active' : 'inactive';

                QrPass::create([
                    'booking_participant_id' => $participant->id,
                    'qr_token'              => QrPass::generateToken(),
                    'valid_from'            => $validFrom,
                    'valid_until'           => $validUntil,
                    'status'                => $status,
                ]);
            }
        });

        $booking->refresh()->load(['payment', 'participants.qrPass']);

        return response()->json([
            'message'      => 'Pembayaran berhasil dikonfirmasi',
            'booking_code' => $booking->booking_code,
            'data'         => $booking,
        ], 200);
    }

    /**
     * Status pembayaran untuk sebuah booking
     */
    public function status(Request $request, $bookingId)
    {
        $booking = Booking::byLeader($request->user()->id)->find($bookingId);

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        $payment = $booking->payment;

        if (!$payment) {
            return response()->json(['message' => 'Belum ada pembayaran untuk booking ini'], 404);
        }

        return response()->json(['data' => $payment], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingParticipant;
use App\Models\QrPass;
use Illuminate\Http\Request;

class QrPassController extends Controller
{
    /**
     * Peserta klaim QR Pass dengan memasukkan booking_code
     *
     * Alur (sesuai spesifikasi):
     * 1. User login ke akun masing-masing
     * 2. Input booking_code dari leader
     * 3. Sistem verifikasi: NIK akun == salah satu NIK peserta dalam booking
     * 4. Jika cocok → terbitkan (atau tampilkan) QR Pass
     */
    public function claim(Request $request)
    {
        $validated = $request->validate([
            'booking_code' => 'required|string',
        ]);

        $user = $request->user();

        // User harus punya NIK untuk klaim QR (WNI)
        if (!$user->nik) {
            return response()->json([
                'message' => 'Akun Anda belum memiliki NIK terdaftar',
            ], 422);
        }

        $booking = Booking::with(['participants.qrPass', 'mountain.regulation'])
            ->where('booking_code', $validated['booking_code'])
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Kode booking tidak valid'], 404);
        }

        if ($booking->status !== 'paid' && $booking->status !== 'active') {
            return response()->json([
                'message' => 'Booking belum dibayar atau tidak aktif',
            ], 422);
        }

        // Cari peserta yang NIK-nya cocok dengan NIK user yang login
        $participant = $booking->participants
            ->firstWhere('nik', $user->nik);

        if (!$participant) {
            return response()->json([
                'message' => 'NIK Anda tidak terdaftar dalam booking ini',
            ], 403);
        }

        // Link user_id ke participant jika belum terhubung
        if (!$participant->user_id) {
            $participant->update(['user_id' => $user->id]);
        }

        $qrPass = $participant->qrPass;

        if (!$qrPass) {
            return response()->json([
                'message' => 'QR Pass belum diterbitkan untuk booking ini. Hubungi leader.',
            ], 404);
        }

        return response()->json([
            'message' => 'QR Pass berhasil ditemukan',
            'data'    => [
                'qr_token'    => $qrPass->qr_token,
                'valid_from'  => $qrPass->valid_from,
                'valid_until' => $qrPass->valid_until,
                'status'      => $qrPass->status,
                'participant' => [
                    'name' => $participant->name,
                    'role' => $participant->role,
                ],
                'booking' => [
                    'mountain'   => $booking->mountain->name,
                    'start_date' => $booking->start_date,
                    'end_date'   => $booking->end_date,
                ],
            ],
        ], 200);
    }

    /**
     * Tampilkan detail QR Pass milik user yang login
     */
    public function show(Request $request, $qrToken)
    {
        $qrPass = QrPass::with(['participant.booking.mountain', 'participant.booking.trail'])
            ->where('qr_token', $qrToken)
            ->first();

        if (!$qrPass) {
            return response()->json(['message' => 'QR Pass tidak ditemukan'], 404);
        }

        $user        = $request->user();
        $participant = $qrPass->participant;

        // Pendaki hanya bisa lihat QR milik sendiri; officer/admin bisa lihat semua
        if ($user->role === 'pendaki' && $participant->user_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        return response()->json([
            'data' => [
                'qr_token'    => $qrPass->qr_token,
                'valid_from'  => $qrPass->valid_from,
                'valid_until' => $qrPass->valid_until,
                'status'      => $qrPass->status,
                'is_valid'    => $qrPass->isValid(),
                'participant' => [
                    'name' => $participant->name,
                    'role' => $participant->role,
                    'nik'  => $participant->masked_nik,
                ],
                'booking' => [
                    'booking_code' => $participant->booking->booking_code,
                    'mountain'     => $participant->booking->mountain->name,
                    'trail'        => $participant->booking->trail->name,
                    'start_date'   => $participant->booking->start_date,
                    'end_date'     => $participant->booking->end_date,
                ],
            ],
        ], 200);
    }

    /**
     * Daftar semua QR Pass milik user yang login
     */
    public function myPasses(Request $request)
    {
        $user = $request->user();

        $passes = QrPass::with(['participant.booking.mountain'])
            ->whereHas('participant', fn ($q) => $q->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($pass) => [
                'qr_token'    => $pass->qr_token,
                'valid_from'  => $pass->valid_from,
                'valid_until' => $pass->valid_until,
                'status'      => $pass->status,
                'is_valid'    => $pass->isValid(),
                'mountain'    => $pass->participant->booking->mountain->name,
                'start_date'  => $pass->participant->booking->start_date,
            ]);

        return response()->json(['data' => $passes], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\QrPass;
use App\Models\TrailCheckpoint;
use App\Models\TrekkingLog;
use Illuminate\Http\Request;

class TrekkingLogController extends Controller
{
    /**
     * Scan QR di pos (oleh pendaki atau petugas pos)
     *
     * Alur:
     * - Terima qr_token dari QR Code pendaki
     * - Validasi QrPass aktif dan belum expired
     * - Validasi checkpoint milik jalur yang sama dengan booking peserta
     * - Catat TrekkingLog dengan direction (up/down)
     * - Aktifkan QrPass jika ini scan pertama (gate_in/up)
     */
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'qr_token'            => 'required|string|exists:qr_passes,qr_token',
            'trail_checkpoint_id' => 'required|integer|exists:trail_checkpoints,id',
            'direction'           => 'required|in:up,down',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'device_info'         => 'nullable|array',
        ]);

        $qrPass = QrPass::with('participant.booking')->where('qr_token', $validated['qr_token'])->first();

        // QR hanya bisa dipakai dalam rentang tanggal yang valid
        if (!$qrPass->isValid()) {
            return response()->json([
                'message' => 'QR Pass tidak aktif atau sudah kadaluarsa',
            ], 422);
        }

        $booking    = $qrPass->participant->booking;
        $checkpoint = TrailCheckpoint::find($validated['trail_checkpoint_id']);

        // Checkpoint harus milik jalur yang sama dengan booking
        if ($checkpoint->trail_id !== $booking->trail_id) {
            return response()->json([
                'message' => 'Checkpoint tidak sesuai dengan jalur pendakian',
            ], 422);
        }

        // Aktifkan booking jika ini scan pertama (gate_in, direction up)
        if ($checkpoint->type === 'gate_in' && $validated['direction'] === 'up'
            && $booking->status === 'paid') {
            $booking->update(['status' => 'active']);
        }

        $log = TrekkingLog::create([
            'qr_pass_id'          => $qrPass->id,
            'trail_checkpoint_id' => $checkpoint->id,
            'direction'           => $validated['direction'],
            'scanned_at'          => now(),
            'scanned_by_user_id'  => $request->user()->id,
            'latitude'            => $validated['latitude'] ?? null,
            'longitude'           => $validated['longitude'] ?? null,
            'device_info'         => $validated['device_info'] ?? null,
        ]);

        // Tandai booking selesai jika scan gate_out direction down
        if ($checkpoint->type === 'gate_out' && $validated['direction'] === 'down') {
            $booking->update(['status' => 'completed']);
            $qrPass->update(['status' => 'used']);
        }

        $log->load('checkpoint');

        return response()->json([
            'message' => 'Scan berhasil dicatat',
            'data'    => $log,
        ], 201);
    }

    /**
     * Riwayat log trekking untuk satu QR Pass
     */
    public function history(Request $request, $qrToken)
    {
        $qrPass = QrPass::with('participant.booking')
            ->where('qr_token', $qrToken)
            ->first();

        if (!$qrPass) {
            return response()->json(['message' => 'QR Pass tidak ditemukan'], 404);
        }

        // Hanya pemilik QR atau petugas/admin yang bisa melihat
        $user = $request->user();
        $participantUserId = $qrPass->participant->user_id;

        if ($user->role === 'pendaki' && $user->id !== $participantUserId) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $logs = $qrPass->trekkingLogs()
            ->with('checkpoint')
            ->orderBy('scanned_at')
            ->get();

        return response()->json(['data' => $logs], 200);
    }

    /**
     * Detail satu log trekking
     */
    public function show(Request $request, $id)
    {
        $log = TrekkingLog::with(['checkpoint', 'qrPass.participant', 'scannedBy'])
            ->find($id);

        if (!$log) {
            return response()->json(['message' => 'Log tidak ditemukan'], 404);
        }

        return response()->json(['data' => $log], 200);
    }
}

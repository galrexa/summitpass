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
     * PERUBAHAN LINTAS JALUR:
     * - Validasi checkpoint tidak lagi hanya cek trail_id === booking.trail_id
     *   melainkan menggunakan Booking::isCheckpointValid() yang mendukung
     *   multi-trail + shared_checkpoint_group.
     * - Penyelesaian booking (status = completed) kini cek gate_out
     *   pada effectiveTrailOut(), bukan trail_id saja.
     * - Anomali "checkpoint di luar rute" di-flag tapi tetap dicatat
     *   (tidak di-reject) agar tidak memblokir pendaki yang berubah rute
     *   karena darurat.
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

        $qrPass = QrPass::with('participant.booking.trail', 'participant.booking.trailOut')
            ->where('qr_token', $validated['qr_token'])
            ->first();

        if (!$qrPass->isValid()) {
            return response()->json([
                'message' => 'QR Pass tidak aktif atau sudah kadaluarsa',
            ], 422);
        }

        $booking    = $qrPass->participant->booking;
        $checkpoint = TrailCheckpoint::find($validated['trail_checkpoint_id']);
        $direction  = $validated['direction'];

        // ── Validasi checkpoint vs rute booking ───────────────────────────
        // Untuk lintas jalur: cek via Booking::isCheckpointValid()
        // yang mempertimbangkan trail_id, trail_out_id, dan shared_checkpoint_group.
        $anomalyFlag   = false;
        $anomalyReason = null;

        if (!$booking->isCheckpointValid($checkpoint)) {
            // Pos benar-benar di luar rute yang dideklarasikan.
            // Tetap catat sebagai anomali (tidak di-reject) agar pendaki
            // yang ganti rute karena cuaca/darurat tetap terekam.
            $anomalyFlag   = true;
            $anomalyReason = 'Checkpoint tidak termasuk dalam rute yang dideklarasikan. '
                . "Trail booking: {$booking->trail->name}"
                . ($booking->is_cross_trail ? " (naik) / {$booking->effectiveTrailOut()->name} (turun)" : "")
                . ". Checkpoint: {$checkpoint->name} (trail_id={$checkpoint->trail_id}).";
        }

        // ── Aktifkan booking jika scan pertama di gate_in arah naik ───────
        if ($checkpoint->type === 'gate_in' && $direction === 'up'
            && $booking->status === 'paid') {
            $booking->update(['status' => 'active']);
            $qrPass->update(['status' => 'active']);
        }

        // ── Catat log ─────────────────────────────────────────────────────
        $log = TrekkingLog::create([
            'qr_pass_id'          => $qrPass->id,
            'trail_checkpoint_id' => $checkpoint->id,
            'direction'           => $direction,
            'scanned_at'          => now(),
            'scanned_by_user_id'  => $request->user()->id,
            'latitude'            => $validated['latitude'] ?? null,
            'longitude'           => $validated['longitude'] ?? null,
            'device_info'         => $validated['device_info'] ?? null,
            'anomaly_flag'        => $anomalyFlag,
            'anomaly_reason'      => $anomalyReason,
        ]);

        // ── Tandai SELESAI jika scan gate_out jalur turun arah down ───────
        // KUNCI PERUBAHAN: cek effectiveTrailOut(), bukan trail_id saja.
        // Sehingga pendaki lintas jalur yang checkout di Senaru (trail_out)
        // tetap dianggap selesai meski trail booking utama adalah Sembalun.
        if ($checkpoint->type === 'gate_out'
            && $direction === 'down'
            && $checkpoint->trail_id === $booking->effectiveTrailOut()->id) {
            $booking->update(['status' => 'completed']);
            $qrPass->update(['status' => 'used']);
        }

        $log->load('checkpoint');

        return response()->json([
            'message'       => $anomalyFlag
                ? 'Scan dicatat dengan flag anomali — checkpoint di luar rute deklarasi.'
                : 'Scan berhasil dicatat',
            'anomaly'       => $anomalyFlag,
            'anomaly_reason'=> $anomalyReason,
            'data'          => $log,
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

        $user              = $request->user();
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

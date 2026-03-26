<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrPass;
use App\Models\TrailCheckpoint;
use App\Models\TrekkingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimulateScanController extends Controller
{
    public function index()
    {
        return view('admin.simulate.scan');
    }

    /**
     * Resolve QR token — kembalikan booking + checkpoints SEMUA jalur yang valid.
     *
     * PERUBAHAN LINTAS JALUR:
     * Untuk booking lintas jalur, checkpoints yang ditampilkan mencakup
     * checkpoints dari trail_id (naik) DAN trail_out_id (turun), bukan
     * hanya satu jalur.
     */
    public function resolve(Request $request)
    {
        $token = trim($request->input('token', ''));

        if (!$token) {
            return response()->json(['error' => 'Token tidak boleh kosong.'], 422);
        }

        $qrPass = QrPass::with([
            'participant.booking.mountain.regulation',
            'participant.booking.trail.checkpoints',
            'participant.booking.trailOut.checkpoints', // ← BARU
            'participant.booking.participants',
        ])->where('qr_token', $token)->first();

        if (!$qrPass) {
            return response()->json(['error' => 'QR token tidak ditemukan.'], 404);
        }

        $booking = $qrPass->participant->booking;
        $reg     = $booking->mountain->regulation;
        $days    = $booking->start_date->diffInDays($booking->end_date) + 1;
        $paxCount = $booking->participants->count();

        // Hitung rincian biaya
        $trailFee      = $reg ? ($reg->base_price ?? 0) : 0;
        $crossTrailFee = 0; // kolom cross_trail_extra_fee belum ada di mountain_regulations
        $guideTotal    = ($booking->guide_requested && $reg)
            ? ($reg->guide_price_per_day ?? 0) * $days
            : 0;

        // Kumpulkan semua checkpoint dari trail naik + trail turun (jika lintas jalur)
        $trailInCheckpoints  = $booking->trail->checkpoints->sortBy('order_seq');
        $trailOutCheckpoints = $booking->is_cross_trail && $booking->trailOut
            ? $booking->trailOut->checkpoints->sortBy('order_seq')
            : collect();

        // Gabung, deduplicate berdasarkan id
        $allCheckpoints = $trailInCheckpoints->concat($trailOutCheckpoints)->unique('id');

        // Scan logs existing
        $scannedLogs = TrekkingLog::where('qr_pass_id', $qrPass->id)
            ->get(['trail_checkpoint_id', 'direction']);

        $scannedUp   = $scannedLogs->where('direction', 'up')->pluck('trail_checkpoint_id')->toArray();
        $scannedDown = $scannedLogs->where('direction', 'down')->pluck('trail_checkpoint_id')->toArray();

        return response()->json([
            'qr_pass_id'   => $qrPass->id,
            'status'       => $qrPass->status,
            'valid_from'   => $qrPass->valid_from?->format('d M Y'),
            'valid_until'  => $qrPass->valid_until?->format('d M Y'),
            'participant'  => [
                'name' => $qrPass->participant->name,
                'role' => $qrPass->participant->role,
            ],
            'booking' => [
                'code'           => $booking->booking_code,
                'mountain'       => $booking->mountain->name,
                'trail_in'       => $booking->trail->name,
                'trail_out'      => $booking->effectiveTrailOut()->name,
                'is_cross_trail' => $booking->is_cross_trail,
                'start_date'     => $booking->start_date->format('d M Y'),
                'end_date'       => $booking->end_date->format('d M Y'),
                'status'         => $booking->status,
                'days'           => $days,
                'pax_count'      => $paxCount,
                'guide_requested'=> $booking->guide_requested,
                'total_price'    => (float) $booking->total_price,
                'fee_breakdown'  => [
                    'base_price' => (float) $trailFee,
                    'cross_trail_fee'      => (float) $crossTrailFee,
                    'guide_total'          => (float) $guideTotal,
                    'guide_price_per_day'  => $reg ? (float) ($reg->guide_price_per_day ?? 0) : 0,
                ],
            ],
            'checkpoints' => $allCheckpoints->map(fn($cp) => [
                'id'          => $cp->id,
                'name'        => $cp->name,
                'type'        => $cp->type,
                'order'       => $cp->order_seq,
                'altitude'    => $cp->altitude,
                'trail_id'    => $cp->trail_id,            // ← BARU: untuk UI label jalur
                'trail_role'  => $this->checkpointTrailRole($cp, $booking), // ← BARU: 'in'|'out'|'shared'
                'scanned_up'   => in_array($cp->id, $scannedUp),
                'scanned_down' => in_array($cp->id, $scannedDown),
            ])->values(),
        ]);
    }

    /**
     * Tandai apakah checkpoint ini bagian dari jalur naik, turun, atau keduanya.
     */
    private function checkpointTrailRole(TrailCheckpoint $cp, $booking): string
    {
        $isInTrail  = $cp->trail_id === $booking->trail_id;
        $isOutTrail = $booking->is_cross_trail && $cp->trail_id === $booking->trail_out_id;

        if ($isInTrail && $isOutTrail) return 'shared';
        if ($isInTrail) return 'in';
        if ($isOutTrail) return 'out';

        // Cek via shared_checkpoint_group
        if ($cp->shared_checkpoint_group) return 'shared';

        return 'unknown';
    }

    /**
     * Record scan — mendukung lintas jalur.
     *
     * PERUBAHAN:
     * - Gunakan Booking::isCheckpointValid() untuk validasi.
     * - Penyelesaian booking cek effectiveTrailOut().
     */
    public function record(Request $request)
    {
        $request->validate([
            'qr_pass_id'          => 'required|exists:qr_passes,id',
            'trail_checkpoint_id' => 'required|exists:trail_checkpoints,id',
            'direction'           => 'required|in:up,down',
        ]);

        $qrPass     = QrPass::with('participant.booking.trail', 'participant.booking.trailOut')
            ->findOrFail($request->qr_pass_id);
        $checkpoint = TrailCheckpoint::findOrFail($request->trail_checkpoint_id);
        $booking    = $qrPass->participant->booking;
        $direction  = $request->direction;

        // Deteksi anomali checkpoint di luar rute
        $anomalyFlag   = false;
        $anomalyReason = null;

        if (!$booking->isCheckpointValid($checkpoint)) {
            $anomalyFlag   = true;
            $anomalyReason = "Checkpoint '{$checkpoint->name}' di luar rute booking."
                . " Rute: {$booking->trail->name}"
                . ($booking->is_cross_trail ? " → {$booking->effectiveTrailOut()->name}" : "");
        }

        TrekkingLog::create([
            'qr_pass_id'          => $qrPass->id,
            'trail_checkpoint_id' => $checkpoint->id,
            'direction'           => $direction,
            'scanned_at'          => now(),
            'scanned_by_user_id'  => Auth::id(),
            'anomaly_flag'        => $anomalyFlag,
            'anomaly_reason'      => $anomalyReason,
        ]);

        // Aktifkan QR & booking di gate_in arah naik
        if ($qrPass->status === 'inactive') {
            $qrPass->update(['status' => 'active']);
        }
        if ($checkpoint->type === 'gate_in' && $direction === 'up' && $booking->status === 'paid') {
            $booking->update(['status' => 'active']);
        }

        // SELESAI: cek gate_out pada effectiveTrailOut — bukan trail naik
        if ($checkpoint->type === 'gate_out'
            && $direction === 'down'
            && $checkpoint->trail_id === $booking->effectiveTrailOut()->id) {
            $qrPass->update(['status' => 'used']);
            $booking->update(['status' => 'completed']);
        }

        return response()->json([
            'success'         => true,
            'message'         => $anomalyFlag
                ? 'Scan dicatat dengan flag anomali.'
                : 'Scan berhasil dicatat.',
            'anomaly'         => $anomalyFlag,
            'anomaly_reason'  => $anomalyReason,
            'checkpoint'      => $checkpoint->name,
            'checkpoint_type' => $checkpoint->type,
            'direction'       => $direction,
            'trail_role'      => $this->checkpointTrailRole($checkpoint, $booking),
            'scanned_at'      => now()->format('d M Y, H:i:s'),
            'booking_status'  => $booking->fresh()->status,
            'qr_status'       => $qrPass->fresh()->status,
        ]);
    }
}

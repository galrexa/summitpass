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
     * Resolve a QR token — return booking + trail checkpoints as JSON.
     */
    public function resolve(Request $request)
    {
        $token = trim($request->input('token', ''));

        if (!$token) {
            return response()->json(['error' => 'Token tidak boleh kosong.'], 422);
        }

        $qrPass = QrPass::with([
            'participant.booking.mountain',
            'participant.booking.trail.checkpoints',
            'participant',
        ])->where('qr_token', $token)->first();

        if (!$qrPass) {
            return response()->json(['error' => 'QR token tidak ditemukan.'], 404);
        }

        $booking     = $qrPass->participant->booking;
        $checkpoints = $booking->trail->checkpoints->sortBy('order_seq');

        // Track which checkpoints have been scanned, per direction
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
                'code'       => $booking->booking_code,
                'mountain'   => $booking->mountain->name,
                'trail'      => $booking->trail->name,
                'start_date' => $booking->start_date->format('d M Y'),
                'end_date'   => $booking->end_date->format('d M Y'),
                'status'     => $booking->status,
            ],
            'checkpoints' => $checkpoints->map(fn($cp) => [
                'id'          => $cp->id,
                'name'        => $cp->name,
                'type'        => $cp->type,
                'order'       => $cp->order_seq,
                'altitude'    => $cp->altitude,
                'scanned_up'   => in_array($cp->id, $scannedUp),
                'scanned_down' => in_array($cp->id, $scannedDown),
            ])->values(),
        ]);
    }

    /**
     * Record a scan — create a TrekkingLog entry.
     */
    public function record(Request $request)
    {
        $request->validate([
            'qr_pass_id'          => 'required|exists:qr_passes,id',
            'trail_checkpoint_id' => 'required|exists:trail_checkpoints,id',
            'direction'           => 'required|in:up,down',
        ]);

        $qrPass     = QrPass::with('participant.booking')->findOrFail($request->qr_pass_id);
        $checkpoint = TrailCheckpoint::findOrFail($request->trail_checkpoint_id);
        $booking    = $qrPass->participant->booking;
        $direction  = $request->direction;

        TrekkingLog::create([
            'qr_pass_id'          => $qrPass->id,
            'trail_checkpoint_id' => $checkpoint->id,
            'direction'           => $direction,
            'scanned_at'          => now(),
            'scanned_by_user_id'  => Auth::id(),
        ]);

        // Scan gate_in naik → aktifkan QR pass & booking
        if ($qrPass->status === 'inactive') {
            $qrPass->update(['status' => 'active']);
        }
        if ($checkpoint->type === 'gate_in' && $direction === 'up' && $booking->status === 'paid') {
            $booking->update(['status' => 'active']);
        }

        // Scan gate_out turun → selesaikan booking & QR pass
        if ($checkpoint->type === 'gate_out' && $direction === 'down') {
            $qrPass->update(['status' => 'used']);
            $booking->update(['status' => 'completed']);
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Scan berhasil dicatat.',
            'checkpoint'     => $checkpoint->name,
            'checkpoint_type'=> $checkpoint->type,
            'direction'      => $direction,
            'scanned_at'     => now()->format('d M Y, H:i:s'),
            'booking_status' => $booking->fresh()->status,
            'qr_status'      => $qrPass->fresh()->status,
        ]);
    }
}

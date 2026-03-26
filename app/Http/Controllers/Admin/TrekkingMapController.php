<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\Trail;
use App\Models\TrailCheckpoint;
use App\Models\TrekkingLog;
use Illuminate\Http\Request;

class TrekkingMapController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'pengelola_tn') {
            $mountain      = $user->managedMountain; // hasOne via pengelola_id
            $trails        = $mountain
                ? Trail::where('mountain_id', $mountain->id)
                    ->where('is_active', true)
                    ->orderBy('route_order')
                    ->get(['id', 'name'])
                : collect();
            $pengelolaMode = true;

            return view('admin.trekking-map.index', compact('mountain', 'trails', 'pengelolaMode'));
        }

        // Admin: tetap kirim semua gunung
        $mountains     = Mountain::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        $pengelolaMode = false;

        return view('admin.trekking-map.index', compact('mountains', 'pengelolaMode'));
    }

    /**
     * JSON endpoint: checkpoints + trekking logs untuk satu atau banyak trail.
     * Mendukung ?trail_id=1 (single) atau ?trail_ids[]=1&trail_ids[]=2 (multi).
     */
    public function data(Request $request)
    {
        $trailIds = $request->query('trail_ids')
            ? array_filter((array) $request->query('trail_ids'))
            : ($request->query('trail_id') ? [$request->query('trail_id')] : []);

        if (empty($trailIds)) {
            return response()->json(['trails' => []]);
        }

        $trailsData = [];

        foreach ($trailIds as $trailId) {
            $trail = Trail::find($trailId, ['id', 'name']);
            if (!$trail) continue;

            $checkpoints = TrailCheckpoint::where('trail_id', $trailId)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('order_seq')
                ->get(['id', 'name', 'type', 'order_seq', 'latitude', 'longitude', 'altitude']);

            $checkpointIds    = $checkpoints->pluck('id');
            $logs             = TrekkingLog::whereIn('trail_checkpoint_id', $checkpointIds)
                ->with(['qrPass.participant:id,name'])
                ->orderBy('scanned_at', 'desc')
                ->get(['id', 'trail_checkpoint_id', 'qr_pass_id', 'direction', 'scanned_at', 'anomaly_flag']);
            $logsByCheckpoint = $logs->groupBy('trail_checkpoint_id');

            $trailsData[] = [
                'trail_id'    => (int) $trailId,
                'trail_name'  => $trail->name,
                'checkpoints' => $checkpoints->map(function ($cp) use ($logsByCheckpoint) {
                    return [
                        'id'        => $cp->id,
                        'name'      => $cp->name,
                        'type'      => $cp->type,
                        'order_seq' => $cp->order_seq,
                        'lat'       => (float) $cp->latitude,
                        'lng'       => (float) $cp->longitude,
                        'altitude'  => $cp->altitude,
                        'logs'      => $logsByCheckpoint->get($cp->id, collect())->map(fn($log) => [
                            'name'       => $log->qrPass?->participant?->name ?? 'Pendaki',
                            'direction'  => $log->direction,
                            'scanned_at' => $log->scanned_at?->format('d M Y, H:i'),
                            'anomaly'    => (bool) $log->anomaly_flag,
                        ])->values(),
                    ];
                })->values(),
            ];
        }

        return response()->json(['trails' => $trailsData]);
    }

    /**
     * JSON endpoint: trails for a mountain (untuk admin dropdown)
     */
    public function trails(int $mountainId)
    {
        $trails = Trail::where('mountain_id', $mountainId)
            ->where('is_active', true)
            ->orderBy('route_order')
            ->get(['id', 'name']);

        return response()->json(['trails' => $trails]);
    }
}

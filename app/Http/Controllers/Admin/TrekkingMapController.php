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
        $mountains = Mountain::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.trekking-map.index', compact('mountains'));
    }

    /**
     * JSON endpoint: checkpoints + trekking logs for a trail
     */
    public function data(Request $request)
    {
        $trailId = $request->query('trail_id');

        if (!$trailId) {
            return response()->json(['checkpoints' => []]);
        }

        $checkpoints = TrailCheckpoint::where('trail_id', $trailId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('order_seq')
            ->get(['id', 'name', 'type', 'order_seq', 'latitude', 'longitude', 'altitude']);

        $checkpointIds = $checkpoints->pluck('id');

        // Ambil trekking logs beserta nama pendaki, waktu scan, dan arah
        $logs = TrekkingLog::whereIn('trail_checkpoint_id', $checkpointIds)
            ->with([
                'qrPass.participant:id,name',
            ])
            ->orderBy('scanned_at', 'desc')
            ->get(['id', 'trail_checkpoint_id', 'qr_pass_id', 'direction', 'scanned_at', 'anomaly_flag']);

        // Kelompokkan log per checkpoint
        $logsByCheckpoint = $logs->groupBy('trail_checkpoint_id');

        $result = $checkpoints->map(function ($cp) use ($logsByCheckpoint) {
            $cpLogs = $logsByCheckpoint->get($cp->id, collect());

            return [
                'id'        => $cp->id,
                'name'      => $cp->name,
                'type'      => $cp->type,
                'order_seq' => $cp->order_seq,
                'lat'       => (float) $cp->latitude,
                'lng'       => (float) $cp->longitude,
                'altitude'  => $cp->altitude,
                'logs'      => $cpLogs->map(function ($log) {
                    $name = $log->qrPass?->participant?->name ?? 'Pendaki';

                    return [
                        'name'         => $name,
                        'direction'    => $log->direction,
                        'scanned_at'   => $log->scanned_at?->format('d M Y, H:i'),
                        'anomaly'      => (bool) $log->anomaly_flag,
                    ];
                })->values(),
            ];
        });

        return response()->json(['checkpoints' => $result]);
    }

    /**
     * JSON endpoint: trails for a mountain (for dropdown)
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

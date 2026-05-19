<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\QrPass;
use App\Models\TrailCheckpoint;
use App\Models\TrekkingLog;

class MonitoringWebController extends Controller
{
    public function index()
    {
        $activePasses = QrPass::with([
                'participant.booking.mountain',
                'participant.booking.trail',
                'trekkingLogs' => fn ($q) => $q->with('checkpoint')->latest('scanned_at')->limit(1),
            ])
            ->where('status', 'active')
            ->orderBy('valid_from')
            ->get();

        $anomalyLogs = TrekkingLog::with([
                'qrPass.participant.booking.mountain',
                'qrPass.participant.booking.trail',
                'qrPass.participant',
                'checkpoint',
            ])
            ->where('anomaly_flag', true)
            ->latest('scanned_at')
            ->limit(30)
            ->get();

        $expiredPasses = QrPass::with([
                'participant.booking.mountain',
                'participant.booking.trail',
                'participant',
            ])
            ->where('status', 'expired')
            ->orderByDesc('valid_until')
            ->limit(20)
            ->get();

        $stats = [
            'active_now'     => $activePasses->count(),
            'anomalies'      => $anomalyLogs->count(),
            'expired'        => QrPass::where('status', 'expired')->count(),
            'total_today'    => Booking::whereIn('status', ['paid', 'active'])
                                    ->whereDate('start_date', '<=', today())
                                    ->whereDate('end_date', '>=', today())
                                    ->count(),
            'active_hikers'  => $activePasses->filter(fn($p) => ($p->participant->role ?? 'hiker') === 'hiker')->count(),
            'active_guides'  => $activePasses->filter(fn($p) => $p->participant->role === 'guide')->count(),
            'active_porters' => $activePasses->filter(fn($p) => $p->participant->role === 'porter')->count(),
        ];

        $checkpoints = TrailCheckpoint::with([
                'trekkingLogs' => fn ($q) => $q->latest('scanned_at')->limit(1)->with('qrPass.participant'),
            ])
            ->whereHas('trail.mountain', fn ($q) => $q->where('pengelola_id', auth()->id()))
            ->orderBy('order_seq')
            ->get();

        return view('admin.monitoring.index', compact('activePasses', 'anomalyLogs', 'expiredPasses', 'stats', 'checkpoints'));
    }
}

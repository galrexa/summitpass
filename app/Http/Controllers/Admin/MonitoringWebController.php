<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrPass;
use App\Models\TrekkingLog;
use App\Models\Booking;

class MonitoringWebController extends Controller
{
    public function index()
    {
        // Pendaki yang sedang aktif di jalur (QrPass status active)
        $activePasses = QrPass::with([
                'participant.booking.mountain',
                'participant.booking.trail',
                'trekkingLogs' => fn ($q) => $q->with('checkpoint')->latest('scanned_at')->limit(1),
            ])
            ->where('status', 'active')
            ->orderBy('valid_from')
            ->get();

        // Alert anomali: trekking log yang ada anomaly_flag = true
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

        // QrPass yang expired (belum checkout)
        $expiredPasses = QrPass::with([
                'participant.booking.mountain',
                'participant.booking.trail',
                'participant',
            ])
            ->where('status', 'expired')
            ->orderByDesc('valid_until')
            ->limit(20)
            ->get();

        // Summary stats
        $stats = [
            'active_now'     => $activePasses->count(),
            'anomalies'      => $anomalyLogs->count(),
            'expired'        => QrPass::where('status', 'expired')->count(),
            'total_today'    => Booking::whereIn('status', ['paid', 'active'])
                                    ->whereDate('start_date', '<=', today())
                                    ->whereDate('end_date', '>=', today())
                                    ->count(),
        ];

        return view('admin.monitoring.index', compact('activePasses', 'anomalyLogs', 'expiredPasses', 'stats'));
    }
}

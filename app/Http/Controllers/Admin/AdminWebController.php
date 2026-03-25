<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Mountain;
use App\Models\Payment;
use App\Models\TrekkingLog;
use App\Models\User;

class AdminWebController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_mountains'      => Mountain::where('is_active', true)->count(),
            'pending_bookings'     => Booking::where('status', 'pending_payment')->count(),
            'active_trekkers'      => TrekkingLog::where('direction', 'up')
                                        ->whereNotIn('qr_pass_id', TrekkingLog::where('direction', 'down')->pluck('qr_pass_id'))
                                        ->count(),
            'revenue_today'        => Payment::where('status', 'paid')
                                        ->whereDate('paid_at', today())
                                        ->sum('amount'),
        ];

        $recentBookings = Booking::with(['mountain', 'trail', 'leader'])
            ->latest()
            ->limit(5)
            ->get();

        $anomalyAlerts = TrekkingLog::with(['qrPass.bookingParticipant.booking.mountain', 'checkpoint'])
            ->where('anomaly_flag', true)
            ->latest('scanned_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'anomalyAlerts'));
    }
}

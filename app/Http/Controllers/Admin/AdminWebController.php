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
        $user = auth()->user();

        if ($user->role === 'pengelola_tn') {
            return $this->pengelolaDashboard($user);
        }

        return $this->adminDashboard();
    }

    private function adminDashboard()
    {
        $stats = [
            'total_mountains'  => Mountain::where('is_active', true)->count(),
            'total_pengelola'  => User::whereHas('userRole', fn($q) => $q->where('name', 'pengelola_tn'))->count(),
            'pending_bookings' => Booking::where('status', 'pending_payment')->count(),
            'active_trekkers'  => TrekkingLog::where('direction', 'up')
                                    ->whereNotIn('qr_pass_id', TrekkingLog::where('direction', 'down')->pluck('qr_pass_id'))
                                    ->count(),
            'revenue_today'    => Payment::where('status', 'paid')
                                    ->whereDate('paid_at', today())
                                    ->sum('amount'),
        ];

        $recentBookings = Booking::with(['mountain', 'trail', 'leader'])
            ->latest()->limit(5)->get();

        $anomalyAlerts = TrekkingLog::with(['qrPass.participant.booking.mountain', 'checkpoint'])
            ->where('anomaly_flag', true)
            ->latest('scanned_at')->limit(5)->get();

        $mountains = Mountain::with('pengelola')->withCount('bookings')->orderBy('name')->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'anomalyAlerts', 'mountains'));
    }

    private function pengelolaDashboard($user)
    {
        $mountain = $user->managedMountain;

        if (!$mountain) {
            return view('admin.dashboard-pengelola', ['mountain' => null, 'stats' => [], 'recentBookings' => collect(), 'anomalyAlerts' => collect()]);
        }

        $stats = [
            'pending_bookings' => Booking::where('mountain_id', $mountain->id)
                                    ->where('status', 'pending_payment')->count(),
            'active_trekkers'  => TrekkingLog::whereHas('qrPass.participant.booking', fn($q) => $q->where('mountain_id', $mountain->id))
                                    ->where('direction', 'up')
                                    ->whereNotIn('qr_pass_id', TrekkingLog::where('direction', 'down')->pluck('qr_pass_id'))
                                    ->count(),
            'revenue_today'    => Payment::where('status', 'paid')
                                    ->whereDate('paid_at', today())
                                    ->whereHas('booking', fn($q) => $q->where('mountain_id', $mountain->id))
                                    ->sum('amount'),
            'total_bookings'   => Booking::where('mountain_id', $mountain->id)->count(),
        ];

        $recentBookings = Booking::with(['trail', 'leader'])
            ->where('mountain_id', $mountain->id)
            ->latest()->limit(5)->get();

        $anomalyAlerts = TrekkingLog::with(['qrPass.participant.booking.mountain', 'checkpoint'])
            ->where('anomaly_flag', true)
            ->whereHas('qrPass.participant.booking', fn($q) => $q->where('mountain_id', $mountain->id))
            ->latest('scanned_at')->limit(5)->get();

        return view('admin.dashboard-pengelola', compact('mountain', 'stats', 'recentBookings', 'anomalyAlerts'));
    }
}

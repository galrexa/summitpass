<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.mountain', 'booking.trail', 'booking.leader'])
            ->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('mountain_id')) {
            $query->whereHas('booking', fn ($q) => $q->where('mountain_id', $request->mountain_id));
        }

        if ($request->filled('search')) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_code', 'like', "%{$request->search}%")
                  ->orWhereHas('leader', fn ($u) =>
                      $u->where('name', 'like', "%{$request->search}%")
                  );
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(20)->withQueryString();
        $mountains = Mountain::orderBy('name')->get(['id', 'name']);

        $summary = [
            'total_paid'    => Payment::where('status', 'paid')->sum('amount'),
            'total_pending' => Payment::where('status', 'pending')->sum('amount'),
            'count_paid'    => Payment::where('status', 'paid')->count(),
            'count_pending' => Payment::where('status', 'pending')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'mountains', 'summary'));
    }
}

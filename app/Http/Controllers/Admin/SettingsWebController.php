<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsWebController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('key')->get();
        $lastAnomalyRun = Cache::get('anomaly:last_run');

        return view('admin.settings.index', compact('settings', 'lastAnomalyRun'));
    }

    public function update(Request $request, string $key)
    {
        $rules = match ($key) {
            'anomaly_check_interval_minutes' => ['value' => 'required|integer|min:5|max:1440'],
            'anomaly_stall_threshold_hours'  => ['value' => 'required|integer|min:1|max:72'],
            'anomaly_checkout_grace_minutes' => ['value' => 'required|integer|min:0|max:120'],
            default                          => ['value' => 'required|string|max:500'],
        };

        $validated = $request->validate($rules);
        SystemSetting::set($key, $validated['value']);

        return back()->with('success', "Setting '{$key}' berhasil disimpan.");
    }

    public function runAnomalyCheck()
    {
        Artisan::call('summitpass:check-anomalies');
        $output = Artisan::output();

        return back()
            ->with('success', 'Anomaly check selesai.')
            ->with('anomaly_output', trim($output));
    }
}

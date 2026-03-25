<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Daftar semua settings (admin only)
     */
    public function index()
    {
        $settings = SystemSetting::orderBy('key')->get();

        return response()->json(['data' => $settings], 200);
    }

    /**
     * Update satu setting berdasarkan key (admin only)
     *
     * Nilai yang bisa diupdate admin:
     *   - anomaly_check_interval_minutes  (integer, min: 5)
     *   - anomaly_stall_threshold_hours   (integer, min: 1)
     *   - anomaly_checkout_grace_minutes  (integer, min: 0)
     */
    public function update(Request $request, string $key)
    {
        $setting = SystemSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['message' => 'Setting tidak ditemukan'], 404);
        }

        $rules = match ($key) {
            'anomaly_check_interval_minutes' => ['value' => 'required|integer|min:5|max:1440'],
            'anomaly_stall_threshold_hours'  => ['value' => 'required|integer|min:1|max:72'],
            'anomaly_checkout_grace_minutes' => ['value' => 'required|integer|min:0|max:120'],
            default                          => ['value' => 'required|string|max:500'],
        };

        $validated = $request->validate($rules);

        SystemSetting::set($key, $validated['value']);

        return response()->json([
            'message' => "Setting '{$key}' berhasil diperbarui",
            'data'    => SystemSetting::where('key', $key)->first(),
        ], 200);
    }

    /**
     * Jalankan cek anomali secara manual (admin only)
     * Berguna untuk testing tanpa menunggu scheduler.
     */
    public function runAnomalyCheck()
    {
        $exitCode = \Artisan::call('anomaly:check', ['--force' => true]);
        $output   = \Artisan::output();

        return response()->json([
            'message'   => 'Anomaly check selesai',
            'exit_code' => $exitCode,
            'output'    => $output,
        ], 200);
    }
}

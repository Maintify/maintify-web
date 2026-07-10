<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman formulir pengaturan global.
     */
    public function index(): View
    {
        $settings = [
            'service_reminder_interval' => (int) Setting::get('service_reminder_interval', 180),
            'service_reminder_mileage' => (int) Setting::get('service_reminder_mileage', 5000),
            'transfer_expiry_days' => (int) Setting::get('transfer_expiry_days', 7),
        ];

        return view('super-admin.settings.index', compact('settings'));
    }

    /**
     * Simpan pembaruan pengaturan global sistem dan catat perubahan di log audit.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'service_reminder_interval' => 'required|integer|min:1',
            'service_reminder_mileage' => 'required|integer|min:1',
            'transfer_expiry_days' => 'required|integer|min:1',
        ]);

        $oldValues = [
            'service_reminder_interval' => (int) Setting::get('service_reminder_interval', 180),
            'service_reminder_mileage' => (int) Setting::get('service_reminder_mileage', 5000),
            'transfer_expiry_days' => (int) Setting::get('transfer_expiry_days', 7),
        ];

        $newValues = [
            'service_reminder_interval' => (int) $request->input('service_reminder_interval'),
            'service_reminder_mileage' => (int) $request->input('service_reminder_mileage'),
            'transfer_expiry_days' => (int) $request->input('transfer_expiry_days'),
        ];

        Setting::set('service_reminder_interval', $newValues['service_reminder_interval']);
        Setting::set('service_reminder_mileage', $newValues['service_reminder_mileage']);
        Setting::set('transfer_expiry_days', $newValues['transfer_expiry_days']);

        // Catat perubahan ke Audit Log
        AuditLog::record(
            'global_settings_update',
            'settings',
            null,
            [
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ]
        );

        return redirect()->back()->with('success', 'Pengaturan global berhasil diperbarui.');
    }
}

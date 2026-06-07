<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SetupController extends Controller
{
    public function settings(Request $request)
    {
        $query = Setting::where('company_id', $request->user()->company_id);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        $settings = $query->orderBy('category')->orderBy('key')->get();

        return response()->json($settings);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.type' => 'required|in:string,number,boolean,json',
            'settings.*.category' => 'required|string',
            'settings.*.description' => 'nullable|string',
            'settings.*.is_public' => 'nullable|boolean',
        ]);

        try {
            foreach ($request->settings as $settingData) {
                Setting::setSetting(
                    $settingData['key'],
                    $settingData['value'],
                    $request->user()->company_id,
                    $settingData['type'],
                    $settingData['category'],
                    $settingData['description'] ?? null,
                    $settingData['is_public'] ?? false
                );
            }

            return response()->json(['message' => 'Settings updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update settings: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function initializeSettings(Request $request)
    {
        $defaultSettings = [
            // Company Settings
            ['key' => 'company.name', 'value' => '', 'type' => 'string', 'category' => 'company', 'description' => 'Company legal name', 'is_public' => true],
            ['key' => 'company.currency', 'value' => 'USD', 'type' => 'string', 'category' => 'company', 'description' => 'Default currency', 'is_public' => true],
            ['key' => 'company.tax_rate', 'value' => '15', 'type' => 'number', 'category' => 'company', 'description' => 'Default tax rate (%)', 'is_public' => false],
            ['key' => 'company.fiscal_year_start', 'value' => '01-01', 'type' => 'string', 'category' => 'company', 'description' => 'Fiscal year start date', 'is_public' => false],

            // Sales Settings
            ['key' => 'sales.default_payment_terms', 'value' => 'Net 30', 'type' => 'string', 'category' => 'sales', 'description' => 'Default payment terms for sales', 'is_public' => false],
            ['key' => 'sales.auto_confirm_orders', 'value' => 'false', 'type' => 'boolean', 'category' => 'sales', 'description' => 'Auto-confirm sales orders', 'is_public' => false],
            ['key' => 'sales.default_tax_account', 'value' => '2200', 'type' => 'string', 'category' => 'sales', 'description' => 'Default tax payable account', 'is_public' => false],

            // Purchase Settings
            ['key' => 'purchase.default_payment_terms', 'value' => 'Net 45', 'type' => 'string', 'category' => 'purchase', 'description' => 'Default payment terms for purchases', 'is_public' => false],
            ['key' => 'purchase.auto_receive_orders', 'value' => 'false', 'type' => 'boolean', 'category' => 'purchase', 'description' => 'Auto-receive purchase orders', 'is_public' => false],
            ['key' => 'purchase.default_tax_account', 'value' => '2300', 'type' => 'string', 'category' => 'purchase', 'description' => 'Default tax receivable account', 'is_public' => false],

            // Inventory Settings
            ['key' => 'inventory.default_warehouse', 'value' => '1', 'type' => 'string', 'category' => 'inventory', 'description' => 'Default warehouse ID', 'is_public' => false],
            ['key' => 'inventory.allow_negative_stock', 'value' => 'false', 'type' => 'boolean', 'category' => 'inventory', 'description' => 'Allow negative inventory', 'is_public' => false],
            ['key' => 'inventory.auto_adjust_cost', 'value' => 'true', 'type' => 'boolean', 'category' => 'inventory', 'description' => 'Auto-adjust item costs', 'is_public' => false],

            // Manufacturing Settings
            ['key' => 'manufacturing.auto_consume_materials', 'value' => 'true', 'type' => 'boolean', 'category' => 'manufacturing', 'description' => 'Auto-consume materials on production start', 'is_public' => false],
            ['key' => 'manufacturing.default_work_center', 'value' => '1', 'type' => 'string', 'category' => 'manufacturing', 'description' => 'Default work center ID', 'is_public' => false],

            // Accounting Settings
            ['key' => 'accounting.auto_post_journal', 'value' => 'false', 'type' => 'boolean', 'category' => 'accounting', 'description' => 'Auto-post journal entries', 'is_public' => false],
            ['key' => 'accounting.require_approval', 'value' => 'true', 'type' => 'boolean', 'category' => 'accounting', 'description' => 'Require approval for journal entries', 'is_public' => false],
            ['key' => 'accounting.fiscal_year', 'value' => '2024', 'type' => 'string', 'category' => 'accounting', 'description' => 'Current fiscal year', 'is_public' => false],

            // HR Settings
            ['key' => 'hr.default_work_hours', 'value' => '40', 'type' => 'number', 'category' => 'hr', 'description' => 'Default work hours per week', 'is_public' => false],
            ['key' => 'hr.overtime_rate', 'value' => '1.5', 'type' => 'number', 'category' => 'hr', 'description' => 'Overtime pay rate multiplier', 'is_public' => false],
            ['key' => 'hr.auto_generate_payroll', 'value' => 'false', 'type' => 'boolean', 'category' => 'hr', 'description' => 'Auto-generate payroll periods', 'is_public' => false],

            // System Settings
            ['key' => 'system.date_format', 'value' => 'Y-m-d', 'type' => 'string', 'category' => 'system', 'description' => 'System date format', 'is_public' => true],
            ['key' => 'system.time_format', 'value' => '24', 'type' => 'string', 'category' => 'system', 'description' => 'Time format (12 or 24)', 'is_public' => true],
            ['key' => 'system.timezone', 'value' => 'UTC', 'type' => 'string', 'category' => 'system', 'description' => 'System timezone', 'is_public' => true],
            ['key' => 'system.backup_enabled', 'value' => 'true', 'type' => 'boolean', 'category' => 'system', 'description' => 'Enable automatic backups', 'is_public' => false],
            ['key' => 'system.backup_frequency', 'value' => 'daily', 'type' => 'string', 'category' => 'system', 'description' => 'Backup frequency', 'is_public' => false],
        ];

        try {
            foreach ($defaultSettings as $setting) {
                $existing = Setting::where('company_id', $request->user()->company_id)
                    ->where('key', $setting['key'])
                    ->first();

                if (!$existing) {
                    Setting::create([
                        'company_id' => $request->user()->company_id,
                        'key' => $setting['key'],
                        'value' => $setting['value'],
                        'type' => $setting['type'],
                        'category' => $setting['category'],
                        'description' => $setting['description'],
                        'is_public' => $setting['is_public'],
                    ]);
                }
            }

            return response()->json(['message' => 'Default settings initialized successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to initialize settings: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function systemInfo(Request $request)
    {
        $info = [
            'app_name' => config('app.name'),
            'app_version' => '1.0.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => config('database.default'),
            'environment' => config('app.env'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
            'user_role' => $request->user()->getRoleNames()->first(),
            'permissions' => $request->user()->getAllPermissions()->pluck('name'),
        ];

        return response()->json($info);
    }

    public function backup(Request $request)
    {
        // This would implement actual backup functionality
        return response()->json([
            'message' => 'Backup functionality not implemented in demo',
            'note' => 'In production, this would create database and file backups'
        ]);
    }

    public function restore(Request $request)
    {
        // This would implement actual restore functionality
        return response()->json([
            'message' => 'Restore functionality not implemented in demo',
            'note' => 'In production, this would restore from backup files'
        ]);
    }
}

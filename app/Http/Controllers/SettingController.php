<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the System Settings control dashboard.
     */
    public function index(): View
    {
        Gate::authorize('manage-settings');

        $settings = array_merge(Setting::getDefaults(), Setting::getAllFromCache());

        return view('settings.index', compact('settings'));
    }

    /**
     * Update system configuration settings in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('manage-settings');

        $validated = $request->validate([
            // 1. Dairy / Business Information
            'business_name' => ['required', 'string', 'max:255'],
            'business_phone' => ['required', 'string', 'max:50'],
            'business_email' => ['required', 'email', 'max:255'],
            'business_address' => ['required', 'string', 'max:500'],
            'business_city' => ['required', 'string', 'max:100'],
            'business_state' => ['required', 'string', 'max:100'],
            'business_pincode' => ['required', 'string', 'max:20'],
            'business_website' => ['nullable', 'url', 'max:255'],
            'business_gst' => ['nullable', 'string', 'max:50'],
            'business_description' => ['nullable', 'string', 'max:1000'],
            'business_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],

            // 2. Milk Business Settings
            'milk_default_unit' => ['required', 'string', 'max:20'],
            'milk_default_currency' => ['required', 'string', 'max:10'],
            'milk_currency_symbol' => ['required', 'string', 'max:10'],
            'milk_base_fat' => ['required', 'numeric', 'min:0', 'max:20'],
            'milk_base_snf' => ['required', 'numeric', 'min:0', 'max:20'],
            'milk_default_shift' => ['required', 'string', 'in:morning,evening'],

            // 3. Order / Delivery Settings
            'order_number_prefix' => ['required', 'string', 'max:20'],
            'delivery_number_prefix' => ['required', 'string', 'max:20'],
            'default_order_status' => ['required', 'string', 'in:pending,confirmed,preparing,dispatched,delivered,cancelled'],
            'default_delivery_status' => ['required', 'string', 'in:pending,assigned,out_for_delivery,delivered,failed,cancelled'],

            // 4. System Settings
            'system_timezone' => ['required', 'string', 'max:100'],
            'system_date_format' => ['required', 'string', 'max:50'],
            'system_pagination_limit' => ['required', 'integer', 'min:5', 'max:100'],
            'customer_ordering_enabled' => ['required', 'boolean'],
            'maintenance_mode_enabled' => ['required', 'boolean'],
        ]);

        $settingsToSave = $validated;

        // Handle safe logo file upload
        if ($request->hasFile('business_logo')) {
            $oldLogo = Setting::get('business_logo');

            // Store new logo in storage/app/public/settings
            $newPath = $request->file('business_logo')->store('settings', 'public');
            $settingsToSave['business_logo'] = $newPath;

            // Delete old logo safely after successful upload
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
        } else {
            // Keep existing logo path
            unset($settingsToSave['business_logo']);
        }

        // Persist all settings into DB
        Setting::setMany($settingsToSave, 'system');

        return back()->with('success', __('System settings updated successfully.'));
    }
}

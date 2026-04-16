<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display the system settings page.
     */
    public function index()
    {
        $settings = [
            'unit_price' => Setting::get('unit_price', config('settings.unit_price')),
            'delivery_days' => json_decode(Setting::get('delivery_days', json_encode(config('settings.delivery_days'))), true),
            'order_prefix' => Setting::get('order_prefix', 'HST'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'unit_price' => 'required|numeric|min:0',
            'delivery_days' => 'required|array|min:1',
            'delivery_days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'order_prefix' => 'nullable|string|max:10',
        ]);

        Setting::set('unit_price', $request->unit_price);
        Setting::set('delivery_days', json_encode($request->delivery_days));
        Setting::set('order_prefix', $request->order_prefix);

        return redirect()->route('settings.index')->with('success', 'System settings updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Dashboard\Setting;
use App\Traits\UploadFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    use UploadFileTrait;
    public function index()
    {
        return view('dashboard.settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|array',
            'site_name.ar' => 'required|string',
            'site_name.en' => 'required|string',
            'address' => 'required|array',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'youtube' => 'nullable|url',
            // 'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            // 'favicon' => 'nullable|image|mimes:jpeg,png,ico,webp|max:1024',
        ]);

        $settings = Setting::first();
        $data = $request->except('logo','favicon');

        // $data = $request->except('favicon','logo');
        if ($request->hasFile('logo')) {
            $old_logo = $settings->logo;
            
            $img = $request->file('logo');
            $data['logo'] = $this->uploadFile($img, 'settings', $old_logo);
        }
        if ($request->hasFile('favicon')) {
            $old_favicon = $settings->favicon;
            $img = $request->file('favicon');
            $data['favicon'] = $this->uploadFile($img, 'settings', $old_favicon);
        }
        try {
            Cache::forget('site_settings');
            $settings->update($data);
            return redirect()->route('dashboard.settings.index')->with(['success' => __('messages.done')]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return redirect()->route('dashboard.settings.index')->with(['error' => __('messages.error')]);
        }

    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureRole($request, 'admin');
        $settings = Settings::query()->pluck('setting_value', 'setting_key');
        return $this->successResponse($settings);
    }

    public function update(Request $request)
    {
        $this->ensureRole($request, 'admin');

        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:255',
            'settings.*.value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        foreach ($request->settings as $item) {
            Settings::query()->updateOrCreate(
                ['setting_key' => $item['key']],
                [
                    'setting_value' => $item['value'] ?? '',
                    'updated_by' => $request->user()?->id,
                ]
            );
        }

        return $this->successResponse(null, 'Settings updated');
    }

    public function show(Request $request, $key)
    {
        $this->ensureRole($request, 'admin');
        $setting = Settings::query()->where('setting_key', $key)->first();
        if (!$setting) {
            return $this->errorResponse('Setting not found', 404);
        }
        return $this->successResponse($setting);
    }
}

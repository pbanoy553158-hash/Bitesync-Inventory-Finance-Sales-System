<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SystemSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SystemSettingsController extends Controller
{
    public function edit(SystemSettings $systemSettings): View
    {
        $settings = $systemSettings->all();
        $settings['appearance_theme'] = auth()->user()->appearance_theme;
        $settings['text_size'] = auth()->user()->text_size;

        return view('admin.settings', [
            'settings' => $settings,
            'currencies' => SystemSettings::CURRENCIES,
            'timezones' => SystemSettings::TIMEZONES,
            'pendingUserCount' => User::query()
                ->where('approval_status', User::APPROVAL_PENDING)
                ->count(),
        ]);
    }

    public function update(
        Request $request,
        SystemSettings $systemSettings
    ): RedirectResponse {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:50'],
            'business_address' => ['nullable', 'string', 'max:1000'],
            'currency_code' => [
                'required',
                Rule::in(array_keys(SystemSettings::CURRENCIES)),
            ],
            'timezone' => [
                'required',
                Rule::in(array_keys(SystemSettings::TIMEZONES)),
            ],
            'locale' => ['required', Rule::in(['en', 'en_PH'])],
            'appearance_theme' => ['required', Rule::in(['light', 'dark'])],
            'text_size' => ['required', Rule::in(['small', 'medium', 'large'])],
            'default_minimum_stock' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
        ]);

        $appearancePreferences = [
            'appearance_theme' => $validated['appearance_theme'],
            'text_size' => $validated['text_size'],
        ];
        unset($validated['appearance_theme'], $validated['text_size']);

        DB::transaction(function () use ($request, $systemSettings, $validated, $appearancePreferences) {
            $systemSettings->update($validated);
            $request->user()->update($appearancePreferences);
        });

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'System settings saved successfully.');
    }
}
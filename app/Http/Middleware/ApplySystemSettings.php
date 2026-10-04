<?php

namespace App\Http\Middleware;

use App\Services\SystemSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ApplySystemSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SystemSettings::class)->all();

        config([
            'app.timezone' => $settings['timezone'],
            'app.locale' => $settings['locale'],
        ]);

        date_default_timezone_set($settings['timezone']);
        app()->setLocale($settings['locale']);

        View::share('systemSettings', $settings);
        View::share('businessName', $settings['business_name']);
        View::share(
            'currencySymbol',
            app(SystemSettings::class)->currencySymbol($settings['currency_code'])
        );

        return $next($request);
    }
}
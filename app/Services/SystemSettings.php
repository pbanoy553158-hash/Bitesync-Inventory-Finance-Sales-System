<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Schema;

class SystemSettings
{
    public const CURRENCIES = [
        'PHP' => [
            'name' => 'Philippine Peso',
            'symbol' => '₱',
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
        ],
    ];

    public const TIMEZONES = [
        'UTC' => 'UTC',
        'Asia/Manila' => 'Asia/Manila',
        'America/Los_Angeles' => 'America/Los_Angeles',
        'America/New_York' => 'America/New_York',
        'Europe/London' => 'Europe/London',
        'Europe/Paris' => 'Europe/Paris',
        'Asia/Tokyo' => 'Asia/Tokyo',
        'Australia/Sydney' => 'Australia/Sydney',
    ];

    public function all(): array
    {
        $defaults = [
            'business_name' => 'The Crazy Bite Co.',
            'business_email' => '',
            'business_phone' => '',
            'business_address' => '',
            'currency_code' => 'PHP',
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => 'en',
            'default_minimum_stock' => '0',
        ];

        if (!Schema::hasTable('system_settings')) {
            return $defaults;
        }

        $stored = SystemSetting::query()
            ->pluck('value', 'key')
            ->all();

        return array_merge($defaults, $stored);
    }

    public function update(array $values): void
    {
        foreach ($values as $key => $value) {
            SystemSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => (string) ($value ?? '')]
            );
        }
    }

    public function currencySymbol(?string $currencyCode = null): string
    {
        $currencyCode ??= $this->all()['currency_code'];

        return self::CURRENCIES[$currencyCode]['symbol'] ?? '₱';
    }
}
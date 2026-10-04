@extends('layouts.app')

@section('title', 'BiteSync | System Settings')

@section('content')
<div class="settings-page">
    <header class="settings-heading">
        <div>
            <div class="settings-eyebrow">Administration</div>
            <h1>System Settings</h1>
            <p>Manage your business profile, regional preferences, and inventory defaults.</p>
        </div>
    </header>

    @if (session('status'))
        <div class="settings-notice" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="settings-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="settings-form">
        @csrf
        @method('PUT')

        <section class="settings-section" aria-labelledby="business-profile-heading">
            <div class="settings-section-heading">
                <div>
                    <h2 id="business-profile-heading">Business Profile</h2>
                    <p>Business details shown in the application.</p>
                </div>
            </div>

            <div class="settings-fields">
                <label class="settings-field settings-field-wide">
                    <span>Business name</span>
                    <input type="text" name="business_name" maxlength="255" required value="{{ old('business_name', $settings['business_name']) }}">
                </label>
                <label class="settings-field">
                    <span>Contact email</span>
                    <input type="email" name="business_email" maxlength="255" value="{{ old('business_email', $settings['business_email']) }}">
                </label>
                <label class="settings-field">
                    <span>Contact phone</span>
                    <input type="text" name="business_phone" maxlength="50" value="{{ old('business_phone', $settings['business_phone']) }}">
                </label>
                <label class="settings-field settings-field-wide">
                    <span>Business address</span>
                    <textarea name="business_address" rows="3" maxlength="1000">{{ old('business_address', $settings['business_address']) }}</textarea>
                </label>
            </div>
        </section>

        <section class="settings-section" aria-labelledby="regional-heading">
            <div class="settings-section-heading">
                <div>
                    <h2 id="regional-heading">Currency &amp; Locale</h2>
                    <p>Currency changes affect display only; existing amounts are not converted.</p>
                </div>
            </div>

            <div class="settings-fields">
                <label class="settings-field">
                    <span>Currency</span>
                    <select name="currency_code" required>
                        @foreach ($currencies as $code => $currency)
                            <option value="{{ $code }}" @selected(old('currency_code', $settings['currency_code']) === $code)>
                                {{ $code }} · {{ $currency['name'] }} ({{ $currency['symbol'] }})
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="settings-field">
                    <span>Timezone</span>
                    <select name="timezone" required>
                        @foreach ($timezones as $timezone => $label)
                            <option value="{{ $timezone }}" @selected(old('timezone', $settings['timezone']) === $timezone)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label class="settings-field">
                    <span>Application locale</span>
                    <select name="locale" required>
                        <option value="en" @selected(old('locale', $settings['locale']) === 'en')>English</option>
                        <option value="en_PH" @selected(old('locale', $settings['locale']) === 'en_PH')>English (Philippines)</option>
                    </select>
                </label>
            </div>
        </section>

        <section class="settings-section" aria-labelledby="inventory-defaults-heading">
            <div class="settings-section-heading">
                <div>
                    <h2 id="inventory-defaults-heading">Inventory Defaults</h2>
                    <p>Applied when creating new inventory items. Existing items are unchanged.</p>
                </div>
            </div>

            <div class="settings-fields">
                <label class="settings-field settings-field-narrow">
                    <span>Default minimum stock</span>
                    <input type="number" name="default_minimum_stock" min="0" max="999999999.99" step="0.01" required value="{{ old('default_minimum_stock', $settings['default_minimum_stock']) }}">
                </label>
            </div>
        </section>

        <div class="settings-actions">
            <button type="submit" class="settings-save-button">Save Settings</button>
        </div>
    </form>

    <section class="settings-section settings-access" aria-labelledby="access-heading">
        <div class="settings-section-heading">
            <div>
                <h2 id="access-heading">User Access</h2>
                <p>Review pending accounts and assign roles in User Management.</p>
            </div>
            <a class="settings-access-link" href="{{ route('admin.users.index') }}">
                Manage Users <span aria-hidden="true">→</span>
            </a>
        </div>
        <div class="settings-access-count">
            <strong>{{ $pendingUserCount }}</strong>
            <span>accounts awaiting approval</span>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.settings-page {
    width: 100%;
    max-width: 1040px;
    margin: 0 auto;
    color: #2c241f;
}

.settings-heading {
    margin-bottom: 22px;
}

.settings-eyebrow {
    color: #a9825b;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
}

.settings-heading h1 {
    margin: 5px 0;
    color: #241a14;
    font-size: 28px;
    line-height: 1.2;
}

.settings-heading p,
.settings-section-heading p {
    margin: 0;
    color: #81776f;
    font-size: 13px;
}

.settings-form {
    display: grid;
    gap: 14px;
}

.settings-section {
    padding: 19px 21px;
    border: 1px solid #e4dcd4;
    border-radius: 9px;
    background: #fff;
}

.settings-section-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 17px;
}

.settings-section-heading h2 {
    margin: 0 0 4px;
    color: #34251d;
    font-size: 15px;
}

.settings-fields {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.settings-field {
    display: grid;
    align-content: start;
    gap: 6px;
    color: #625951;
    font-size: 12px;
    font-weight: 700;
}

.settings-field input,
.settings-field select,
.settings-field textarea {
    width: 100%;
    min-height: 40px;
    padding: 9px 11px;
    border: 1px solid #ddd4ca;
    border-radius: 7px;
    background: #fff;
    color: #34251d;
    font: inherit;
    font-size: 13px;
}

.settings-field textarea {
    min-height: 82px;
    resize: vertical;
}

.settings-field-wide {
    grid-column: 1 / -1;
}

.settings-field-narrow {
    max-width: 320px;
}

.settings-actions {
    display: flex;
    justify-content: flex-end;
}

.settings-save-button,
.settings-access-link {
    min-height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 16px;
    border: 0;
    border-radius: 7px;
    background: #a85f28;
    color: #fff;
    font: inherit;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
}

.settings-save-button:hover,
.settings-access-link:hover {
    background: #87491f;
    color: #fff;
}

.settings-access {
    margin-top: 15px;
}

.settings-access-link {
    min-height: 36px;
    background: #f4e4d4;
    color: #7d4e29;
}

.settings-access-link:hover {
    background: #ecd3bc;
    color: #603b21;
}

.settings-access-count {
    display: flex;
    align-items: baseline;
    gap: 8px;
    color: #81776f;
    font-size: 12px;
}

.settings-access-count strong {
    color: #34251d;
    font-size: 20px;
}

.settings-notice,
.settings-error {
    margin-bottom: 16px;
    padding: 12px 15px;
    border-radius: 7px;
    font-size: 13px;
}

.settings-notice {
    border: 1px solid #cfe4d2;
    background: #edf6ef;
    color: #356b41;
}

.settings-error {
    border: 1px solid #f0cccc;
    background: #fff2f1;
    color: #b94b4b;
}

@media (max-width: 700px) {
    .settings-heading h1 {
        font-size: 24px;
    }

    .settings-section {
        padding: 16px;
    }

    .settings-fields {
        grid-template-columns: 1fr;
    }

    .settings-field-wide {
        grid-column: auto;
    }

    .settings-field-narrow {
        max-width: none;
    }

    .settings-section-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .settings-actions,
    .settings-save-button,
    .settings-access-link {
        width: 100%;
    }
}
</style>
@endpush

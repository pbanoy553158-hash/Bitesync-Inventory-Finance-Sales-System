@extends('layouts.app')

@section('title', 'BiteSync | System Settings')

@section('content')
<div class="settings-page">
    <div class="topbar">
        <div class="page-title"><small>Account</small><h1>System settings</h1><p>Manage your profile and appearance preferences.</p></div>
    </div>

    @if (session('status')) <div class="alert-success">{{ session('status') }}</div> @endif
    @if ($errors->any()) <div class="alert-error"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="content-card settings-form">
        @csrf @method('PUT')
        <section>
            <h2>Profile information</h2>
            <p class="form-help">Update the details shown in your BiteSync account.</p>
            <div class="profile-upload">
                @if ($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile picture" class="profile-preview">
                @else
                    <div class="profile-preview profile-initial">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
                <label class="button button-secondary">Choose profile picture<input type="file" name="profile_photo" accept="image/*" hidden></label>
            </div>
            <div class="form-grid">
                <label>Name<input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>
                <label>Email<input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
            </div>
        </section>
        <section>
            <h2>Appearance</h2>
            <p class="form-help">Choose how BiteSync looks on this account.</p>
            <label>Theme<select name="theme"><option value="light" @selected(old('theme', $user->theme) === 'light')>Light mode</option><option value="dark" @selected(old('theme', $user->theme) === 'dark')>Dark mode</option></select></label>
        </section>
        <section>
            <h2>Change password</h2><p class="form-help">Leave these fields blank to keep your current password.</p>
            <div class="form-grid"><label>New password<input type="password" name="password" autocomplete="new-password"></label><label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password"></label></div>
        </section>
        <button class="button button-primary" type="submit">Save settings</button>
    </form>
</div>
@endsection

@push('styles')
<style>
.settings-form { max-width: 820px; margin-top: 24px; padding: 28px; }
.settings-form section { padding-bottom: 25px; margin-bottom: 25px; border-bottom: 1px solid var(--border); }
.settings-form h2 { margin-bottom: 4px; }
.form-help { color: var(--muted); margin-bottom: 18px; }
.form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; margin-top: 18px; }
.settings-form label { display: flex; flex-direction: column; gap: 7px; font-weight: 700; }
.settings-form input, .settings-form select { border: 1px solid var(--border); border-radius: 8px; padding: 11px 12px; background: var(--card-soft); color: var(--text); font: inherit; }
.profile-upload { display: flex; align-items: center; gap: 16px; }
.profile-preview { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; }
.profile-initial { display: grid; place-items: center; color: #fff; background: var(--orange); font-size: 1.7rem; font-weight: 800; }
.button-secondary { color: var(--text); background: var(--orange-light); cursor: pointer; }
.alert-success, .alert-error { max-width: 820px; margin-top: 18px; padding: 12px 16px; border-radius: 8px; }
.alert-success { color: var(--green); background: var(--green-light); }
.alert-error { color: var(--red); background: var(--red-light); }
@media (max-width: 700px) { .form-grid { grid-template-columns: 1fr; } }
</style>
@endpush

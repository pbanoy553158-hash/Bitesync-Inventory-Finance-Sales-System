@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    <div style="max-width: 760px; margin: 0 auto;">
        <div style="margin-bottom: 24px;">
            <div style="color: #a9825b; font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase;">
                My Account
            </div>
            <h1 style="margin: 6px 0; color: #241a14; font-size: 28px;">Edit Profile</h1>
            <p style="margin: 0; color: #81776f; font-size: 14px;">
                Update your name, email address, or password.
            </p>
        </div>

        @if (session('status'))
            <div role="status" style="margin-bottom: 18px; padding: 13px 16px; border: 1px solid #cfe4d2; border-radius: 10px; background: #edf6ef; color: #356b41;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" style="margin-bottom: 18px; padding: 13px 16px; border: 1px solid #f0cccc; border-radius: 10px; background: #fff2f1; color: #b94b4b;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" style="padding: 24px; border: 1px solid #e4dcd4; border-radius: 14px; background: #fff;">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 18px;">
                <label for="name" style="display: block; margin-bottom: 7px; color: #34251d; font-size: 13px; font-weight: 700;">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" style="width: 100%; min-height: 46px; padding: 0 12px; border: 1px solid #ddd4ca; border-radius: 9px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label for="email" style="display: block; margin-bottom: 7px; color: #34251d; font-size: 13px; font-weight: 700;">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email" style="width: 100%; min-height: 46px; padding: 0 12px; border: 1px solid #ddd4ca; border-radius: 9px;">
            </div>

            <h2 style="margin: 0 0 6px; color: #34251d; font-size: 16px;">Change password</h2>
            <p style="margin: 0 0 16px; color: #81776f; font-size: 13px;">Leave these fields blank if you want to keep your current password.</p>

            <div style="margin-bottom: 18px;">
                <label for="current_password" style="display: block; margin-bottom: 7px; color: #34251d; font-size: 13px; font-weight: 700;">Current password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" style="width: 100%; min-height: 46px; padding: 0 12px; border: 1px solid #ddd4ca; border-radius: 9px;">
            </div>

            <div style="margin-bottom: 18px;">
                <label for="password" style="display: block; margin-bottom: 7px; color: #34251d; font-size: 13px; font-weight: 700;">New password</label>
                <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" style="width: 100%; min-height: 46px; padding: 0 12px; border: 1px solid #ddd4ca; border-radius: 9px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label for="password_confirmation" style="display: block; margin-bottom: 7px; color: #34251d; font-size: 13px; font-weight: 700;">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" style="width: 100%; min-height: 46px; padding: 0 12px; border: 1px solid #ddd4ca; border-radius: 9px;">
            </div>

            <button type="submit" style="min-height: 46px; padding: 0 20px; border: 0; border-radius: 9px; background: #a85f28; color: #fff; cursor: pointer; font-weight: 700;">
                Save Changes
            </button>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'User Approval')

@section('content')
    <div style="max-width: 1100px; margin: 0 auto;">
        <div style="margin-bottom: 24px;">
            <div style="color: #a9825b; font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase;">
                Administration
            </div>
            <h1 style="margin: 6px 0; color: #241a14; font-size: 28px;">User Approval</h1>
            <p style="margin: 0; color: #81776f; font-size: 14px;">
                Review new accounts and assign an access role when approving them.
            </p>
        </div>

        @if (session('status'))
            <div role="status" style="margin-bottom: 18px; padding: 13px 16px; border: 1px solid #cfe4d2; border-radius: 10px; background: #edf6ef; color: #356b41;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" style="margin-bottom: 18px; padding: 13px 16px; border: 1px solid #f0cccc; border-radius: 10px; background: #fff2f1; color: #b94b4b;">
                {{ $errors->first() }}
            </div>
        @endif

        <section style="overflow: hidden; border: 1px solid #e4dcd4; border-radius: 14px; background: #fff;">
            <div style="padding: 18px 20px; border-bottom: 1px solid #eee7e0;">
                <h2 style="margin: 0; color: #34251d; font-size: 16px;">Pending accounts ({{ $pendingUsers->count() }})</h2>
            </div>

            @forelse ($pendingUsers as $pendingUser)
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 18px 20px; border-bottom: 1px solid #f0ebe6;">
                    <div>
                        <div style="color: #34251d; font-weight: 700;">{{ $pendingUser->name }}</div>
                        <div style="margin-top: 4px; color: #81776f; font-size: 13px;">{{ $pendingUser->email }}</div>
                        <div style="margin-top: 4px; color: #9b9189; font-size: 12px;">Requested {{ $pendingUser->created_at->format('M d, Y') }}</div>
                    </div>

                    <form method="POST" action="{{ route('admin.users.approve', $pendingUser) }}" style="display: flex; align-items: center; gap: 10px;">
                        @csrf
                        <label for="role-{{ $pendingUser->id }}" style="color: #71675f; font-size: 12px;">Assign role</label>
                        <select id="role-{{ $pendingUser->id }}" name="role" required style="min-height: 40px; padding: 0 10px; border: 1px solid #ddd4ca; border-radius: 8px; background: #fff; color: #34251d;">
                            <option value="Procurement">Procurement</option>
                            <option value="Finance">Finance</option>
                            <option value="CEO/Admin">CEO/Admin</option>
                        </select>
                        <button type="submit" style="min-height: 40px; padding: 0 14px; border: 0; border-radius: 8px; background: #a85f28; color: white; cursor: pointer; font-weight: 700;">
                            Approve
                        </button>
                    </form>
                </div>
            @empty
                <div style="padding: 28px 20px; color: #81776f; font-size: 14px;">
                    There are no accounts waiting for approval.
                </div>
            @endforelse
        </section>
    </div>
@endsection

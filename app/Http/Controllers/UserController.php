<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Show accounts awaiting admin approval.
     */
    public function index(): View
    {
        $pendingUsers = User::query()
            ->where('approval_status', User::APPROVAL_PENDING)
            ->orderBy('created_at')
            ->get();

        return view('admin.users.index', [
            'pendingUsers' => $pendingUsers,
        ]);
    }

    /**
     * Approve a pending account and assign its role.
     */
    public function approve(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => [
                'required',
                'string',
                Rule::in(['CEO/Admin', 'Finance', 'Procurement']),
            ],
        ]);

        if ($user->approval_status !== User::APPROVAL_PENDING) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors([
                    'approval' => 'This account is no longer waiting for approval.',
                ]);
        }

        $user->forceFill([
            'role' => $validated['role'],
            'approval_status' => User::APPROVAL_APPROVED,
        ])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "{$user->name}'s account has been approved.");
    }
}

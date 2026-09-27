<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Show the account registration page.
     */
    public function showSignup(): View
    {
        return view('auth.signup');
    }

    /**
     * Show the approval status after signup.
     */
    public function showSignupPending(): View
    {
        return view('auth.signup-pending');
    }

    /**
     * Create an account that must be approved and assigned a role by an admin.
     */
    public function signup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'Pending',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        return redirect()->route('signup.pending');
    }

    /**
     * Process the login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'email' => 'The email or password is incorrect.',
                ])
                ->onlyInput('email');
        }

        // Regenerate the session after successful login.
        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->isApproved()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your account is waiting for admin approval.',
                ])
                ->onlyInput('email');
        }

        /*
        |--------------------------------------------------------------------------
        | CEO/Admin
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'CEO/Admin') {
            return redirect()->route('admin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Finance
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'Finance') {
            return redirect()->route('finance.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Procurement
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'Procurement') {
            return redirect()->route('procurement.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | Invalid Role
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Your account does not have a valid BiteSync role.',
            ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
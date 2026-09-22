<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'CEO/Admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'Finance') {
                return redirect()->route('finance.dashboard');
            }

            if ($user->role === 'Procurement') {
                return redirect()->route('procurement.dashboard');
            }
        }

        return view('auth.login');
    }

    /**
     * Show the registration page.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'CEO/Admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'Finance') {
                return redirect()->route('finance.dashboard');
            }

            if ($user->role === 'Procurement') {
                return redirect()->route('procurement.dashboard');
            }
        }

        return view('auth.register');
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

        $request->session()->regenerate();

        return $this->redirectUserAfterAuth(Auth::user(), $request);
    }

    /**
     * Process the registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectUserAfterAuth($user, $request);
    }

    /**
     * Redirect the authenticated user based on their role.
     */
    protected function redirectUserAfterAuth(?User $user, Request $request): RedirectResponse
    {
        if ($user === null) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account does not have a valid BiteSync role.',
            ]);
        }

        if ($user->role === 'CEO/Admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'Finance') {
            return redirect()->route('finance.dashboard');
        }

        if ($user->role === 'Procurement') {
            return redirect()->route('procurement.dashboard');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
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
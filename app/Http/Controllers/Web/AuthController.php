<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $adminExists = User::role('admin')->exists();

        return view('auth.login', compact('adminExists'));
    }

    /**
     * Handle user login authentication.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('status', "Welcome back, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     * Detects if an Admin exists to configure single-admin setup vs employee registration.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $adminExists = User::role('admin')->exists();

        return view('auth.register', compact('adminExists'));
    }

    /**
     * Handle user registration.
     * Enforces single Admin: First registered user becomes Admin; all subsequent are Employees.
     */
    public function register(Request $request): RedirectResponse
    {
        $adminExists = User::role('admin')->exists();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (! $adminExists) {
            // First registered user becomes the single primary Administrator
            $user->assignRole('admin');
            $statusMessage = 'Platform Administrator initialized successfully! Welcome to your executive workspace.';
        } else {
            // All subsequent registrations default to Employee
            // (Managers can only be created or promoted by the Administrator)
            $user->assignRole('employee');
            $statusMessage = 'Registration complete! Welcome to your employee task board.';
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)->with('status', $statusMessage);
    }

    /**
     * Log user out and destroy session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }

    /**
     * Redirect authenticated user according to their assigned role.
     */
    public function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('employee.dashboard');
    }
}

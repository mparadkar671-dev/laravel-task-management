<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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
     * Handle user login authentication with brute force protection.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('status', "Welcome back, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

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
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', 'unique:users,email'],
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
     * Show the forgot password form.
     */
    public function showForgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.forgot-password');
    }

    /**
     * Send password reset link to user's email address.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = PasswordBroker::sendResetLink(
            $request->only('email')
        );

        if ($status === PasswordBroker::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    /**
     * Show the reset/change password form.
     */
    public function showResetPassword(Request $request, string $token): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Reset user password using token verification.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ]);

                if (! $user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Your password has been reset successfully! You can now sign in with your new password.');
        }

        return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
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

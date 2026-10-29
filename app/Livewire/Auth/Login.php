<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * Authenticate the user and rotate the session identifier.
     */
    public function authenticate(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        $throttleKey = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "تعداد تلاش‌های ورود زیاد است. {$seconds} ثانیه دیگر دوباره تلاش کنید.",
            ]);
        }

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'ایمیل یا رمز عبور نادرست است.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        request()->session()->regenerate();

        $user = Auth::user();

        if ($user === null || ! $user->roles()->where('name', 'admin')->exists()) {
            $this->redirectRoute('cranes.index', navigate: true);

            return;
        }

        $this->redirectIntended(route('admin.dashboard'), navigate: true);
    }

    /**
     * Render the sign-in form.
     */
    public function render(): View
    {
        return view('livewire.auth.login')->title('ورود به سامانه');
    }

    /**
     * Create a non-identifying key for login rate limiting.
     */
    private function throttleKey(): string
    {
        return 'login:'.hash('sha256', Str::lower($this->email).'|'.request()->ip());
    }
}

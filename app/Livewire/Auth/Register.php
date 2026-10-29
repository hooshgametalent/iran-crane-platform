<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $passwordConfirmation = '';

    /**
     * Create a customer account and authenticate its owner.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required', 'string'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            $customerRole = Role::query()->where('name', 'customer')->firstOrFail();

            $user->roles()->attach($customerRole);

            return $user;
        });

        Auth::login($user);
        request()->session()->regenerate();

        $this->redirectRoute('requests.create', navigate: true);
    }

    /**
     * Render the registration form.
     */
    public function render(): View
    {
        return view('livewire.auth.register')->title('ایجاد حساب کاربری');
    }
}

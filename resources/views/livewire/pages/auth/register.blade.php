<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Register • Practizly')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required', 'string', 'lowercase', 'min:3', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(default: route('dashboard', ['user' => Auth::user()->username], absolute: false), navigate: true);
    }
}; ?>

<form wire:submit="register" class="space-y-6">
    <div class="text-center">
        <flux:heading size="lg">Create an account</flux:heading>
        <flux:subheading>Enter your details below to create an account.</flux:subheading>
    </div>

    <flux:input label="Name" type="text" autofocus placeholder="Your name" id="name" wire:model="name" required />

    <flux:input label="Email" type="email" placeholder="Your email address" id="email" wire:model="email"
        required />
    
    <flux:input label="Username" type="text" placeholder="Your username" id="username" wire:model="username" required />

    <flux:input viewable label="Password" type="password" placeholder="Your password" id="password"
        wire:model="password" required />

    <flux:input viewable label="Confirm Password" type="password" placeholder="Confirm your password"
        id="password_confirmation" wire:model="password_confirmation" required />

    <div class="space-y-2">
        <flux:button type="submit" variant="primary" class="w-full">Register</flux:button>

        <flux:button variant="ghost" class="w-full" wire:navigate href="{{ route('login') }}">Already have an
            account?
        </flux:button>
    </div>
</form>

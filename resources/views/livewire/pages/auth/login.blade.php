<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Login • Practizly')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', ['user' => Auth::user()->username], absolute: false), navigate: true);
    }
}; ?>

<form wire:submit="login" class="space-y-6">
    <div class="text-center">
        <flux:heading size="lg">Log in to your account</flux:heading>
        <flux:subheading>Welcome back!</flux:subheading>
    </div>

    <div class="space-y-6">
        <flux:input label="Email" type="email" placeholder="Your email address" id="email" wire:model="form.email"
            required autofocus autocomplete="username" id="email" />

        <flux:field>
            <div class="flex justify-between mb-3">
                <flux:label>Password</flux:label>

                @if (Route::has('password.request'))
                    <flux:link wire:navigate href="{{ route('password.request') }}" variant="subtle" class="text-sm">
                        Forgot
                        your
                        password?</flux:link>
                @endif
            </div>

            <flux:input type="password" viewable placeholder="Your password" wire:model="form.password" required
                autocomplete="current-password" id="password" />

            <flux:error name="form.password" />
        </flux:field>

        <flux:checkbox label="Remember me" id="remember" wire:model='form.remember' value="remember" />
    </div>

    <div class="space-y-2">
        <flux:button type="submit" variant="primary" class="w-full">Log in</flux:button>

        <flux:button variant="ghost" class="w-full" wire:navigate href="{{ route('register') }}">Sign up for a
            new account
        </flux:button>
    </div>
</form>

<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Forgot password • Practizly')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        Flux::toast(heading: 'Verification link sent.', text: __($status), variant: 'success');
    }
}; ?>

<form wire:submit="sendPasswordResetLink" class="space-y-6">
    <div class="text-center">
        <flux:heading size="lg">Forgot your password?</flux:heading>
        <flux:subheading>No problem. We will email you a password reset link.</flux:subheading>
    </div>

    <flux:input wire:model='email' label="Email" type="email" placeholder="Your email address" id="email"
        type="email" name="email" required autofocus />

    <div class="space-y-2">
        <flux:button variant="primary" type="submit" class="w-full">
            {{ __('Send reset link') }}
        </flux:button>

        <flux:button wire:navigate variant="ghost" class="w-full" href="{{ route('login') }}">Back to login
        </flux:button>
    </div>
</form>

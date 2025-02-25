<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] #[Title('Verify email • Practizly')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            if (!Auth::user()->is_admin) {
                $this->redirectIntended(default: route('dashboard', ['user' => Auth::user()->username], absolute: false), navigate: true);
            } else {
                $this->redirectIntended(default: route('panel', absolute: false), navigate: true);
            }

            return;
        }

        Auth::user()->sendEmailVerificationNotification();
        Auth::user()->sendEmailVerificationNotification();

        Flux::toast(heading: 'Verification link sent.', text: 'We have emailed you a verification link. Please check your email.', variant: 'success');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="space-y-6">
    <div class="text-center">
        <flux:heading size="lg">Welcome aboard!</flux:heading>
        <flux:subheading>Please verify your email address to get started.</flux:subheading>
    </div>

    <div class="space-y-2">
        <flux:button variant="primary" class="w-full" wire:click="sendVerification">Resend verification
            email
        </flux:button>

        <flux:button wire:click="logout" variant="ghost" class="w-full">Log out
        </flux:button>
    </div>
</div>

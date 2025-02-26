<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new #[Layout('layouts.dashboard')] #[Title('Profile • Practizly')] class extends Component {
    //
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Profile</flux:heading>

    <flux:separator variant="subtle" />

    <div class="max-w-xl space-y-6">
        <div class="space-y-6">
            <div>
                <flux:heading>Profile information</flux:heading>
                <flux:subheading>Update your accounts profile info and email.</flux:subheading>
            </div>

            <livewire:profile.update-profile-information-form />
        </div>

        <flux:separator variant="subtle" />

        <div class="space-y-6">
            <div>
                <flux:heading>Update password</flux:heading>
                <flux:subheading>Ensure your account is using a long, random password to stay secure.
                </flux:subheading>
            </div>

            <livewire:profile.update-password-form />
        </div>
    </div>
</div>
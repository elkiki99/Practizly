<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new #[Layout('layouts.dashboard')] #[Title('Settings • Practizly')] class extends Component {
    //
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Settings</flux:heading>

    <flux:separator variant="subtle" />

    <div class="max-w-xl space-y-6">
        <div>
            <flux:heading>Billing information</flux:heading>
            <flux:subheading>Manage your billing preferences and packages.</flux:subheading>
        </div>

        <flux:button variant="filled" icon-trailing="chevron-right" as="link" href="#">My plan</flux:button>

        <flux:separator variant="subtle" />
    </div>

    <div class="max-w-xl space-y-6">
        <div>
            <flux:heading>Delete account</flux:heading>
            <flux:subheading>Delete your account and all of its resources.</flux:subheading>
        </div>

        <livewire:profile.delete-user-form />
    </div>
</div>

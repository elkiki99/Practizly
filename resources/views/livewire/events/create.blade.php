<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<form wire:submit.prevent="createEvent">
    <flux:modal name="create-event" class="space-y-6 md:w-96">
        <div>
            <flux:heading size="lg">New event</flux:heading>
            <flux:subheading>Create a new event.</flux:subheading>
        </div>

        <!-- Name -->
        <flux:input label="Event name" placeholder="Event name" wire:model='name' autofocus required autocomplete="name" />
    </flux:modal>
</form>
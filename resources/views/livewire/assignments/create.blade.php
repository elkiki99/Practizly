<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $variant = '';
}; ?>

<flux:modal variant="{{ $variant }}" name="create-assignment" class="space-y-6 md:w-96">
    <div>
        <flux:heading size="lg">New assignment</flux:heading>
        <flux:subheading>Create a new assignment.</flux:subheading>
    </div>

    <flux:input label="Name" placeholder="Assignment name" />

    {{-- <flux:input label="Date of birth" type="date" /> --}}

    <div class="flex">
        <flux:spacer />

        <flux:button type="submit" variant="primary">Create assignment</flux:button>
    </div>
</flux:modal>


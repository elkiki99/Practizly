<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $variant = '';
}; ?>

<flux:modal variant="{{ $variant }}" name="create-attachment" class="space-y-6 md:w-96">
    <div>
        <flux:heading size="lg">New attachment</flux:heading>
        <flux:subheading>Create a new attachment.</flux:subheading>
    </div>

    <flux:input label="Name" placeholder="Attachment name" />

    {{-- <flux:input label="Date of birth" type="date" /> --}}

    <div class="flex">
        <flux:spacer />

        <flux:button type="submit" variant="primary">Create attachment</flux:button>
    </div>
</flux:modal>


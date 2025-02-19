<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $variant = '';
}; ?>

<flux:modal variant="{{ $variant }}" name="create-summary" class="space-y-6 md:w-96">
    <div>
        <flux:heading size="lg">New summary</flux:heading>
        <flux:subheading>Create a new summary.</flux:subheading>
    </div>

    <flux:input label="Name" placeholder="Summary name" />

    {{-- <flux:input label="Date of birth" type="date" /> --}}

    <div class="flex">
        <flux:spacer />

        <flux:button type="submit" variant="primary">Create summary</flux:button>
    </div>
</flux:modal>

<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $variant = '';
}; ?>

<flux:modal variant="{{ $variant }}" name="create-exam" class="space-y-6 md:w-96">
    <div>
        <flux:heading size="lg">New exam</flux:heading>
        <flux:subheading>Create a new exam.</flux:subheading>
    </div>

    <flux:input label="Name" placeholder="Exam name" />

    {{-- <flux:input label="Date of birth" type="date" /> --}}

    <div class="flex">
        <flux:spacer />

        <flux:button type="submit" variant="primary">Create exam</flux:button>
    </div>
</flux:modal>

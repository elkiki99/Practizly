<?php

use Livewire\Volt\Component;
use App\Models\Subject;

new class extends Component {
    public string $variant = '';

    public string $name = '';

    public $subjects;

    public function mount()
    {
        $this->subjects = Subject::all();
    }
}; ?>

<flux:modal variant="{{ $variant }}" name="create-exam" class="space-y-6 md:w-96">
    <div>
        <flux:heading size="lg">New exam prep</flux:heading>
        <flux:subheading>Generate a new AI exam.</flux:subheading>
    </div>

    <flux:field>
        <flux:label class="mb-2">Subject</flux:label>
        <flux:select searchable variant="listbox" wire:model="subject" placeholder="Select subject">
            @forelse($subjects as $subject)
                <flux:select.option value="{{ $subject->id }}">{{ $subject->name }}</flux:select.option>
            @empty
                <flux:select.option disabled>No subjects found</flux:select.option>
            @endforelse
        </flux:select>
    </flux:field>

    {{-- <flux:radio.group wire:model="payment" label="Select your payment method"> --}}
        <flux:radio value="cc" label="Credit Card" checked />
        <flux:radio value="paypal" label="Paypal" />
        <flux:radio value="ach" label="Bank transfer" />
    {{-- </flux:radio.group> --}}

    <div class="flex">
        <flux:spacer />

        <flux:button type="submit" variant="primary">Create exam</flux:button>
    </div>
</flux:modal>

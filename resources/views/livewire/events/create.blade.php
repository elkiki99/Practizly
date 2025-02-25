<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Event;
use Carbon\Carbon;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('required|string|in:test,exam,evaluation,oral_presentation,assignment')]
    public string $type = '';

    #[Validate('required|date')]
    public Carbon $date;

    #[Validate('nullable|string|max:1000')]
    public string $note = '';

    #[Validate('required|in:pending,completed')]
    public string $status = 'pending';

    public function createEvent()
    {
        $this->validate();

        Event::create([
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'date' => $this->date,
            'note' => $this->note,
            'status' => $this->status,
            'user_id' => Auth::id(),
        ]);

        $this->reset();

        $this->dispatch('eventCreated');

        Flux::toast(heading: 'Event created', text: 'Your event was created successfully', variant: 'success');

        $this->modal('create-event')->close();
    }
}; ?>

<form wire:submit.prevent="createEvent">
    <flux:modal variant="flyout" name="create-event" class="space-y-6 md:w-96">
        <div>
            <flux:heading size="lg">New event</flux:heading>
            <flux:subheading>Create a new event.</flux:subheading>
        </div>

        <!-- Name -->
        <flux:input label="Event name" placeholder="Algebra test" wire:model='name' autofocus required
            autocomplete="name" />

        <!-- Description -->
        <flux:textarea label="Event description" placeholder="Algebra test, topics: functions, polynomials"
            wire:model='description' rows="2"></flux:textarea>

        <!-- Type -->
        <flux:select required placeholder="Select type" variant="listbox" required wire:model='type' label="Event type">
            <flux:select.option value="test">Test</flux:select.option>
            <flux:select.option value="exam">Exam</flux:select.option>
            <flux:select.option value="evaluation">Evaluation</flux:select.option>
            <flux:select.option value="oral_presentation">Oral presentation</flux:select.option>
            <flux:select.option value="assignment">Assignment</flux:select.option>
        </flux:select>

        <!-- Date -->
        <flux:date-picker label="Event date" required wire:model='date'>
            <x-slot name="trigger">
                <flux:date-picker.input />
            </x-slot>
        </flux:date-picker>

        <!-- Note -->
        <flux:textarea label="Event note" placeholder="Remember to create a summary on polynomials for this test."
            wire:model='note' rows="2"></flux:textarea>

        <!-- Status -->
        <flux:radio.group wire:model='status' required variant="segmented" label="Status">
            <flux:radio icon="clock" value="pending" label="Pending" />
            <flux:radio icon="check-circle" value="completed" label="Completed" />
        </flux:radio.group>

        <div class="flex justify-between">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Create event</flux:button>
        </div>
    </flux:modal>
</form>

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\Topic;
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

    #[Validate('required|exists:topics,id')]
    public $topic;

    #[Validate('required|exists:subjects,id')]
    public $subject;

    public $subjects = [];
    public $topics = [];

    public function mount()
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();

        if ($this->subjects->count() === 1) {
            $this->subject = $this->subjects->first()->id;

            $this->topics = Topic::where('subject_id', $this->subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = $this->topics->first()->id;
            }
        }
    }

    #[On('subjectCreated')]
    public function updatedSubject($subject = null)
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();

        if ($this->subjects->count() === 1) {
            $this->subject = $this->subjects->first()->id;
        }

        if (!empty($subject)) {
            $this->topics = Topic::where('subject_id', $subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = $this->topics->first()->id;
            } else {
                $this->topic = null;
            }
        }
    }

    #[On('topicCreated')]
    public function updatedTopic($topic = null)
    {
        $this->topics = Topic::where('subject_id', $this->subject)->get();

        if ($this->topics->count() === 1) {
            $this->topic = $this->topics->first()->id;
        }
    }

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
            'topic_id' => $this->topic,
        ]);

        $this->reset();

        $this->dispatch('eventCreated');

        Flux::toast(heading: 'Event created', text: 'Your event was created successfully', variant: 'success');

        $this->modal('create-event')->close();
    }
}; ?>

<form wire:submit.prevent="createEvent">
    <flux:modal variant="flyout" name="create-event" class="space-y-6 w-96" x-data="{ createTopic: false }"
        x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
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

        <flux:select required label="Event subject" searchable variant="listbox" wire:model.live="subject"
            placeholder="Select subject">
            @forelse($subjects as $item)
                <flux:select.option value="{{ $item->id }}">
                    {{ $item->name }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>Create a new subject first</flux:select.option>
            @endforelse
        </flux:select>

        <flux:field>
            <div class="flex items-center justify-between mb-2">
                <flux:label>Event topic</flux:label>
                <flux:button as="link" size="xs" variant="subtle" icon-trailing="plus"
                    x-on:click="createTopic = true">New topic</flux:button>
            </div>

            <flux:select required searchable variant="listbox" wire:model="topic" placeholder="Select topic">
                @forelse($topics as $topic)
                    <flux:select.option value="{{ $topic->id }}">{{ $topic->name }}
                    </flux:select.option>
                @empty
                    <flux:select.option disabled>No topics found</flux:select.option>
                @endforelse
            </flux:select>

            <flux:error name="topic" />
        </flux:field>

        @if ($subject)
            <livewire:components.topics.create :subject_id="$subject" />
        @endif

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

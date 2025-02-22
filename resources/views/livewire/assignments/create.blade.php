<?php

use Livewire\Volt\Component;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Assignment;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string|max:1000')]
    public string $description = '';

    #[Validate('required|string|max:1000')]
    public string $guidelines = '';

    #[Validate('required|file|mimes:jpg,jpeg,png,webp|max:10240')]
    public $attachments = [];

    #[Validate('required|date')]
    public ?Carbon $due_date;

    #[Validate('required|in:pending,completed')]
    public string $status = 'pending';

    #[Validate('required|exists:topics,id')]
    public $topic;

    #[Validate('required|exists:subjects,id')]
    public $subject;

    public $subjects = [];
    public $topics = [];

    #[On('subjectCreated')]
    public function updatedSubject($subject = null)
    {
        $this->subjects = Subject::all();

        if ($this->subjects->count() === 1) {
            $this->subject = Subject::first()->id;
        }

        if (!empty($subject)) {
            $this->topics = Topic::where('subject_id', $subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = $this->topics->first()->id;
            } else {
                $this->topic = [];
            }
        } else {
            $this->topics = [];
        }
    }

    #[On('topicCreated')]
    public function updatedTopic($topic = null)
    {
        $this->topics = Topic::where('subject_id', $this->subject)->get();

        if ($this->topics->count() === 1) {
            $this->topic = Topic::first()->id;
        }
    }

    #[On('topicCreated')]
    #[On('subjectCreated')]
    public function mount()
    {
        $this->subjects = Subject::all();

        if ($this->subjects->count() === 1) {
            $this->subject = Subject::first()->id;

            $this->topics = Topic::all();

            if ($this->topics->count() === 1) {
                $this->topic = Topic::first()->id;
            }
        }
    }
}; ?>

<form wire:submit.prevent="createAssignment">
    <flux:modal name="create-assignment" class="w-[90%] space-y-6 md:w-96" x-data="{ createTopic: false }" x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
        <div>
            <flux:heading size="lg">New assignment</flux:heading>
            <flux:subheading>Create a new assignment.</flux:subheading>
        </div>

        <flux:input label="Title" placeholder="Calculate quarterly revenue" />

        <flux:input label="Description" placeholder="Analyze case study to determine performance." />

        <flux:textarea label="Guidelines"
            placeholder="Use financial statements to support your analysis, include charts, and ensure all calculations are accurate." />

        <flux:select label="Exam subject" searchable variant="listbox" wire:model.live="subject"
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
                <flux:label>Exam topic</flux:label>
                <flux:button x-show="{{ $subjects }}" as="link" size="xs" variant="subtle"
                    icon-trailing="plus" x-on:click="createTopic = true">New topic</flux:button>
            </div>

            <flux:select searchable variant="listbox" selected-suffix="{{ __('topics selected') }}"
                wire:model.live="topic" placeholder="Select topic">
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
            <livewire:exams.topics.create :subject_id="$subject" />
        @endif

        <flux:date-picker label="Due date" wire:model="due_date">
            <x-slot name="trigger">
                <flux:date-picker.input />
            </x-slot>
        </flux:date-picker>

        <flux:radio.group wire:model="status" label="Status" variant="segmented">
            <flux:radio value="pending" icon="clock" label="Pending" />
            <flux:radio value="completed" icon="document-check" label="Completed" />
        </flux:radio.group>

        <flux:input type="file" wire:model="attachments" label="Attachments" multiple />

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary">Create assignment</flux:button>
        </div>
    </flux:modal>
</form>

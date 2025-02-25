<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Assignment;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('required|string|max:1000')]
    public string $guidelines = '';

    #[Validate(['attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,doc,docx,pdf|max:10240'])]
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

    public function createAssignment()
    {
        $this->validate();

        $assignment = Assignment::create([
            'title' => $this->title,
            'description' => $this->description,
            'guidelines' => $this->guidelines,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'topic_id' => $this->topic,
        ]);

        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachmentFile) {
                $fileName = Str::slug("{$assignment->title} {$assignment->topic->name} {$assignment->topic->subject->name} assignment", '-');
                $filePath = $attachmentFile->storeAs('attachments', "{$fileName}.{$attachmentFile->getClientOriginalExtension()}", 'public');

                $assignment->attachments()->create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                ]);
            }
        }

        //Create new event

        $this->reset(['title', 'description', 'guidelines', 'attachments', 'due_date']);

        $this->dispatch('assignmentCreated');

        Flux::toast(heading: 'Assignment created', text: 'Your assignment was created successfully', variant: 'success');

        $this->modal('create-assignment')->close();
    }
}; ?>

<form wire:submit.prevent="createAssignment">
    <flux:modal variant="flyout" name="create-assignment" class="space-y-6 w-96" x-data="{ createTopic: false }" x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
        <div>
            <flux:heading size="lg">New assignment</flux:heading>
            <flux:subheading>Create a new assignment.</flux:subheading>
        </div>

        <flux:field>
            <div class="flex items-center gap-2 mb-2">
                <flux:label>Assignment title</flux:label>
                <flux:tooltip toggleable position="right">
                    <flux:button icon="information-circle" size="sm" variant="ghost" />

                    <flux:tooltip.content class="max-w-[20rem] space-y-2">
                        <p>We recommend giving your assignment a descriptive name for better organization.</p>
                    </flux:tooltip.content>
                </flux:tooltip>
            </div>
            <flux:input required wire:model="title" placeholder="Calculate quarterly revenue" autofocus
            autocomplete="name" />
        </flux:field>

        <flux:input required wire:model="description" label="Assignment description"
            placeholder="Analyze case study to determine performance." />

        <flux:textarea required wire:model="guidelines" label="Assignment guidelines"
            placeholder="Use financial statements to support your analysis, include charts, and ensure all calculations are accurate." />

        <flux:select required label="Assignment subject" searchable variant="listbox" wire:model.live="subject"
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
                <flux:label>Assignment topic</flux:label>
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
            <livewire:exams.topics.create :subject_id="$subject" />
        @endif

        <flux:date-picker required label="Assignment due date" wire:model="due_date">
            <x-slot name="trigger">
                <flux:date-picker.input />
            </x-slot>
        </flux:date-picker>

        <flux:radio.group required wire:model="status" label="Assignment status" variant="segmented">
            <flux:radio value="pending" icon="clock" label="Pending" />
            <flux:radio value="completed" icon="document-check" label="Completed" />
        </flux:radio.group>

        <flux:input type="file" wire:model="attachments" label="Assignment attachments" multiple />

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary">Create assignment</flux:button>
        </div>
    </flux:modal>
</form>

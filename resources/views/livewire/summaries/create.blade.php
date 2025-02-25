<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\Subject;
use App\Models\Summary;
use App\Models\Topic;

new class extends Component {
    #[Validate('required|exists:subjects,id')]
    public $subject = '';

    #[Validate('required|exists:topics,id')]
    public $topic = '';

    #[Validate('required|exists:attachments,id')]
    public $attachment;

    #[Validate('required|in:short,medium,long')]
    public string $size = 'short';

    public $subjects = [];
    public $topics = [];
    public $attachments = [];

    #[On('subjectCreated')]
    #[On('assignmentCreated')]
    public function mount()
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();

        if ($this->subjects->count() === 1) {
            $this->subject = $this->subjects->first()->id;

            $this->topics = Topic::where('subject_id', $this->subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = $this->topics->first()->id;

                $this->attachments = Topic::find($this->topic)->all_attachments;

                if ($this->attachments->count() === 1) {
                    $this->attachment = $this->attachments->first()->id;
                } else {
                    $this->attachment = null;
                }
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
    #[On('assignmentCreated')]
    public function updatedTopic($topic = null)
    {
        $this->topics = Topic::where('subject_id', $this->subject)->get();

        if ($this->topics->count() === 1) {
            $this->topic = $this->topics->first()->id;
        }

        if (!empty($topic)) {
            $this->attachments = Topic::find($this->topic)->all_attachments;

            if ($this->attachments->count() === 1) {
                $this->attachment = $this->attachments->first()->id;
            } else {
                $this->attachment = null;
            }
        }
    }

    public function createSummary()
    {
        $this->validate();

        $topic = Topic::find($this->topic);

        $summary = Summary::create([
            'topic_id' => $this->topic,
            'title' => $topic->name . ' Summary',
            'content' => '',
            'size' => $this->size,
            'attachment_id' => $this->attachment
        ]);     

        $summary->update([
            'title' => $topic->name . ' Summary #' . $summary->id,
        ]);

        $this->dispatch('summaryCreated');

        Flux::toast(heading: 'Summary created', text: 'Your summary was created successfully', variant: 'success');

        $this->modal('create-summary')->close();
    }
}; ?>

<form wire:submit.prevent="createSummary">
    <flux:modal name="create-summary" class="space-y-6 md:w-96">
        <div>
            <flux:heading size="lg">New summary</flux:heading>
            <flux:subheading>Create a new summary.</flux:subheading>
        </div>

        <flux:select required autofocus label="Summary subject" searchable variant="listbox" wire:model.live="subject"
            placeholder="Select subject">
            @forelse($subjects as $item)
                <flux:select.option value="{{ $item->id }}">
                    {{ $item->name }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>Create a new subject first</flux:select.option>
            @endforelse
        </flux:select>

        <flux:select required label="Summary topic" searchable variant="listbox" wire:model.live="topic"
            placeholder="Select topic">
            @forelse($topics as $topic)
                <flux:select.option value="{{ $topic->id }}">{{ $topic->name }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>Create a topic first</flux:select.option>
            @endforelse
        </flux:select>

        <flux:select required label="Summary attachment" searchable variant="listbox" wire:model="attachment"
            placeholder="Select attachments">
            @forelse($attachments as $attachment)
                <flux:select.option value="{{ $attachment->id }}">{{ $attachment->file_name }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>No attachments yet</flux:select.option>
            @endforelse
        </flux:select>

        <!-- Size -->
        <flux:radio.group required wire:model='size' label="Summary size" class="flex-col">
            <flux:radio value="short" label="Short" />
            <flux:radio value="medium" label="Medium" />
            <flux:radio value="long" label="Long" />
        </flux:radio.group>

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary">Create summary</flux:button>
        </div>
    </flux:modal>
</form>

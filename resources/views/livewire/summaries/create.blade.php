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

    public function mount()
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();

        if ($this->subjects->count() === 1) {
            $this->subject = $this->subjects->first()->id;

            $this->topics = Topic::where('subject_id', $this->subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = $this->topics->first()->id;
                $this->topics = collect([$this->topics->first()]);

                $this->attachments = $this->topics->flatMap->all_attachments->unique();

                if ($this->attachments->count() === 1) {
                    $this->attachment = $this->attachments->first()->id;
                    $this->attachments = collect([$this->attachments->first()]);
                } else {
                    $this->attachment = collect();
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
                $this->topics = collect([$this->topics->first()]);
                $this->topic = $this->topics->first()->id;
            } else {
                $this->topic = collect();
            }
        }
    }

    #[On('topicCreated')]
    public function updatedTopic($topic = null)
    {
        $this->topics = Topic::where('subject_id', $this->subject)->get();

        if ($this->topics->count() === 1) {
            $this->topic = $this->topics->first()->id;
            $this->topics = collect([$this->topics->first()]);
        }

        if (!empty($topic)) {
            $topics = Topic::whereIn('id', (array) $topic)->get();

            $this->attachments = $topics->flatMap->all_attachments->unique();

            if ($this->attachments->count() === 1) {
                $this->attachment = $this->attachments->first()->id;
                $this->attachments = collect([$this->attachments->first()]);
            } else {
                $this->attachment = collect();
            }
        }
    }

    #[On('attachmentCreated')]
    public function updateAttachment($attachment = null)
    {
        $this->topics = Topic::whereIn('id', (array) $this->topic)->get();

        $this->attachments = $this->topics->flatMap->all_attachments->unique();

        if ($this->attachments->count() === 1) {
            $this->attachment = $this->attachments->first()->id;
            $this->attachments = collect([$this->attachments->first()]);
        } else {
            $this->attachment = collect();
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
            'attachment_id' => $this->attachment,
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
    <flux:modal variant="flyout" name="create-summary" class="w-[90%] space-y-6 md:w-96" x-data="{ createTopic: false, createAttachment: false }"
        x-init="window.addEventListener('topicCreated', () => { createTopic = false });
        window.addEventListener('attachmentCreated', () => { createAttachment = false });">
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

        <flux:field>
            <div class="flex items-center justify-between mb-2">
                <flux:label>Summary topic</flux:label>
                <flux:button as="link" size="xs" variant="subtle" icon-trailing="plus"
                    x-on:click="createTopic = true">
                    New topic</flux:button>
            </div>

            <flux:select required multiple searchable variant="listbox" selected-suffix="{{ __('topics selected') }}"
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
            <livewire:components.topics.create :subject_id="$subject" />
        @endif

        <!-- Attachment -->
        <flux:field>
            <div class="flex items-center justify-between mb-2">
                <flux:label>Summary attachment</flux:label>
                <flux:button as="link" size="xs" variant="subtle" icon-trailing="plus"
                    x-on:click="createAttachment = true">
                    New attachment</flux:button>
            </div>

            <flux:select multiple required searchable selected-suffix="{{ __('attachments selected') }}"
                variant="listbox" wire:model="attachment" placeholder="Select attachment">
                @forelse($attachments as $attachment)
                    <flux:select.option value="{{ $attachment->id }}">{{ $attachment->file_name }}
                    </flux:select.option>
                @empty
                    <flux:select.option disabled>No attachments found</flux:select.option>
                @endforelse
            </flux:select>

            <flux:error name="attachment" />
        </flux:field>

        @if (
            (!empty($this->topics) && count($this->topics) === 1) ||
                (!empty($this->topic) && is_countable($this->topic) && count($this->topic) === 1))
            <livewire:components.attachments.create :topic_id="$this->topic" />
        @endif

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

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\Subject;
use App\Models\Summary;
use App\Models\Topic;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|exists:subjects,id')]
    public $subject = '';

    #[Validate('required|exists:topics,id')]
    public $topic = '';

    #[Validate('required|exists:attachments,id')]
    public $attachment = null;

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
            $selectedTopics = Topic::whereIn('id', (array) $topic)->get();
            $this->attachments = collect($this->attachments);

            $this->attachments = $this->attachments->merge($selectedTopics->flatMap->all_attachments)->unique();

            if ($this->attachments->count() === 1) {
                $this->attachment = $this->attachments->first()->id;
                $this->attachments = collect([$this->attachments->first()]);
            } else {
                $this->attachment = collect();
            }
        }
    }

    #[On('attachmentCreated')]
    public function updatedAttachment($attachment = null)
    {
        $selectedTopics = Topic::whereIn('id', (array) $this->topic)->get();
        $this->attachments = collect($this->attachments);

        $this->attachments = $this->attachments->merge($selectedTopics->flatMap->all_attachments)->unique();

        if ($this->attachments->count() === 1) {
            $this->attachment = $this->attachments->first()->id;
            $this->attachments = collect([$this->attachments->first()]);
        } else {
            $this->attachment = collect();
        }
    }

    public function createSummary()
    {
        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $counter = 1;

        while (Summary::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $summary = Summary::create([
            'subject_id' => $this->subject,
            'slug' => $slug,
            'title' => $this->title,
            'size' => $this->size,
        ]);

        if (!is_array($this->attachment)) {
            $this->attachment = is_null($this->attachment) ? [] : [$this->attachment];
        }

        foreach ($this->attachment as $attachment) {
            $summary->attachments()->attach($attachment);
        }

        $summary->topics()->sync($this->topic);

        $this->reset(['title', 'size']);

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

        <flux:field>
            <div class="flex items-center gap-2 mb-2">
                <flux:label>Summary title</flux:label>
                <flux:tooltip toggleable position="left">
                    <flux:button icon="information-circle" size="sm" variant="ghost" />

                    <flux:tooltip.content class="max-w-[20rem] space-y-2">
                        <p>We recommend giving your summary a descriptive name for better organization.</p>
                    </flux:tooltip.content>
                </flux:tooltip>
            </div>
            <flux:input required type="text" wire:model="title" placeholder="Summary on quantitative analysis"
                autofocus autocomplete="name" />
        </flux:field>

        <flux:select required label="Summary subject" searchable variant="listbox" wire:model.live="subject"
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

            <flux:select required multiple searchable selected-suffix="{{ __('attachments selected') }}"
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

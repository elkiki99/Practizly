<?php

use Livewire\Volt\Component;
use App\Models\{Subject, Topic, Exam, Attachment};
use App\Livewire\Actions\GenerateExam;
use Livewire\Attributes\{Validate, On};
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|exists:subjects,id')]
    public $subject;

    #[Validate('required|exists:topics,id')]
    public $topic;

    #[Validate('required|exists:attachments,id')]
    public $attachment = null;

    #[Validate('required|string|in:open_ended,multiple_choice,true_or_false')]
    public string $type = 'open_ended';

    #[Validate('required|string|in:easy,medium,hard')]
    public string $difficulty = 'medium';

    #[Validate('required|in:short,medium,long')]
    public string $size = 'short';

    public $attachments = [];
    public $topics = [];
    public $subjects = [];

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
    public function updatedAttachments($attachment = null)
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

    public function createExam(GenerateExam $generateExam)
    {
        $this->validate();

        $subject = Subject::find($this->subject);

        $baseSlug = Str::slug($subject->name . '-test-' . $this->type);
        $slug = $baseSlug;
        $counter = 1;

        while (Exam::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $exam = Exam::create([
            'subject_id' => $this->subject,
            'title' => $subject->name . ' Test',
            'type' => $this->type,
            'difficulty' => $this->difficulty,
            'size' => $this->size,
            'slug' => $slug,
        ]);

        $exam->update([
            'title' => $subject->name . ' Test #' . $exam->id,
        ]);

        if (!is_array($this->attachment)) {
            $this->attachment = is_null($this->attachment) ? [] : [$this->attachment];
        }

        foreach ($this->attachment as $attachment) {
            $exam->attachments()->attach($attachment);
        }

        $exam->topics()->sync($this->topic);

        // We call the action to generate the exam
        $generateExam($exam);

        $this->dispatch('examCreated');

        Flux::toast(heading: 'Exam created', text: 'Your exam was created successfully', variant: 'success');

        $this->modal('create-exam')->close();
    }
}; ?>

<form wire:submit.prevent="createExam">
    <flux:modal variant="flyout" name="create-exam" class="w-[90%] space-y-6 md:w-96" x-data="{ createTopic: false, createAttachment: false }"
        x-init="window.addEventListener('topicCreated', () => { createTopic = false });
        window.addEventListener('attachmentCreated', () => { createAttachment = false });">
        <div>
            <flux:heading size="lg">New exam prep</flux:heading>
            <flux:subheading>Generate a new AI exam.</flux:subheading>
        </div>

        <flux:select label="Exam subject" searchable variant="listbox" wire:model.live="subject"
            placeholder="Select subject" autofocus required>
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
                <flux:label>Exam attachment</flux:label>
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

        <!-- Difficulty -->
        <flux:radio.group required wire:model="difficulty" label="Exam difficulty" variant="segmented">
            <flux:radio value="easy" label="Easy" icon="check-circle" />
            <flux:radio value="medium" label="Medium" icon="stop-circle" />
            <flux:radio value="hard" label="Hard" icon="exclamation-circle" />
        </flux:radio.group>

        <!-- Size -->
        <flux:radio.group required wire:model='size' label="Exam size" class="flex-col">
            <flux:radio value="short" label="Short" description="Between 5 and 10 questions" />
            <flux:radio value="medium" label="Medium" description="Between 10 and 15 questions" />
            <flux:radio value="long" label="Long" description="Between 15 and 30 questions" />
        </flux:radio.group>

        <!-- Type -->
        <flux:radio.group required wire:model="type" label="Exam type">
            <flux:radio label="Open ended" value="open_ended" />
            <flux:radio label="Multiple choice" value="multiple_choice" />
            <flux:radio label="True or false" value="true_or_false" />
        </flux:radio.group>

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Generate exam</flux:button>
        </div>
    </flux:modal>
</form>

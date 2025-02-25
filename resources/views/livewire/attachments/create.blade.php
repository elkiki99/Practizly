<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Attachment;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|exists:subjects,id')]
    public $subject;

    #[Validate('required|exists:topics,id')]
    public $topic;

    #[Validate(['attachments.*' => 'required|file|mimes:jpg,jpeg,png,webp,doc,docx,pdf|max:10240'])]
    public $attachments = [];

    public $topics = [];
    public $subjects = [];

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

    public function createAttachment()
    {
        $this->validate();

        $topic = Topic::find($this->topic);

        foreach ($this->attachments as $attachmentFile) {
            $fileName = Str::slug("{$this->name} {$topic->name} {$topic->subject->name} attachment", '-');
            $filePath = $attachmentFile->storeAs('attachments', "{$fileName}.{$attachmentFile->getClientOriginalExtension()}", 'public');

            Attachment::create([
                'file_name' => $fileName,
                'file_path' => $filePath,
                'attachable_type' => Topic::class,
                'attachable_id' => $topic->id,
            ]);
        }

        $this->reset(['name', 'attachments']);

        $this->dispatch('attachmentCreated');

        Flux::toast(heading: 'Attachment created', text: 'Your attachment was created successfully', variant: 'success');

        $this->modal('create-attachment')->close();
    }
}; ?>

<form wire:submit.prevent='createAttachment'>
    <flux:modal variant="flyout" name="create-attachment" class="space-y-6 md:w-96" x-data="{ createTopic: false }" x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
        <div>
            <flux:heading size="lg">New attachment</flux:heading>
            <flux:subheading>Create a new attachment.</flux:subheading>
        </div>

        <flux:field>
            <div class="flex items-center gap-2 mb-2">
                <flux:label>Attachment name</flux:label>
                <flux:tooltip toggleable position="right">
                    <flux:button icon="information-circle" size="sm" variant="ghost" />

                    <flux:tooltip.content class="max-w-[20rem] space-y-2">
                        <p>We recommend giving your attachment a descriptive name for better organization.</p>
                    </flux:tooltip.content>
                </flux:tooltip>
            </div>
            <flux:input wire:model='name' placeholder="Math algebra polynomials attachment" autofocus required
                autocomplete="name" />
        </flux:field>

        <flux:select required label="Attachment subject" searchable variant="listbox" wire:model.live="subject"
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
                <flux:label>Attachment topic</flux:label>
                <flux:button as="link" size="xs" variant="subtle" icon-trailing="plus"
                    x-on:click="createTopic = true">New topic</flux:button>
            </div>

            <flux:select required searchable variant="listbox" selected-suffix="{{ __('topics selected') }}"
                wire:model="topic" placeholder="Select topic">
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

        <flux:input label="Attachment file" type="file" wire:model="attachments" multiple required />

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary">Create attachment</flux:button>
        </div>
    </flux:modal>
</form>

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Assignments;
use App\Models\Attachment;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    public $subjects = [];
    public $subject = '';
    public $topics = [];
    public $topic = '';
    public $attachments = [];
    public $attachment = '';
    public $assignments = [];

    #[On('subjectCreated')]
    #[On('assignmentCreated')]
    public function mount()
    {
        $this->subjects = Subject::where('user_id', auth()->user()->id)->get();

        if ($this->subjects->count() === 1) {
            $this->subject = Subject::first()->id;

            $this->topics = Topic::where('subject_id', $this->subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = Topic::first()->id;

                $this->attachments = Attachment::where('attachable_id', $this->topic)->get();
            }
        }
    }

    #[On('subjectCreated')]
    public function updatedSubject($subject = null)
    {
        $this->subjects = Subject::where('user_id', auth()->user()->id)->get();

        if ($this->subjects->count() === 1) {
            $this->subject = Subject::first()->id;
        }

        if (!empty($subject)) {
            $this->topics = Topic::where('subject_id', $subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = Topic::first()->id;

                $this->attachments = Attachment::where('attachable_id', $this->topic)->get();
            } else {
                $this->topic = null;
            }
        } else {
            $this->topics = [];
        }
    }

    #[On('topicCreated')]
    public function updatedTopic($topic = null)
    {
        $this->topics = Topic::where('subject_id', $this->subject)->get();
        $this->attachments = Attachment::where('attachable_id', $this->topic)->get();
    }

    #[On('assignmentCreated')]
    public function updatedAssignment($assignment = null)
    {
        $this->attachments = Attachment::where('attachable_id', $this->topic)->get();
    }
}; ?>

<flux:modal name="create-summary" class="space-y-6 md:w-96">
    <div>
        <flux:heading size="lg">New summary</flux:heading>
        <flux:subheading>Create a new summary.</flux:subheading>
    </div>

    <flux:select label="Summary subject" searchable variant="listbox" wire:model.live="subject"
        placeholder="Select subject">
        @forelse($subjects as $item)
            <flux:select.option value="{{ $item->id }}">
                {{ $item->name }}
            </flux:select.option>
        @empty
            <flux:select.option disabled>Create a new subject first</flux:select.option>
        @endforelse
    </flux:select>

    <flux:select label="Summary topic" searchable variant="listbox" wire:model.live="topic" placeholder="Select topic">
        @forelse($topics as $topic)
            <flux:select.option value="{{ $topic->id }}">{{ $topic->name }}
            </flux:select.option>
        @empty
            <flux:select.option disabled>Create a topic first</flux:select.option>
        @endforelse
    </flux:select>

    <flux:select label="Summary attachments" searchable variant="listbox" wire:model="attachments"
        placeholder="Select attachments">
        @forelse($attachments as $attachment)
            <flux:select.option value="{{ $attachment->id }}">{{ $attachment->file_path }}
            </flux:select.option>
        @empty
            <flux:select.option disabled>Create an attachment first</flux:select.option>
        @endforelse
    </flux:select>

    <div class="flex">
        <flux:spacer />

        <flux:button type="submit" variant="primary">Create summary</flux:button>
    </div>
</flux:modal>

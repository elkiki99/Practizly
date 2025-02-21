<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    public string $name = '';
    public $type = 'open_ended';
    public $size = 'medium';

    public $subjects = [];
    public $subject;
    public $topics = [];
    public $topic;
    
    public function mount()
    {
        $this->subjects = Subject::all();
        $this->topics = Topic::all();
    }

    #[On('subjectCreated')]
    public function updatedSubject($subject = null)
    {
        $this->subjects = Subject::all();

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

        if (!empty($topic)) {
            $this->subject = Topic::find($topic)->subject_id;
            $this->topics = Topic::where('subject_id', $this->subject)->get();
        }
    }

    public function createExam()
    {
        
    }
}; ?>

<form wire:submit.prevent="createExam">
    <flux:modal name="create-exam" class="space-y-6 md:w-96" x-data="{ createTopic: false }" x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
        <div>
            <flux:heading size="lg">New exam prep</flux:heading>
            <flux:subheading>Generate a new AI exam.</flux:subheading>
        </div>

        <flux:select searchable variant="listbox" wire:model.live="subject" placeholder="Select subject">
            @forelse($subjects as $item)
                <flux:select.option wire:key="{{ $item->id }}" value="{{ $item->id }}">
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

            <flux:select searchable variant="listbox" wire:model.live="topic" placeholder="Select topic">
                @forelse($topics as $topic)
                    <flux:select.option wire:key='{{ $topic->id }}' value="{{ $topic->id }}">{{ $topic->title }}
                    </flux:select.option>
                @empty
                    <flux:select.option disabled>No topics found</flux:select.option>
                @endforelse
            </flux:select>
        </flux:field>

        @if ($subject)
            <livewire:topics.create :subject_id="$subject" />
        @endif

        <flux:radio.group wire:model="type" label="Select your exam type">
            <flux:radio label="Open ended" value="open_ended" checked />
            <flux:radio label="Multiple choice" value="multiple_choice" />
            <flux:radio label="True or false" value="true_or_false" />
        </flux:radio.group>

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Create exam</flux:button>
        </div>
    </flux:modal>
</form>

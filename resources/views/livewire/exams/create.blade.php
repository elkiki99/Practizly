<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Exam;

new class extends Component {
    #[Validate('required|string|max:30')]
    public string $type = 'open_ended';

    #[Validate('required|string|max:30')]
    public string $difficulty = 'easy';

    #[Validate('required|string|max:30')]
    public string $size = 'short';

    public $subjects = [];
    public $subject;
    public $topics = [];
    public $topic;

    #[On('topicCreated')]
    #[On('subjectCreated')]
    public function mount()
    {
        $this->subjects = Subject::all();

        if($this->subjects->count() === 1) {
            $this->subject = Subject::first()->id;
        }

        $this->topics = Topic::all();

        if($this->topics->count() === 1) {
            $this->topic = Topic::first()->id;
        }
    }

    #[On('subjectCreated')]
    public function updatedSubject($subject = null)
    {
        $this->subjects = Subject::all();

        if($this->subjects->count() === 1) {
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

        if($this->topics->count() === 1) {
            $this->topic = Topic::first()->id;
        }
        
        if (!empty($topic)) {
            $this->subject = Topic::find($topic)->subject_id;
            $this->topics = Topic::where('subject_id', $this->subject)->get();
        }
    }

    public function createExam()
    {
        $this->validate();

        $subject = Subject::find($this->subject);

        $exam = Exam::create([
            'title' => $subject->name . ' Test',
            'type' => $this->type,
            'difficulty' => $this->difficulty,
            'size' => $this->size,
        ]);

        $exam->update([
            'title' => $subject->name . ' Test #' . $exam->id,
        ]);

        $this->reset(['type', 'difficulty', 'size']);

        $this->dispatch('examCreated');

        Flux::toast(heading: 'Exam created', text: 'Your exam was created successfully', variant: 'success');

        $this->modal('create-exam')->close();
    }
}; ?>

<form wire:submit.prevent="createExam">
    <flux:modal name="create-exam" class="w-[90%] space-y-6 md:w-96" x-data="{ createTopic: false }" x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
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
            <livewire:exams.topics.create :subject_id="$subject" />
        @endif

        <!-- Difficulty -->
        <flux:radio.group wire:model="difficulty" label="Difficulty" variant="segmented">
            <flux:radio value="easy" label="Easy" icon="" />
            <flux:radio value="medium" label="Medium" icon="" />
            <flux:radio value="hard" label="Hard" icon="" />
        </flux:radio.group>

        <!-- Type -->
        <flux:radio.group wire:model="type" label="Select your exam type">
            <flux:radio label="Open ended" value="open_ended" />
            <flux:radio label="Multiple choice" value="multiple_choice" />
            <flux:radio label="True or false" value="true_or_false" />
        </flux:radio.group>

        <!-- Size -->
        <flux:radio.group wire:model='size' label="Size" class="flex-col">
            <flux:radio value="short" label="Short" description="Between 5 and 10 questions" />
            <flux:radio value="medium" label="Medium" description="Between 10 and 15 questions" />
            <flux:radio value="long" label="Long" description="Between 15 and 30 questions" />
        </flux:radio.group>

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Generate exam</flux:button>
        </div>
    </flux:modal>
</form>

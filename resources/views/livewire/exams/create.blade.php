<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Exam;

new class extends Component {
    #[Validate('required|string|in:open_ended,multiple_choice,true_or_false,quiz')]
    public string $type = 'open_ended';

    #[Validate('required|string|in:easy,medium,hard')]
    public string $difficulty = 'easy';

    #[Validate('required|in:short,medium,long')]
    public string $size = 'short';

    #[Validate('required|exists:topics,id')]
    public $topic;
    
    #[Validate('required|exists:subjects,id')]
    public $subject;
    
    public $topics = [];
    public $subjects = [];

    #[On('subjectCreated')]
    public function mount()
    {
        $this->subjects = Subject::where('user_id', auth()->user()->id)->get();

        if ($this->subjects->count() === 1) {
            $this->subject = Subject::first()->id;

            $this->topics = Topic::where('subject_id', $this->subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = Topic::first()->id;
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
                $this->topic = $this->topics->first()->id;
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
    }

    public function createExam()
    {
        $this->validate();

        $subject = Subject::find($this->subject);
        $topics = Topic::find($this->topic);

        $exam = Exam::create([
            'subject_id' => $this->subject,
            'title' => $subject->name . ' Test',
            'type' => $this->type,
            'difficulty' => $this->difficulty,
            'size' => $this->size,
        ]);

        $exam->update([
            'title' => $subject->name . ' Test #' . $exam->id,
        ]);

        $exam->topics()->sync($this->topic);

        $this->reset();

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

        <flux:select label="Exam subject" searchable variant="listbox" wire:model.live="subject" placeholder="Select subject">
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
                <flux:button as="link" size="xs" variant="subtle"
                    icon-trailing="plus" x-on:click="createTopic = true">New topic</flux:button>
            </div>

            <flux:select multiple searchable variant="listbox" selected-suffix="{{ __('topics selected') }}"
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

        <!-- Difficulty -->
        <flux:radio.group wire:model="difficulty" label="Difficulty" variant="segmented">
            <flux:radio value="easy" label="Easy" icon="check-circle" />
            <flux:radio value="medium" label="Medium" icon="stop-circle" />
            <flux:radio value="hard" label="Hard" icon="exclamation-circle" />
        </flux:radio.group>

        <!-- Type -->
        <flux:radio.group wire:model="type" label="Select your exam type">
            <flux:radio label="Open ended" value="open_ended" />
            <flux:radio label="Multiple choice" value="multiple_choice" />
            <flux:radio label="True or false" value="true_or_false" />
            <flux:radio label="Quiz" value="quiz" />
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

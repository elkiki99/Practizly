<?php

use Livewire\Volt\Component;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    public string $name = '';
    public $exam_type = 'open_ended';

    public $subjects = [];
    public $subject;
    public $topics = [];
    public $topic;

    public function mount()
    {
        // Cargar todos los subjects
        $this->subjects = Subject::all();

        // Si ya hay un topic seleccionado, cargar el subject correspondiente
        if ($this->topic) {
            $this->subject = Topic::find($this->topic)->subject_id;
            $this->topics = Topic::where('subject_id', $this->subject)->get();
        } else {
            $this->topics = Topic::all();
        }
    }

    // Cuando se actualiza el subject, actualizamos los topics
    public function updatedSubject($subject)
    {
        // Si se selecciona un subject válido, actualizamos los topics
        if (!empty($subject)) {
            $this->topics = Topic::where('subject_id', $subject)->get();
            $this->topic = null; // Reseteamos el topic seleccionado
        } else {
            $this->topics = [];
        }
    }

    // Cuando se actualiza el topic, actualizamos el subject correspondiente
    public function updatedTopic($topic)
    {
        if (!empty($topic)) {
            // Encuentra el subject asociado con el topic seleccionado
            $this->subject = Topic::find($topic)->subject_id;
            $this->topics = Topic::where('subject_id', $this->subject)->get(); // Actualizamos los topics
        }
    }
}; ?>

<flux:modal name="create-exam" class="space-y-6 md:w-96">
    <div>
        <flux:heading size="lg">New exam prep</flux:heading>
        <flux:subheading>Generate a new AI exam.</flux:subheading>
    </div>

    <flux:select label="Exam subject" searchable variant="listbox" wire:model.live="subject" placeholder="Select subject">
        @forelse($subjects as $subject)
            <flux:select.option value="{{ $subject->id }}">{{ $subject->name }}</flux:select.option>
        @empty
            <flux:select.option disabled>No subjects found</flux:select.option>
        @endforelse
    </flux:select>

    <flux:select label="Exam topic" searchable variant="listbox" wire:model.live="topic" placeholder="Select topic">
        @forelse($topics as $topic)
            <flux:select.option value="{{ $topic->id }}">{{ $topic->title }}</flux:select.option>
        @empty
            <flux:select.option disabled>No topics found</flux:select.option>
        @endforelse
    </flux:select>

    <flux:radio.group wire:model="exam_type" label="Select your exam type">
        <flux:radio label="Open ended" value="open_ended" checked />
        <flux:radio label="Multiple choice" value="multiple_choice" />
        <flux:radio label="True or false" value="true_or_false" />
    </flux:radio.group>

    <div class="flex">
        <flux:spacer />
        <flux:button type="submit" variant="primary">Create exam</flux:button>
    </div>
</flux:modal>

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Topic;

new class extends Component {
    #[Validate('required|exists:subjects,id')]
    public $subject;

    #[Validate('required|string|max:255')]
    public string $name;

    public $subjects = [];

    public function mount()
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();

        if ($this->subjects->count() === 1) {
            $this->subject = $this->subjects->first()->id;
        }
    }

    public function createTopic()
    {
        $this->validate();

        Topic::create([
            'subject_id' => $this->subject,
            'name' => $this->name,
        ]);

        Flux::toast(heading: 'Topic created', text: 'Your topic was created successfully', variant: 'success');

        $this->modal('original-create-topic')->close();

        $this->reset('name');

        $this->dispatch('topicCreated');
    }
}; ?>

<form wire:submit.prevent="createTopic">
    <flux:modal variant="flyout" name="original-create-topic" class="space-y-6 w-96">
        <div>
            <flux:heading size="lg">New topic</flux:heading>
            <flux:subheading>Create a new topic.</flux:subheading>
        </div>

        <!-- Name -->
        <flux:input label="Topic name" placeholder="Algebra" wire:model='name' autofocus required autocomplete="name" />

        <!-- Subject -->
        <flux:select required label="Topic subject" searchable variant="listbox" wire:model.live="subject"
            placeholder="Select subject">
            @forelse($subjects as $subject)
                <flux:select.option value="{{ $subject->id }}">
                    {{ $subject->name }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>Create a new subject first</flux:select.option>
            @endforelse
        </flux:select>

        <div class="flex">
            <flux:spacer />
            <flux:button variant="primary" type="submit">Create topic</flux:button>
        </div>
    </flux:modal>
</form>

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Reactive;
use App\Models\Topic;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|exists:subjects,id')]
    #[Reactive]
    public $subject_id = null;

    public function mount($subject_id)
    {
        $this->subject_id = $subject_id;
    }

    public function createTopic()
    {
        $this->validate();

        $topic = Topic::create([
            'subject_id' => $this->subject_id,
            'name' => $this->name,
        ]);

        $this->reset('name');

        $this->dispatch('topicCreated');

        Flux::toast(heading: 'Topic created', text: 'Your topic was created successfully', variant: 'success');
    }
}; ?>

<flux:field x-show="createTopic">
    <flux:label class="mb-2">New topic</flux:label>
    <div class="flex items-center gap-2 mb-3">
        <flux:input placeholder="Quantum mechanics" @keydown.enter="createTopic = false" wire:model="name">
        </flux:input>

        <flux:button class="px-2" variant="ghost" wire:click.prevent='createTopic' icon="plus">
        </flux:button>
    </div>

    <flux:error name="name" />
</flux:field>

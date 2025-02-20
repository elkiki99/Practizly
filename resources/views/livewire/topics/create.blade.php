<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Topic;

new class extends Component {
    
    #[Validate('required|string|max:255')]
    public string $title = '';

    public function createTopic()
    {
        $this->validate();

        $topic = Topic::create([
            'title' => $this->title,
        ]);

        $this->title = '';

        $this->dispatch('topicCreated');

        Flux::toast(heading: 'Topic created', text: 'Your topic was created successfully', variant: 'success');
    }
}; ?>

<flux:field x-show="createTopic">
    <flux:label class="mb-2">New topic</flux:label>
    <div class="flex items-center gap-2 mb-3">
        <flux:input placeholder="Quantum mechanics" @keydown.enter="createTopic = false" wire:model="title">
        </flux:input>

        <flux:button class="px-1" variant="ghost" type="submit" wire:click='createTopic' icon="plus">
        </flux:button>
    </div>

    <flux:error name="title" />
</flux:field>

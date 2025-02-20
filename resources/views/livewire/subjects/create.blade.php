<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:1000')]
    public string $description = '';

    #[Validate('required|string|max:50')]
    public string $color = '';

    #[Validate('boolean')]
    public bool $is_favorite = false;

    #[Validate('required|integer|exists:topics,id', as: 'topic')]
    public ?int $topicId = null;

    public $topics;

    #[On('topicCreated')]
    public function mount()
    {
        $this->topics = Topic::all();
    }

    public function createSubject()
    {
        $this->validate();

        $subject = Subject::create([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'is_favorite' => $this->is_favorite,
        ]);

        if ($this->topicId) {
            Topic::where('id', $this->topicId)->update(['subject_id' => $subject->id]);
        }

        $this->reset(['name', 'description', 'color', 'is_favorite', 'topicId']);

        Flux::toast(heading: 'Subject created', text: 'Your subject was created successfully', variant: 'success');

        $this->modal('create-subject')->close();
    }
}; ?>

<form wire:submit.prevent="createSubject">
    <flux:modal name="create-subject" class="space-y-6 md:w-96" x-data="{ createTopic: false }"
        x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
        <div>
            <flux:heading size="lg">New subject</flux:heading>
            <flux:subheading>Create a new subject.</flux:subheading>
        </div>

        <!-- Name -->
        <flux:input label="Subject name" placeholder="Physics" wire:model='name' autofocus required autocomplete="name" />

        <!-- Description -->
        <flux:input label="Subject description" placeholder="Physics - 1st semester 2025" wire:model='description'
            autofocus required />

        <flux:field>
            <div class="flex items-center justify-between mb-2">
                <flux:label>Subject topic</flux:label>
                <flux:button as="link" size="sm" variant="subtle" icon-trailing="plus"
                    x-on:click="createTopic = true">New topic</flux:button>
            </div>

            <flux:select variant="listbox" searchable placeholder="Select topic" wire:model="topicId">
                @forelse($topics as $topic)
                    <flux:select.option value="{{ $topic->id }}">{{ $topic->title }}</flux:select.option>
                @empty
                    <flux:select.option disabled>Create a new topic</flux:select.option>
                @endforelse
            </flux:select>

            <flux:error name="topicId" />
        </flux:field>

        <livewire:topics.create />

        <!-- Colors -->
        <flux:field>
            <flux:label class="mb-2">Subject color</flux:label>

            <flux:select wire:model="color" variant="listbox" placeholder="Select color">
                <flux:select.option value="green"><span
                        class="inline-block mr-2 bg-green-500 rounded-full size-2"></span>Green</flux:select.option>
                <flux:select.option value="red"><span
                        class="inline-block mr-2 bg-red-500 rounded-full size-2"></span>Red
                </flux:select.option>
                <flux:select.option value="blue"><span
                        class="inline-block mr-2 bg-blue-500 rounded-full size-2"></span>Blue</flux:select.option>
                <flux:select.option value="yellow"><span
                        class="inline-block mr-2 bg-yellow-500 rounded-full size-2"></span>Yellow</flux:select.option>
                <flux:select.option value="orange"><span
                        class="inline-block mr-2 bg-orange-500 rounded-full size-2"></span>Orange</flux:select.option>
                <flux:select.option value="purple"><span
                        class="inline-block mr-2 bg-purple-500 rounded-full size-2"></span>Purple</flux:select.option>
                <flux:select.option value="black"><span
                        class="inline-block mr-2 bg-black rounded-full size-2"></span>Black
                </flux:select.option>
                <flux:select.option value="gray"><span
                        class="inline-block mr-2 bg-gray-500 rounded-full size-2"></span>Gray</flux:select.option>
            </flux:select>

            <flux:error name="color" />
        </flux:field>

        <!-- Favourites -->
        <flux:field variant="inline" class="flex justify-between w-full">
            <flux:switch wire:model.live="is_favorite" />
            <flux:label>Mark as favorite</flux:label>
            <flux:error name="is_favorite" />
        </flux:field>

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Create subject</flux:button>
        </div>
    </flux:modal>
</form>

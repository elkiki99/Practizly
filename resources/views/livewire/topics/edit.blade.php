<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Topic;

new class extends Component {
    public ?Topic $topic;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|exists:subjects,id')]
    public $subject;

    public $subjects = [];

    public function mount(Topic $topic)
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();
        $this->topic = $topic;
        $this->name = $topic->name;
        $this->subject = $topic->subject_id;
    }

    public function editTopic()
    {
        $this->validate();

        $this->topic->update([
            'name' => $this->name,
            'subject_id' => $this->subject,
        ]);

        Flux::toast(heading: 'Topic updated', text: 'Your topic was updated successfully', variant: 'success');

        Flux::modals()->close();
        
        $this->dispatch('topicUpdated');
    }
}; ?>

<form wire:submit.prevent="editTopic">
    <flux:modal variant="flyout" name="edit-topic-{{ $topic->id }}" class="space-y-6 w-96">
        <div>
            <flux:heading size="lg">Edit topic</flux:heading>
            <flux:subheading>Edit {{ Str::of($topic->name)->ucfirst() }} topic.</flux:subheading>
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
            <flux:button variant="primary" type="submit">Edit topic</flux:button>
        </div>
    </flux:modal>
</form>

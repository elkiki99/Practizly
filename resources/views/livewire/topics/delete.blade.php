<?php

use Livewire\Volt\Component;
use App\Models\Topic;

new class extends Component {
    public ?Topic $topic;

    public function mount(Topic $topic)
    {
        $this->topic = $topic;
    }

    public function deleteTopic()
    {
        $this->topic->delete();

        Flux::toast(heading: 'Topic deleted', text: 'Your topic was deleted successfully', variant: 'danger');

        $this->dispatch('topicDeleted');

        Flux::modals()->close();

    }
}; ?>

<form wire:submit.prtopic="deleteTopic">
    <flux:modal name="delete-topic-{{ $topic->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete topic?</flux:heading>

                <flux:subheading>
                    <p>You're about to delete the topic {{ $topic->name }}.</p>
                    <p>All assignments associated with this topic will be deleted.</p>
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger">Delete topic</flux:button>
            </div>
        </div>
    </flux:modal>
</form>

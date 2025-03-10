<?php

use Livewire\Volt\Component;
use App\Models\Event;

new class extends Component {
    public ?Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function deleteEvent()
    {
        $this->event->delete();

        Flux::toast(heading: 'Event deleted', text: 'Your event was deleted successfully', variant: 'danger');

        $url = request()->header('Referer');

        if ($url === url()->route('calendar', [Auth::user()->username])) {
            $this->dispatch('eventDeleted');
            Flux::modals()->close();
        } else {
            Flux::modals()->close();
            $this->redirectRoute('calendar', [Auth::user()->username], navigate: true);
        }
    }
}; ?>

<form wire:submit.prevent="deleteEvent">
    <flux:modal name="delete-event-{{ $event->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete event?</flux:heading>

                <flux:subheading>
                    <p>You're about to delete the event {{ $event->name }}.</p>
                    <p>Are you sure you want to proceed?</p>
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger">Delete event</flux:button>
            </div>
        </div>
    </flux:modal>
</form>

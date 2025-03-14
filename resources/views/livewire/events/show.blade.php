<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Event;
use Carbon\Carbon;

new #[Layout('layouts.dashboard-component')] #[Title('Events • Practizly')] class extends Component {
    public ?Event $event;

    public function mount($slug, Event $event)
    {
        $this->event = Event::where('slug', $slug)->first();
    }

    #[On('eventCreated')]
    #[On('eventUpdated')]
    #[On('eventDeleted')]
    public function updatedEvent()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="space-y-3">
            <flux:heading level="1" size="xl">
                {{ Str::of($event->name)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate
                    href="/{{ Auth::user()->username }}/subjects/{{ $event->subject->slug }}">
                    {{ Str::of($event->subject->name)->ucfirst() }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate
                    href="/{{ Auth::user()->username }}/subjects/{{ $event->subject->slug }}/events">Events
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ Str::of($event->name)->ucfirst() }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="edit-event-{{ $event->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="pencil-square" size="lg">Edit
                    event
                </flux:badge>
            </flux:modal.trigger>

            <flux:modal.trigger name="delete-event-{{ $event->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="trash" size="lg">Delete event
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <livewire:subjects.components.nav-bar :subject="$event->subject" />

    <!-- Subject card -->
    <flux:card class="flex flex-col items-stretch flex-grow h-full space-y-6 w-1/3"
        wire:key="event-{{ $event->id }}">
        <!-- Description -->
        <flux:heading>{{ $event->name }}</flux:heading>

        <flux:subheading>{{ $event->subject->name }}</flux:subheading>

        <div class="flex items-center space-x-2">
            <flux:badge variant="subtle">
                {{ Carbon::parse($event->date)->format('M d, Y') }}
            </flux:badge>
            <flux:badge variant="outline">
                {{ Str::of($event->type)->ucfirst() }}
            </flux:badge>
        </div>
    </flux:card>

    <!-- Update subject modal -->
    <livewire:events.edit :$event wire:key="edit-event-{{ $event->id }}" />

    <!-- Delete event modal -->
    <livewire:events.delete :$event wire:key="delete-event-{{ $event->id }}" />
</div>

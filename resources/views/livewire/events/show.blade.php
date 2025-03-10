<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Event;

new #[Layout('layouts.dashboard-component')] #[Title('Events • Practizly')] class extends Component {
    public ?Event $event;

    public function mount($slug, Event $event)
    {
        $this->event = Event::where('slug', $slug)->first();
    }

    #[On('eventUpdated')]
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
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/calendar">Events
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
    <flux:card
        class="flex flex-col items-stretch flex-grow h-full space-y-6 w-1/3 p-4 border border-gray-200 dark:border-gray-700 rounded-xl shadow-md bg-white dark:bg-gray-900">
        <!-- Event Header -->
        <div>
            <div class="flex items-center justify-between">
                {{-- <flux:link href="{{ route('events.show', $event->slug) }}" size="lg">
                    {{ Str::ucfirst($event->name) }}
                </flux:link> --}}
                <span class="inline-block ml-2 size-2 bg-{{ $event->color }}-500 rounded-full"></span>
            </div>
            <flux:subheading class="text-sm text-gray-500 dark:text-gray-400">
                {{ $event->description }}
            </flux:subheading>
        </div>

        <!-- Event Meta (Date & Type) -->
        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300">
            <flux:badge variant="subtle">
                {{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}
            </flux:badge>
            <flux:badge variant="outline" class="capitalize">
                {{ $event->type }}
            </flux:badge>
        </div>

        <!-- Subject & Topics -->
        <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Subject:
                {{-- <flux:link href="{{ route('subjects.show', $event->subject->slug ?? '#') }}">
                    {{ $event->subject->name ?? 'No subject' }}
                </flux:link> --}}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Topics:
                @foreach ($event->topics as $topic)
                    <flux:badge variant="soft" class="mr-1">
                        {{ $topic->name }}
                    </flux:badge>
                @endforeach
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between mt-auto">
            {{-- <flux:button variant="outline" size="sm" href="{{ route('events.edit', $event->slug) }}">
                Edit
            </flux:button>
            <flux:button variant="primary" size="sm" href="{{ route('events.show', $event->slug) }}">
                View Details
            </flux:button> --}}
        </div>
    </flux:card>

    <!-- Update subject modal -->
    <livewire:events.edit :$event wire:key="edit-event-{{ $event->id }}" />

    <!-- Delete event modal -->
    <livewire:events.delete :$event wire:key="delete-event-{{ $event->id }}" />
</div>

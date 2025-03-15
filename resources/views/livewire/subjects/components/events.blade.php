<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Subject;
use App\Models\Event;
use Carbon\Carbon;

new #[Layout('layouts.dashboard-component')] #[Title('Events • Practizly')] class extends Component {
    public ?Subject $subject;

    public function mount($slug, Subject $subject)
    {
        $this->subject = Subject::where('slug', $slug)->first();
    }

    public function with()
    {
        return [
            'events' => $this->subject->events()->orderBy('created_at', 'desc')->paginate(12),
        ];
    }

    #[On('eventCreated')]
    #[On('eventUpdated')]
    #[On('eventDeleted')]
    public function updatedEvents()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div class="space-y-3">
            <flux:heading level="1" size="xl">
                {{ Str::of($subject->name)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">
                    {{ Str::of($subject->name)->ucfirst() }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Events</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="create-event">
                <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New event
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <livewire:subjects.components.nav-bar :subject="$subject" />

    <div class="space-y-6">
        <flux:table :paginate="$events">
            <flux:table.columns>
                <flux:table.column>Event</flux:table.column>
                <flux:table.column sortable>Date</flux:table.column>
                <flux:table.column class="hidden md:table-cell">Topics</flux:table.column>
                <flux:table.column class="hidden sm:table-cell" sortable>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($events as $event)
                    <flux:table.row wire:key="event-{{ $event->id }}">
                        <!-- Icon type & name -->
                        <flux:table.cell variant="strong" class="flex items-center space-x-2 whitespace-nowrap">
                            @if ($event->type === 'test')
                                <flux:icon.document-text variant="mini" inset="top bottom" />
                            @elseif($event->type === 'exam')
                                <flux:icon.book-open variant="mini" inset="top bottom" />
                            @elseif($event->type === 'evaluation')
                                <flux:icon.clipboard-document-check variant="mini" inset="top bottom" />
                            @elseif($event->type === 'oral_presentation')
                                <flux:icon.microphone variant="mini" inset="top bottom" />
                            @elseif($event->type === 'assignment')
                                <flux:icon.clipboard-document-list variant="mini" inset="top bottom" />
                            @endif
                            <flux:link class="text-sm font-medium text-zinc-800 dark:text-white whitespace-nowrap"
                                wire:navigate href="/{{ Auth::user()->username }}/events/{{ $event->slug }}">
                                {{ Str::of($event->name)->ucfirst() }}</flux:link>
                        </flux:table.cell>

                        <!-- Date -->
                        <flux:table.cell class="whitespace-nowrap">{{ Carbon::parse($event->date)->format('F j, Y') }}
                        </flux:table.cell>

                        <!-- Topics -->
                        <flux:table.cell class="hidden md:table-cell">
                            @php
                                $topicsToShow = $event->topics->take(2);
                                $hasMoreTopics = $event->topics->count() > 2;
                            @endphp

                            @foreach ($topicsToShow as $topic)
                                <flux:badge inset="top bottom" size="sm" color="zinc">{{ $topic->name }}
                                </flux:badge>
                            @endforeach

                            @if ($hasMoreTopics)
                                <flux:badge inset="top bottom" size="sm" color="zinc">+ {{ $event->topics->count() - 2 }} more
                                </flux:badge>
                            @endif
                        </flux:table.cell>

                        <!-- Status -->
                        <flux:table.cell class="hidden sm:table-cell">
                            @if ($event->status === 'pending')
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            @elseif($event->status === 'completed')
                                <flux:badge size="sm" color="green" inset="top bottom">Completed</flux:badge>
                            @endif
                        </flux:table.cell>

                        <!-- Actions -->
                        <flux:table.cell>
                            <div class="flex justify-end items-end space-x-2">
                                <flux:modal.trigger name="edit-event-{{ $event->id }}">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom">
                                    </flux:button>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-event-{{ $event->id }}">
                                    <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom">
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>

                            <!-- Edit event modal -->
                            <livewire:events.edit :$event wire:key="edit-event-{{ $event->id }}" />

                            <!-- Delete event modal -->
                            <livewire:events.delete :$event wire:key="delete-event-{{ $event->id }}" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row class="text-center">
                        <flux:table.cell colspan="4">You don't have any events yet.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Modal actions -->
    <livewire:events.create />
</div>

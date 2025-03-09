<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Carbon\Carbon;
use App\Models\Event;

new #[Layout('layouts.dashboard')] #[Title('Calendar • Practizly')] class extends Component {
    use WithPagination;

    public $dates = [];

    public function mount()
    {
        $this->loadDates();
    }

    public function with()
    {
        return [
            'events' => Event::whereHas('topics.subject', function ($query) {
                $query->whereIn('id', Auth::user()->subjects()->pluck('id'));
            })
                ->orderBy('date', 'asc')
                ->paginate(12),
        ];
    }

    public function loadDates()
    {
        $this->dates = Event::whereHas('topics.subject', function ($query) {
            $query->whereIn('id', Auth::user()->subjects()->pluck('id'));
        })
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->toArray();
    }

    #[On('eventCreated')]
    #[On('eventUpdated')]
    #[On('eventDeleted')]
    public function updatedEventsAndDates()
    {
        $this->loadDates();
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="space-y-3">
        <flux:heading level="1" size="xl">Events</flux:heading>

        <flux:breadcrumbs>
            <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Events
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <flux:separator variant="subtle" />

    <!-- Panel navbar -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2">
                <flux:select variant="listbox" class="sm:max-w-fit">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.funnel variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>

                    <flux:select.option value="all" selected>All</flux:select.option>
                    <flux:select.option value="tests">Tests</flux:select.option>
                    <flux:select.option value="evaluations">Evaluations</flux:select.option>
                    <flux:select.option value="oral_presentations">Oral presentations</flux:select.option>
                    <flux:select.option value="assignments">Assignments</flux:select.option>
                </flux:select>

                <flux:select variant="listbox" class="sm:max-w-fit">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>

                    <flux:select.option value="status" selected>Status</flux:select.option>
                    <flux:select.option value="pending">Pending</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                </flux:select>
            </div>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2 max-lg:hidden">
                <flux:modal.trigger name="create-event">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New event
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div>
        <flux:calendar min="today" static fixed-weeks multiple wire:model='dates' />
    </div>

    <div class="space-y-6">
        <div>
            <flux:heading level="2">Next events</flux:heading>
            <flux:subheading>Check out your upcoming events.</flux:subheading>
        </div>

        <flux:table :paginate="$events">
            <flux:table.columns>
                <flux:table.column>Event</flux:table.column>
                <flux:table.column sortable>Date</flux:table.column>
                <flux:table.column>Tags</flux:table.column>
                <flux:table.column sortable>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($events as $event)
                    <flux:table.row wire:key="event-{{ $event->id }}">
                        <!-- Icon type & name -->
                        <flux:table.cell class="flex items-center space-x-2 whitespace-nowrap">
                            @if ($event->type === 'test')
                                <flux:icon.document-text variant="mini" inset="top bottom" />
                            @elseif($event->type === 'exam')
                                <flux:icon.book-open variant="mini" inset="top bottom" />
                            @elseif($event->type === 'evaluation')
                                <flux:icon.clipboard-document-check variant="mini" inset="top bottom" />
                            @elseif($event->type === 'oral_presentation')
                                <flux:icon.microphone variant="mini" inset="top bottom" />
                            @elseif($event->type === 'assignment')
                                <flux:icon.pencil-square variant="mini" inset="top bottom" />
                            @endif
                            <flux:link class="text-sm text-zinc-500 dark:text-zinc-300 whitespace-nowrap" wire:navigate
                                href="/{{ Auth::user()->username }}/events/{{ $event->slug }}">
                                {{ Str::of($event->name)->ucfirst() }}</flux:link>
                        </flux:table.cell>

                        <!-- Date -->
                        <flux:table.cell class="whitespace-nowrap">{{ Carbon::parse($event->date)->format('F j, Y') }}
                        </flux:table.cell>

                        <!-- Topics -->
                        <flux:table.cell>
                            @php
                                $topicsToShow = $event->topics->take(2);
                                $hasMoreTopics = $event->topics->count() > 2;
                            @endphp

                            @foreach ($topicsToShow as $topic)
                                <flux:badge size="sm" color="zinc">{{ $topic->name }}
                                </flux:badge>
                            @endforeach

                            @if ($hasMoreTopics)
                                <flux:badge size="sm" color="zinc">+ {{ $event->topics->count() - 2 }} more</flux:badge>
                            @endif
                        </flux:table.cell>

                        <!-- Status -->
                        <flux:table.cell>
                            @if ($event->status === 'pending')
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            @elseif($event->status === 'completed')
                                <flux:badge size="sm" color="green" inset="top bottom">Completed</flux:badge>
                            @endif
                        </flux:table.cell>

                        <!-- Actions -->
                        <flux:table.cell>
                            <flux:modal.trigger name="edit-event-{{ $event->id }}">
                                <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom">
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal.trigger name="delete-event-{{ $event->id }}">
                                <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom">
                                </flux:button>
                            </flux:modal.trigger>

                            <!-- Edit event modal -->
                            <livewire:events.edit :$event wire:key="edit-event-{{ $event->id }}" />

                            <!-- Delete event modal -->
                            <livewire:events.delete :$event wire:key="delete-event-{{ $event->id }}" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">You don't have any events yet.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Modal actions -->
    <livewire:events.create />
</div>

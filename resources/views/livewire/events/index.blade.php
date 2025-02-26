<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use App\Models\Event;
use Carbon\Carbon;

new #[Layout('layouts.dashboard')] #[Title('Calendar • Practizly')] class extends Component {
    public $events;
    public $dates = [];
    // public $viewType = 'grid';

    public function mount()
    {
        $this->events = Auth::user()->events()->get();
        $this->dates = $this->events->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->unique()->toArray();
    }

    #[On('eventCreated')]
    public function updatedEvent()
    {
        $this->events = Auth::user()->events()->get();
        $this->dates = $this->events->pluck('date')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->unique()->toArray();
    }
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Calendar</flux:heading>
    <flux:separator variant="subtle" />

    <!-- Panel navbar -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

            <flux:select size="sm" class="">
                <option selected>All events</option>
                <option>Tests</option>
                <option>Exams</option>
                <option>Evaluations</option>
                <option>Oral presentations</option>
                <option>Assignments</option>
            </flux:select>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2 max-lg:hidden">
                <flux:modal.trigger name="create-event">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New calendar
                        event
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>

        <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
            <flux:tab selected value="grid" icon="squares-2x2" icon-variant="outline" />    
            <flux:tab value="table" icon="list-bullet" icon-variant="outline" />
        </flux:tabs>
    </div>

    <!-- Calendar -->
    <div>
        <flux:calendar static fixed-weeks multiple wire:model='dates' />
    </div>

    <div class="space-y-6 ">
        <div>
            <flux:heading level="2">Next events</flux:heading>
            <flux:subheading>Check out your upcoming events.</flux:subheading>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column sortable>Event</flux:table.column>
                <flux:table.column sortable>Date</flux:table.column>
                <flux:table.column sortable>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($events as $event)
                    <flux:table.row>
                        <flux:table.cell class="flex items-center gap-3 whitespace-nowrap">
                            @if ($event->type === 'test')
                                <flux:icon.document-text />
                            @elseif($event->type === 'exam')
                                <flux:icon.book-open />
                            @elseif($event->type === 'evaluation')
                                <flux:icon.clipboard-document-check />
                            @elseif($event->type === 'oral_presentation')
                                <flux:icon.microphone />
                            @elseif($event->type === 'assignment')
                                <flux:icon.pencil-square />
                            @endif
                            {{ Str::of($event->type)->ucfirst() }}
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">{{ Carbon::parse($event->date)->format('F j, Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                            </flux:button>
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

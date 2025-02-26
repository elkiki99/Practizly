<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Livewire\Attributes\On;
use Carbon\Carbon;

new #[Layout('layouts.dashboard')] #[Title('Dashboard • Practizly')] class extends Component {
    public $events;

    public function mount()
    {
        $this->events = Auth::user()->events()->take(3)->get();
    }

    #[On('eventCreated')]
    public function updatedEvents()
    {
        $this->events = Auth::user()->events()->take(3)->get();
    }
}; ?>

<div class="space-y-6">
    {{-- <div> --}}
    <flux:heading class="mb-6" size="xl">Welcome back {{ Auth::user()->name }}</flux:heading>
    <flux:separator variant="subtle" />
    {{-- </div> --}}

    <div class="space-y-12">
        <!-- Actions (overview) -->
        <div class="space-y-6">
            <div class="">
                <flux:heading level="2">Overview</flux:heading>
                <flux:subheading>Let's get you organized!</flux:subheading>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- New subject -->
                <livewire:components.modal-card :icon="'book-open'" :title="'New subject'" :subtitle="'Create and manage study subjects.'"
                    :modal-event="'create-subject'" />

                <!-- New exam -->
                <livewire:components.modal-card :icon="'academic-cap'" :title="'New exam prep'" :subtitle="'Generate AI-powered practice tests.'"
                    :modal-event="'create-exam'" />

                <!-- New event -->
                <livewire:components.modal-card :icon="'calendar'" :title="'New calendar event'" :subtitle="'Create a new calendar event.'"
                    :modal-event="'create-event'" />

                <!-- New assignment -->
                <livewire:components.modal-card :icon="'document-text'" :title="'New assignment'" :subtitle="'Create and track your tasks.'"
                    :modal-event="'create-assignment'" />

                <!-- Summaries -->
                <livewire:components.modal-card :icon="'light-bulb'" :title="'New summary'" :subtitle="'Save and organize study notes.'"
                    :modal-event="'create-summary'" />

                <!-- New attachment -->
                <livewire:components.modal-card :icon="'paper-clip'" :title="'New attachment'" :subtitle="'Attach files to your library.'"
                    :modal-event="'create-attachment'" />
            </div>

            <!-- Modal actions -->
            <div>
                <livewire:subjects.create />
                <livewire:exams.create />
                <livewire:assignments.create />
                <livewire:attachments.create />
                <livewire:summaries.create />
                <livewire:events.create />
            </div>
        </div>

        <!-- Next events (exams, assignments, etc.) -->
        <div class="space-y-6">
            <div class="">
                <div class="flex items-center gap-2 mb-2">
                    <flux:heading level="2">Next events</flux:heading>
                    <flux:button as="link" href="/{{ Auth::user()->username }}/calendar" wire:navigate icon-trailing="chevron-right"
                        size="xs" variant="ghost" />
                </div>

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

                            <flux:table.cell class="whitespace-nowrap">
                                {{ Carbon::parse($event->date)->format('F j, Y') }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
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

        <!-- Uploaded files -->
        <div class="space-y-6">
            <div class="">
                <div class="flex items-center gap-2 mb-2">
                    <flux:heading level="2">Last uploaded files</flux:heading>
                    <flux:button as="link" href="/{{ Auth::user()->username }}/library" wire:navigate icon-trailing="chevron-right"
                        size="xs" variant="ghost" />
                </div>

                <flux:subheading>Check out latest file uploads.</flux:subheading>
            </div>
        </div>
    </div>
</div>

{{-- 
<div class="space-y-6">
    <flux:heading level="1" size="xl">Welcome back {{ Auth::user()->name }}</flux:heading>
    <flux:separator variant="subtle" />
    <div class="space-y-12">
        <!-- Actions (overview) -->
        <div class="space-y-6">
            <div class="">
                <flux:heading level="2">Overview</flux:heading>
                <flux:subheading>Let's get you organized!</flux:subheading>
            </div>

            <!-- Important links (Subjects, Exams, Assignments, Calendar, Library, Summaries) -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- New subject -->
                <flux:card as="link" wire:navigate href="/{{ Auth::user()->username }}/subjects"
                    class="flex gap-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:cursor-pointer">
                    <flux:icon.book-open variant="mini" />
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:heading level="2">New subject</flux:heading>
                            <flux:icon.chevron-right variant="mini" />
                        </div>
                        <flux:subheading>Create and manage study subjects.</flux:subheading>
                    </div>
                </flux:card>

                <!-- New exam -->
                <flux:card as="link" wire:navigate href="/{{ Auth::user()->username }}/exams"
                    class="flex gap-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:cursor-pointer">
                    <flux:icon.academic-cap variant="mini" />
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:heading level="2">New exam</flux:heading>
                            <flux:icon.chevron-right variant="mini" />
                        </div>
                        <flux:subheading>Generate AI-powered practice tests.</flux:subheading>
                    </div>
                </flux:card>

                <!-- New event -->
                <flux:card as="link" wire:navigate href="/{{ Auth::user()->username }}/events"
                    class="flex gap-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:cursor-pointer">
                    <flux:icon.calendar variant="mini" />
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:heading level="2">New event</flux:heading>
                            <flux:icon.chevron-right variant="mini" />
                        </div>
                        <flux:subheading>Create a new calendar event.</flux:subheading>
                    </div>
                </flux:card>

                <!-- New assignment -->
                <flux:card as="link" wire:navigate href="/{{ Auth::user()->username }}/assignments"
                    class="flex gap-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:cursor-pointer">
                    <flux:icon.document-text variant="mini" />
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:heading level="2">New assignment</flux:heading>
                            <flux:icon.chevron-right variant="mini" />
                        </div>
                        <flux:subheading>Create and track your tasks.</flux:subheading>
                    </div>
                </flux:card>

                <!-- New summary -->
                <flux:card as="link" wire:navigate href="/{{ Auth::user()->username }}/summaries"
                    class="flex gap-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:cursor-pointer">
                    <flux:icon.light-bulb variant="mini" />
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:heading level="2">New summary</flux:heading>
                            <flux:icon.chevron-right variant="mini" />
                        </div>
                        <flux:subheading>Save and organize study notes.</flux:subheading>
                    </div>
                </flux:card>

                <!-- New attachment -->
                <flux:card as="link" wire:navigate href="/{{ Auth::user()->username }}/library"
                    class="flex gap-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:cursor-pointer">
                    <flux:icon.paper-clip variant="mini" />
                    <div class="flex flex-col items-start">
                        <div class="flex items-center gap-2 mb-2">
                            <flux:heading level="2">New attachment</flux:heading>
                            <flux:icon.chevron-right variant="mini" />
                        </div>
                        <flux:subheading>Save and organize study notes.</flux:subheading>
                    </div>
                </flux:card>
            </div>
        </div>

        <!-- Next events (exams, assignments, etc.) -->
        <div class="space-y-6">
            <div class="">
                <div class="flex items-center gap-2 mb-2">
                    <flux:heading level="2">Next events</flux:heading>
                    <flux:button as="link" href="/calendar" wire:navigate icon-trailing="chevron-right"
                        size="xs" variant="ghost" />
                </div>

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

                            <flux:table.cell class="whitespace-nowrap">
                                {{ Carbon::parse($event->date)->format('F j, Y') }}
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
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

        <!-- Uploaded files -->
        <div class="space-y-6">
            <div class="">
                <div class="flex items-center gap-2 mb-2">
                    <flux:heading level="2">Last uploaded files</flux:heading>
                    <flux:button as="link" href="/library" wire:navigate icon-trailing="chevron-right"
                        size="xs" variant="ghost" />
                </div>

                <flux:subheading>Check out latest file uploads.</flux:subheading>
            </div>
        </div>
    </div>
</div> --}}

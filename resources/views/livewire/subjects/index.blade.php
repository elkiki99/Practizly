<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use App\Models\Subject;
use Carbon\Carbon;

new #[Layout('layouts.dashboard')] #[Title('Subjects • Practizly')] class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'subjects' => Auth::user()->subjects()->latest()->paginate(12),
        ];
    }

    #[On('subjectCreated')]
    #[On('subjectUpdated')]
    #[On('subjectDeleted')]
    public function updatedSubjects()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="space-y-3">
        <flux:heading level="1" size="xl">Subjects</flux:heading>

        <flux:breadcrumbs>
            <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Subjects
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

                    <flux:select.option value="all" selected>Creation</flux:select.option>
                    <flux:select.option value="last_exam">Last exam</flux:select.option>
                    <flux:select.option value="latest_assignment">Latest assignment</flux:select.option>
                    <flux:select.option value="favorite">Favorite</flux:select.option>
                </flux:select>

                <flux:select variant="listbox" class="sm:max-w-fit">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>

                    <flux:select.option value="popular" selected>Most popular</flux:select.option>
                    <flux:select.option value="newest">Newest</flux:select.option>
                    <flux:select.option value="oldest">Oldest</flux:select.option>
                </flux:select>
            </div>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2">
                <flux:modal.trigger name="create-subject">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New subject
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div>
            <flux:heading level="2">Available subjects</flux:heading>
            <flux:subheading>Explore the subjects you are enrolled in.</flux:subheading>
        </div>

        <flux:table :paginate="$subjects">
            <flux:table.columns>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column sortable>Upcoming event</flux:table.column>
                <flux:table.column>My tests</flux:table.column>
                <flux:table.column sortable>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($subjects as $subject)
                    <flux:table.row wire:key="subject-{{ $subject->id }}">
                        <!-- Name -->
                        <flux:table.cell variant="strong" class="flex items-center space-x-2 whitespace-nowrap">
                            <flux:icon.book-open variant="mini" inset="top bottom" />
                            <flux:link class="text-sm  font-medium text-zinc-800 dark:text-white whitespace-nowrap"
                                wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">
                                {{ Str::of($subject->name)->ucfirst() }}</flux:link>
                        </flux:table.cell>

                        <!-- Upcoming event -->
                        @php
                            $upcomingEvent = $subject->events()->latest()->first() ?? null;
                        @endphp
                        <flux:table.cell class="whitespace-nowrap">
                            @if ($upcomingEvent)
                                {{ $upcomingEvent->name }} -
                                {{ Carbon::parse($upcomingEvent->date)->format('d/m') }}
                            @else
                                No events yet
                            @endif
                        </flux:table.cell>

                        <!-- My tests -->
                        <flux:table.cell class="whitespace-nowrap">
                            {{ $subject->exams()->latest()->first()->title ?? 'No tests yet' }}
                        </flux:table.cell>

                        <!-- Status -->
                        <flux:table.cell>
                            @if ($subject->status == true)
                                <flux:badge size="sm" color="yellow" inset="top bottom">Ongoing</flux:badge>
                            @elseif($subject->status == false)
                                <flux:badge size="sm" color="green" inset="top bottom">Completed</flux:badge>
                            @endif
                        </flux:table.cell>

                        <!-- Actions -->
                        <flux:table.cell>
                            <div class="flex justify-end items-end space-x-2">
                                <flux:modal.trigger name="edit-subject-{{ $subject->id }}">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom">
                                    </flux:button>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-subject-{{ $subject->id }}">
                                    <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom">
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>

                            <!-- Edit subject modal -->
                            <livewire:subjects.edit :$subject wire:key="edit-subject-{{ $subject->id }}" />

                            <!-- Delete subject modal -->
                            <livewire:subjects.delete :$subject wire:key="delete-subject-{{ $subject->id }}" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row class="text-center">
                        <flux:table.cell colspan="4">You don't have any subjects yet.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Modal actions -->
    <livewire:subjects.create />
</div>

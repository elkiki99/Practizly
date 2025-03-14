<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Assignment;
use Carbon\Carbon;

new #[Layout('layouts.dashboard')] #[Title('Assignments • Practizly')] class extends Component {
    use WithPagination;
    use WithoutUrlPagination;

    public function with()
    {
        return [
            'assignments' => Assignment::whereHas('topic.subject', function ($query) {
                $query->whereIn('id', Auth::user()->subjects()->pluck('id'));
            })
                ->with(['topic.subject'])
                ->orderBy('due_date', 'asc')
                ->paginate(12),
        ];
    }

    #[On('assignmentCreated')]
    #[On('assignmentUpdated')]
    #[On('assignmentDeleted')]
    public function updatedAssignments()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="space-y-3">
        <flux:heading level="1" size="xl">Assignments</flux:heading>
        <flux:subheading>Check out your assignments.</flux:subheading>
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

                    <flux:select.option value="due_date" selected>Due date</flux:select.option>
                    <flux:select.option value="subject">Subject</flux:select.option>
                    <flux:select.option value="topic">Topic</flux:select.option>
                    <flux:select.option value="creation">Creation</flux:select.option>
                </flux:select>

                <flux:select variant="listbox" class="sm:max-w-fit">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>

                    <flux:select.option value="pending" selected>Pending</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                </flux:select>
            </div>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2 max-lg:hidden">
                <flux:modal.trigger name="create-assignment">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New
                        assignment
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <flux:table :paginate="$assignments">
            <flux:table.columns>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column sortable>Due date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($assignments as $assignment)
                    <flux:table.row wire:key="assignment-{{ $assignment->id }}">
                        <flux:table.cell variant="strong">
                            <flux:link class="text-sm font-medium text-zinc-800 dark:text-white" wire:navigate
                                href="/{{ Auth::user()->username }}/assignments/{{ $assignment->slug }}">
                                {{ Str::of($assignment->title)->ucfirst() }}
                            </flux:link>
                        </flux:table.cell>

                        <!-- Subject -->
                        <flux:table.cell>
                            <flux:link class="text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap" wire:navigate
                                href="/{{ Auth::user()->username }}/subjects/{{ $assignment->subject->slug }}">
                                {{ $assignment->subject->name }}</flux:link>
                        </flux:table.cell>

                        <flux:table.cell class="whitespace-nowrap">
                            {{ Carbon::parse($assignment->due_date)->format('F j, Y') }}</flux:table.cell>

                        <flux:table.cell>
                            @if ($assignment->status === 'pending')
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            @elseif($assignment->status === 'completed')
                                <flux:badge size="sm" color="green" inset="top bottom">Completed</flux:badge>
                            @endif
                        </flux:table.cell>

                        <!-- Actions -->
                        <flux:table.cell>
                            <div class="flex justify-end items-end space-x-2">
                                <flux:modal.trigger name="edit-assignment-{{ $assignment->id }}">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom">
                                    </flux:button>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-assignment-{{ $assignment->id }}">
                                    <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom">
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>

                            <!-- Edit assignment modal -->
                            <livewire:assignments.edit :$assignment wire:key="edit-assignment-{{ $assignment->id }}" />

                            <!-- Delete assignment modal -->
                            <livewire:assignments.delete :$assignment
                                wire:key="delete-assignment-{{ $assignment->id }}" />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row class="text-center">
                        <flux:table.cell colspan="4">You don't have any assignments yet.</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Modal actions -->
    <livewire:assignments.create />
</div>

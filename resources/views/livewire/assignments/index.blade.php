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
                ->paginate(10),
        ];
    }

    #[On('assignmentCreated')]
    public function updatedAssignments()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="space-y-3">
        <flux:heading level="1" size="xl">Assignments</flux:heading>

        <flux:breadcrumbs>
            <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Assignments
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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
        @forelse($assignments as $assignment)
            <flux:card class="space-y-6" wire:key="assignment-{{ $assignment->id }}">
                <div>
                    <div class="flex items-center">
                        <flux:subheading>{{ Str::of($assignment->topic->name)->ucfirst() }} -
                            {{ Str::of($assignment->subject->name)->ucfirst() }}
                        </flux:subheading>

                        <flux:spacer />

                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item as="link" wire:navigate
                                    href="/{{ Auth::user()->username }}/assignments/{{ $assignment->slug }}"
                                    icon-trailing="chevron-right">Finish assignment</flux:menu.item>
                                <flux:menu.separator />

                                <flux:modal.trigger name="edit-assignment-{{ $assignment->id }}">
                                    <flux:menu.item icon="pencil-square">Edit assignment</flux:menu.item>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-assignment-{{ $assignment->id }}">
                                    <flux:menu.item variant="danger" icon="trash">Delete assignment
                                        </flux:menu.button>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        <!-- Update assignment modal -->
                        <livewire:assignments.edit :$assignment wire:key="edit-assignment-{{ $assignment->id }}" />

                        <!-- Delete assignment modal -->
                        <livewire:assignments.delete :$assignment wire:key="delete-assignment-{{ $assignment->id }}" />
                    </div>

                    <flux:heading size="lg">{{ Str::of($assignment->title)->ucfirst() }}</flux:heading>
                </div>

                <div class="space-y-3">
                    <div class="gap-3 items-center flex">
                        <flux:icon.clipboard-document variant="micro" />
                        <flux:heading>{{ Str::of($assignment->description)->ucfirst() }}</flux:heading>
                    </div>

                    <div class="gap-3 items-center flex">
                        <flux:icon.command-line variant="micro" />
                        <flux:heading>{{ $assignment->guidelines }}</flux:heading>
                    </div>

                    <div class="gap-3 items-center flex">
                        <flux:icon.paper-clip variant="micro" />
                        <flux:heading x-data="{ count: {{ $assignment->attachments->count() }} }"
                            x-text="count === 1 ? count + ' attachment' : count + ' attachments'"></flux:heading>
                    </div>

                    <div class="gap-3 items-center flex">
                        <flux:icon.clock variant="micro" />
                        <flux:heading>{{ Carbon::parse($assignment->due_date)->format('F j, Y') }}</flux:heading>
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:subheading>You don't have any assignments yet.</flux:subheading>
        @endforelse
    </div>

    <!-- Modal actions -->
    <livewire:assignments.create />
</div>

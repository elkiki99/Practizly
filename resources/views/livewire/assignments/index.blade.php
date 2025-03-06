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
            'assignments' => Auth::user()
                ->subjects()
                ->with('topics.assignments')
                ->orderBy('due_date', 'asc')
                ->get()
                ->flatMap(function ($subject) {
                    return $subject->topics->flatMap(function ($topic) {
                        return $topic->assignments->sortByDesc('due_date');
                    });
                }),
        ];
    }

    #[On('assignmentCreated')]
    public function updatedAssignments()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Assignments</flux:heading>
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

        <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
            <flux:tab selected value="grid" icon="squares-2x2" icon-variant="outline" />
            <flux:tab value="table" icon="list-bullet" icon-variant="outline" />
        </flux:tabs>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
        @forelse($assignments as $assignment)
            <flux:card>
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center">
                            <flux:subheading>{{ $assignment->topic->name }} - {{ $assignment->subject->name }}</flux:subheading>
                            <flux:spacer />
                            <flux:tooltip content="Options" position="left">
                                <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />
                            </flux:tooltip>
                        </div>

                        <flux:heading size="lg">{{ $assignment->title }}</flux:heading>
                    </div>
                    <div class="space-y-3">
                        <div class="gap-3 items-center flex">
                            <flux:icon.clipboard-document variant="micro" />
                            <flux:heading>{{ $assignment->description }}</flux:heading>
                        </div>
                        
                        <div class="gap-3 items-center flex">
                            <flux:icon.command-line variant="micro" />
                            <flux:heading>{{ $assignment->guidelines }}</flux:heading>
                        </div>
                        
                        <div class="gap-3 items-center flex">
                            <flux:icon.clock variant="micro" />
                            <flux:heading>{{ Carbon::parse($assignment->due_date)->format('F j, Y') }}</flux:heading>
                        </div>
                        
                        <div class="gap-3 items-center flex">
                            <flux:icon.paper-clip variant="micro" />
                            <flux:heading x-data="{ count: {{ $assignment->attachments->count() }} }" x-text="count === 1 ? count + ' attachment' : count + ' attachments'"></flux:heading>
                        </div>
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

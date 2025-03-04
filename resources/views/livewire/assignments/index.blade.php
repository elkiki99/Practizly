<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Assignment;

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
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

            <flux:select size="sm">
                <option selected>Creation</option>
                <option>Due date</option>
                <option>Status</option>
                <option>Subject</option>
                <option>Topic</option>
            </flux:select>

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
                        <flux:subheading>{{ $assignment->topic->subject->name }}</flux:subheading>
                        <flux:heading size="lg">{{ $assignment->title }}</flux:heading>
                    </div>
                    <div>
                        <flux:subheading>{{ $assignment->description }}</flux:subheading>
                        <flux:subheading>{{ $assignment->guidelines }}</flux:subheading>
                        <flux:subheading>{{ $assignment->due_date }}</flux:subheading>
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

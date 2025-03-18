<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Str;
use App\Models\Subject;
use Carbon\Carbon;

new #[Layout('layouts.dashboard-component')] #[Title('Assignments • Practizly')] class extends Component {
    public string $slug;

    public ?Subject $subject;

    public function mount($slug)
    {
        $this->slug = Str::slug($slug);
    }

    public function with()
    {
        $this->subject = Subject::where('slug', $this->slug)->first();

        return [
            'subject' => $this->subject,
            'assignments' => $this->subject->assignments()->orderBy('due_date', 'asc')->paginate(12),
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
    <div class="flex items-start justify-between">
        <div class="space-y-3"> 
            <flux:heading level="1" size="xl" class="text-{{ $subject->color }}">
                {{ Str::of($subject->name)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>  
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">
                    {{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Assignments</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="create-assignment">
                <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New assignment
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Header & nav bar -->
    <livewire:subjects.components.nav-bar :subject="$subject" />

    <flux:table :paginate="$assignments">
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
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
                        <livewire:assignments.delete :$assignment wire:key="delete-assignment-{{ $assignment->id }}" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row class="text-center">
                    <flux:table.cell colspan="3">You don't have any assignments yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <livewire:assignments.create />
</div>

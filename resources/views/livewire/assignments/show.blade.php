<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Assignment;
use Carbon\Carbon;

new #[Layout('layouts.dashboard-component')] #[Title('Assignments • Practizly')] class extends Component {
    public ?Assignment $assignment;

    public function mount($slug, Assignment $assignment)
    {
        $this->assignment = Assignment::where('slug', $slug)->first();
    }

    #[On('assignmentUpdated')]
    public function updatedAssignment()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="space-y-3">
            <flux:heading level="1" size="xl">
                {{ Str::of($assignment->title)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $assignment->subject->slug }}">
                    {{ Str::of($assignment->subject->name)->ucfirst() }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $assignment->subject->slug }}/assignments">Assignments
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ Str::of($assignment->title)->ucfirst() }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="edit-assignment-{{ $assignment->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="pencil-square" size="lg">Edit
                    assignment
                </flux:badge>
            </flux:modal.trigger>

            <flux:modal.trigger name="delete-assignment-{{ $assignment->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="trash" size="lg">Delete assignment
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <livewire:subjects.components.nav-bar :subject="$assignment->subject" />

    <!-- Subject card -->
    <flux:card class="space-y-6 w-1/3" wire:key="assignment-{{ $assignment->id }}">
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

    <!-- Update subject modal -->
    <livewire:assignments.edit :$assignment wire:key="edit-assignment-{{ $assignment->id }}" />

    <!-- Delete assignment modal -->
    <livewire:assignments.delete :$assignment wire:key="delete-assignment-{{ $assignment->id }}" />
</div>
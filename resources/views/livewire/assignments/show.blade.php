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
    <div class="space-y-3">
        <div class="flex items-start justify-between gap-2">
            <flux:heading level="1" size="xl">
                {{ Str::of($assignment->title)->ucfirst() }}
            </flux:heading>

            <div class="flex items-center justify-start gap-2">
                <flux:modal.trigger name="edit-assignment-{{ $assignment->id }}">
                    <flux:badge as="button" variant="pill" color="zinc" icon="pencil-square" size="lg">
                        Edit&nbsp;<span class="hidden sm:inline">assignment</span>
                    </flux:badge>
                </flux:modal.trigger>

                <flux:modal.trigger name="delete-assignment-{{ $assignment->id }}">
                    <flux:badge as="button" variant="pill" color="zinc" icon="trash" size="lg">
                        Delete&nbsp;<span class="hidden sm:inline">assignment</span>
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>

        <flux:breadcrumbs>
            <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item wire:navigate
                href="/{{ Auth::user()->username }}/subjects/{{ $assignment->subject->slug }}">
                {{ Str::of($assignment->subject->name)->ucfirst() }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item wire:navigate
                href="/{{ Auth::user()->username }}/subjects/{{ $assignment->subject->slug }}/assignments">
                Assignments
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ Str::of($assignment->title)->ucfirst() }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <livewire:subjects.components.nav-bar :subject="$assignment->subject" />

    <!-- Subject card -->
    <flux:card class="space-y-6 w-96" wire:key="assignment-{{ $assignment->id }}">
        <div>
            <flux:heading size="lg">{{ Str::of($assignment->title)->ucfirst() }}</flux:heading>
            <flux:subheading>{{ Str::of($assignment->topic->name)->ucfirst() }}</flux:subheading>
        </div>

        <div class="space-y-3">
            <div class="gap-3 items-start flex">
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

    <!-- Update assignment modal -->
    <livewire:assignments.edit :$assignment wire:key="edit-assignment-{{ $assignment->id }}" />

    <!-- Delete assignment modal -->
    <livewire:assignments.delete :$assignment wire:key="delete-assignment-{{ $assignment->id }}" />
</div>

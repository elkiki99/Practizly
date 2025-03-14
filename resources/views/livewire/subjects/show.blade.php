<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Subject;

new #[Layout('layouts.dashboard-component')] #[Title('Subjects • Practizly')] class extends Component {
    public ?Subject $subject;

    public function mount($slug, Subject $subject)
    {
        $this->subject = Subject::where('slug', $slug)->first();
    }

    #[On('subjectUpdated')]
    public function updatedSubject()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="space-y-3">
            <flux:heading level="1" size="xl" class="text-{{ $subject->color }}">
                {{ Str::of($subject->name)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="edit-subject-{{ $subject->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="pencil-square" size="lg">Edit
                    subject
                </flux:badge>
            </flux:modal.trigger>

            <flux:modal.trigger name="delete-subject-{{ $subject->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="trash" size="lg">Delete subject
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <livewire:subjects.components.nav-bar :subject="$subject" />

    <!-- Subject card -->
    <flux:card class="flex flex-col items-stretch flex-grow h-full space-y-6 w-1/3"
        wire:key="subject-{{ $subject->id }}">
        <!-- Subject heading -->
        <div>
            <div class="flex items-center mb-2">
                <flux:link size="lg">{{ Str::of($subject->name)->ucfirst() }}</flux:link>
                <span class="inline-block ml-2 size-2 bg-{{ $subject->color }}-500 rounded-full"></span>
            </div>
            <flux:subheading>{{ $subject->description }}</flux:subheading>
        </div>

        <!-- Last exams -->
        <div>
            <div class="gap-3 items-center flex">
                <flux:icon.academic-cap variant="micro" />
                <flux:heading>Recent tests</flux:heading>
            </div>
            <ul class="ml-7">
                @forelse ($subject->exams()->latest()->take(2)->get() as $exam)
                    <li class="flex items-center justify-between">
                        <flux:subheading>{{ $exam->title }}</flux:subheading>
                        <flux:tooltip content="Finish test" position="left">
                            <flux:button size="sm" as="link" variant="ghost" wire:navigate
                                href="/{{ Auth::user()->username }}/exams/{{ $exam->slug }}" icon="chevron-right" />
                        </flux:tooltip>
                    </li>
                @empty
                    <li class="flex items-center justify-between">
                        <flux:subheading>No tests yet</flux:subheading>
                        <flux:tooltip content="New test" position="left">
                            <flux:button size="sm" as="link" variant="ghost" wire:navigate
                                href="/{{ Auth::user()->username }}/exams" icon="chevron-right" />
                        </flux:tooltip>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Last assignments -->
        <div>
            <div class="gap-3 items-center flex">
                <flux:icon.document-text variant="micro" />
                <flux:heading>Recent assignments</flux:heading>
            </div>
            <ul class="ml-7">
                @forelse ($subject->assignments()->latest()->take(2)->get() as $assignment)
                    <li class="flex items-center justify-between" wire:key="assignment-{{ $assignment->id }}">
                        <flux:subheading>{{ $assignment->title }}</flux:subheading>
                        <flux:tooltip content="Finish assignment" position="left">
                            <flux:button size="sm" as="link" variant="ghost" wire:navigate
                                href="/{{ Auth::user()->username }}/assignments/{{ $assignment->slug }}"
                                icon="chevron-right" />
                        </flux:tooltip>
                    </li>
                @empty
                    <li class="flex items-center justify-between">
                        <flux:subheading>No assignments yet</flux:subheading>
                        <flux:tooltip content="New assignment" position="left">
                            <flux:button size="sm" as="link" variant="ghost" wire:navigate
                                href="/{{ Auth::user()->username }}/assignments" icon="chevron-right" />
                        </flux:tooltip>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Last events -->
        <div>
            <div class="gap-3 items-center flex">
                <flux:icon.calendar variant="micro" />
                <flux:heading>Next events</flux:heading>
            </div>
            <ul class="ml-7">
                @forelse ($subject->events()->take(2)->get() as $event)
                    <li class="flex items-center justify-between" wire:key="event-{{ $event->id }}">
                        <flux:subheading>{{ $event->name }}</flux:subheading>
                        <flux:tooltip content="Finish event" position="left">
                            <flux:button size="sm" as="link" variant="ghost" wire:navigate
                                href="/{{ Auth::user()->username }}/events/{{ $event->slug }}"
                                icon="chevron-right" />
                        </flux:tooltip>
                    </li>
                @empty
                    <li class="flex items-center justify-between">
                        <flux:subheading>No events yet</flux:subheading>
                        <flux:tooltip content="New event" position="left">
                            <flux:button size="sm" as="link" variant="ghost" wire:navigate
                                href="/{{ Auth::user()->username }}/calendar" icon="chevron-right" />
                        </flux:tooltip>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Topics list -->
        <div class="mt-auto">
            <flux:separator variant="subtle" class="mb-6" />

            <div class="flex mb-2 items-center gap-3">
                <flux:icon.tag variant="micro" />
                <flux:heading>Topics</flux:heading>
            </div>

            <div class="flex flex-wrap gap-2 ml-7">
                @forelse($subject->topics as $topic)
                    <flux:badge size="sm" wire:key="topic-{{ $topic->id }}">{{ $topic->name }}
                    </flux:badge>
                @empty
                    <flux:subheading>No topics yet</flux:subheading>
                @endforelse
            </div>
        </div>
    </flux:card>

    <!-- Update subject modal -->
    <livewire:subjects.edit :$subject wire:key="edit-subject-{{ $subject->id }}" />

    <!-- Delete subject modal -->
    <livewire:subjects.delete :$subject wire:key="delete-subject-{{ $subject->id }}" />
</div>

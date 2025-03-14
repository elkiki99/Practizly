<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Subject;

new #[Layout('layouts.dashboard-component')] #[Title('Subjects • Practizly')] class extends Component {
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
            'exams' => $this->subject->exams()->orderBy('created_at', 'desc')->paginate(12),
        ];
    }

    #[On('examCreated')]
    #[On('examDeleted')]
    public function updatedExams()
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
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">
                    {{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Exams</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="create-exam">
                <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New exam
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Header & nav bar -->
    <livewire:subjects.components.nav-bar :subject="$subject" />

    <flux:table :paginate="$exams">
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column class="hidden sm:table-cell">Topics</flux:table.column>
            <flux:table.column sortable>Difficulty</flux:table.column>
            <flux:table.column class="hidden md:table-cell">Size</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($exams as $exam)
                <flux:table.row wire:key="exam-{{ $exam->id }}">
                    <!-- Name -->
                    <flux:table.cell variant="strong" class="flex items-center space-x-2 whitespace-nowrap">
                        <flux:icon.academic-cap variant="mini" inset="top bottom" />
                        <flux:link class="text-sm  font-medium text-zinc-800 dark:text-white whitespace-nowrap"
                            wire:navigate href="/{{ Auth::user()->username }}/exams/{{ $exam->slug }}">
                            {{ Str::of($exam->title)->ucfirst() }}</flux:link>
                    </flux:table.cell>
                    
                    <!-- Topics -->
                    <flux:table.cell class="hidden sm:table-cell">
                        @php
                            $topicsToShow = $exam->topics->take(2);
                            $hasMoreTopics = $exam->topics->count() > 2;
                        @endphp

                        @if ($topicsToShow->isEmpty())
                            <flux:badge inset="top bottom" size="sm" color="zinc">No topics yet</flux:badge>
                        @else
                            @foreach ($topicsToShow as $topic)
                                <flux:badge inset="top bottom" size="sm" color="zinc">{{ $topic->name }}</flux:badge>
                            @endforeach

                            @if ($hasMoreTopics)
                                <flux:badge inset="top bottom" size="sm" color="zinc">+ {{ $exam->topics->count() - 2 }} more
                                </flux:badge>
                            @endif
                        @endif
                    </flux:table.cell>

                    <!-- Difficulty -->
                    <flux:table.cell class="whitespace-nowrap">{{ Str::of($exam->difficulty)->ucfirst() }}</flux:table.cell>

                    <!-- Size -->
                    <flux:table.cell class="whitespace-nowrap hidden md:table-cell">{{ Str::of($exam->size)->ucfirst() }}</flux:table.cell>

                    <!-- Actions -->
                    <flux:table.cell>
                        <flux:dropdown class="flex justify-end items-end space-x-2" >
                            <flux:button inset="top bottom" size="sm" variant="ghost" icon="ellipsis-horizontal"/>

                            <flux:menu>
                                <flux:menu.item as="link" wire:navigate
                                    href="/{{ Auth::user()->username }}/exams/{{ $exam->slug }}"
                                    icon-trailing="chevron-right">Take exam</flux:menu.item>
                                <flux:menu.separator />

                                <flux:modal.trigger name="download-exam-{{ $exam->id }}">
                                    <flux:menu.item icon="arrow-down-tray">Download exam</flux:menu.button>
                                </flux:modal.trigger>
                                <flux:modal.trigger name="delete-exam-{{ $exam->id }}">
                                    <flux:menu.item variant="danger" icon="trash">Delete exam</flux:menu.button>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        <livewire:exams.delete :$exam wire:key="delete-exam-{{ $exam->id }}" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row class="text-center">
                    <flux:table.cell colspan="4">You don't have any exams yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- Modal actions -->
    <livewire:exams.create />
</div>

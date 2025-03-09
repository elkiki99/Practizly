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
            'exams' => $this->subject->exams()->orderBy('created_at', 'desc')->paginate(6),
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
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($exams as $exam)
            <flux:card class="space-y-6">
                <!-- Exam heading -->
                <div class="flex-1">
                    <div class="flex items-center">
                        <flux:heading size="lg">{{ $exam->title }}</flux:heading>
                        <flux:spacer />
                        
                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />

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

                        <!-- Delete exam modal -->
                        <livewire:exams.delete :$exam wire:key="delete-exam-{{ $exam->id }}" />
                    </div>
                </div>

                <!-- Details -->
                <div class="flex-1 space-y-2">
                    <flux:heading>Details</flux:heading>

                    <div class="gap-3 items-center flex">
                        <flux:icon.book-open variant="micro" />
                        <flux:heading>{{ $exam->subject->name }}</flux:heading>
                    </div>
                    <div class="gap-3 items-center flex">
                        <flux:icon.arrows-up-down variant="micro" />
                        <flux:heading>{{ Str::of($exam->size)->ucfirst() }}</flux:heading>
                    </div>

                    <div class="gap-3 items-center flex">
                        <flux:icon.chart-bar variant="micro" />
                        <flux:heading>{{ Str::of($exam->difficulty)->ucfirst() }}</flux:heading>
                    </div>

                    <div class="gap-3 items-center flex">
                        <flux:icon.adjustments-vertical variant="micro" />
                        <flux:heading>{{ Str::of($exam->type)->replace('_', ' ')->ucfirst() }}</flux:heading>
                    </div>
                </div>

                <!-- Topics list -->
                <div class="flex-1">
                    <flux:heading class="mb-2">Topics</flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @forelse($exam->topics as $topic)
                            <flux:badge size="sm">{{ Str::of($topic->name)->ucfirst() }}</flux:badge>
                        @empty
                            <flux:subheading>No topics yet</flux:subheading>
                        @endforelse
                    </div>
                </div>

                <div class="flex">
                    <flux:spacer />
                    <flux:button as="link" variant="primary" icon-trailing="chevron-right"
                        href="/{{ Auth::user()->username }}/exams/{{ $exam->slug }}">Take exam</flux:button>
                </div>
            </flux:card>
        @empty
            <flux:subheading>You don't have any exams yet.</flux:subheading>
        @endforelse
    </div>

    <!-- Paginator -->
    <flux:table :paginate="$exams" />

    <!-- Modal actions -->
    <livewire:exams.create />
</div>

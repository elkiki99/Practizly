<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use App\Models\Exam;

new #[Layout('layouts.dashboard')] #[Title('Exams • Practizly')] class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'exams' => Auth::user()->exams()->latest()->paginate(6),
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
    <div class="space-y-3">
        <flux:heading level="1" size="xl">Exams</flux:heading>

        <flux:breadcrumbs>
            <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Exams
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

                    <flux:select.option value="all" selected>All</flux:select.option>
                    <flux:select.option value="unapproved">Unapproved</flux:select.option>
                    <flux:select.option value="approved">Approved</flux:select.option>
                </flux:select>

                <flux:select variant="listbox" class="sm:max-w-fit">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>

                    <flux:select.option value="popular" selected>Most popular</flux:select.option>
                    <flux:select.option value="newest">Newest</flux:select.option>
                    <flux:select.option value="oldest">Oldest</flux:select.option>
                </flux:select>
            </div>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2 max-lg:hidden">
                <flux:modal.trigger name="create-exam">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New exam
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($exams as $exam)
            <flux:card class="space-y-6" wire:key="exam-{{ $exam->id }}">
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
                        href="{{-- route('exams.show', $exam) --}}">Take exam</flux:button>
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

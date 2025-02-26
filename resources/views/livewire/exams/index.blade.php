<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use App\Models\Exam;

new #[Layout('layouts.dashboard')] #[Title('Exams • Practizly')] class extends Component {
    public $exams;
    // public $viewType = 'grid';

    public function mount()
    {
        $this->exams = Auth::user()->exams()->latest()->get();
        // $this->viewType = 'grid';
    }

    #[On('examCreated')]
    public function updatedExams()
    {
        $this->exams = Auth::user()->exams()->latest()->get();
    }
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Exams</flux:heading>
    <flux:separator variant="subtle" />

    <!-- Panel navbar -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            {{-- <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

            <flux:select size="sm" class="">
                <option selected>Creation</option>
                <option>Subject</option>
                <option>Topic</option>
            </flux:select> --}}

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

        <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
            <flux:tab selected value="grid" icon="squares-2x2" icon-variant="outline" />
            <flux:tab value="table" icon="list-bullet" icon-variant="outline" />
        </flux:tabs>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($exams as $exam)
            <flux:card class="space-y-6">
                <!-- Exam heading -->
                <div class="flex-1">
                    <div class="flex items-center">
                        <flux:heading size="lg">{{ $exam->title }}</flux:heading>
                        <flux:spacer />
                        <flux:tooltip content="Options" position="left">
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />
                        </flux:tooltip>
                    </div>
                </div>

                <!-- Details -->
                <div class="flex-1">
                    <flux:heading class="mb-2">Details</flux:heading>
                    <flux:subheading><strong>Subject: </strong> {{ $exam->subject->name }}</flux:subheading>
                    <flux:subheading><strong>Size: </strong>{{ Str::of($exam->size)->ucfirst() }}</flux:subheading>
                    <flux:subheading><strong>Difficulty: </strong> {{ Str::of($exam->difficulty)->ucfirst() }}
                    </flux:subheading>
                    <flux:subheading><strong>Type: </strong>{{ Str::of($exam->type)->replace('_', ' ')->ucfirst() }}
                    </flux:subheading>
                </div>

                <flux:separator variant="subtle" />

                <!-- Topics list -->
                <div class="flex-1">
                    <flux:heading class="mb-2">Topics</flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @forelse($exam->topics as $topic)
                            <flux:badge size="sm">{{ $topic->name }}</flux:badge>
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

    <!-- Modal actions -->
    <livewire:exams.create />
</div>

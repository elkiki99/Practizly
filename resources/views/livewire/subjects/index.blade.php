<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Livewire\Attributes\On;
use App\Models\Subject;

new #[Layout('layouts.dashboard')] #[Title('Subjects • Practizly')] class extends Component {
    // public $viewType;
    public $subjects;

    public function mount()
    {
        // $this->viewType = 'grid';
        $this->subjects = Auth::user()->subjects()->latest()->get();
    }

    #[On('subjectCreated')]
    public function updatedSubjects()
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();
    }
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Subjects</flux:heading>
    <flux:separator variant="subtle" />

    <!-- Panel navbar -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

            <flux:select size="sm" class="">
                <option selected>Creation</option>
                <option>Latest exam</option>
                <option>Latest assignment</option>
                <option>Favorite</option>
            </flux:select>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2 max-lg:hidden">
                <flux:modal.trigger name="create-subject">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New subject
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
        @forelse($subjects as $subject)
            <flux:card class="flex flex-col items-stretch flex-grow h-full space-y-6">
                <!-- Subject heading -->
                <div class="flex-1">
                    <div class="flex items-center">
                        <flux:heading size="lg">{{ $subject->name }}</flux:heading>
                        <!-- Indicador de color -->
                        <span class="inline-block ml-2 size-2 bg-{{ $subject->color }}-500 rounded-full"></span>
                        <flux:spacer />
                        <flux:tooltip content="Options" position="left">
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />
                        </flux:tooltip>
                    </div>
                </div>

                {{-- <flux:separator variant="subtle" /> --}}

                <!-- Last exams -->
                <div class="flex-1">
                    <flux:heading>Recent tests</flux:heading>
                    <ul>
                        @forelse ($subject->exams()->latest()->take(2)->get() as $exam)
                            <li class="flex items-center justify-between">
                                <flux:subheading>{{ $exam->title }}</flux:subheading>
                                <flux:tooltip content="Finish test" position="left">
                                    <flux:button size="sm" as="link" variant="ghost" href="#"
                                        icon="chevron-right" />
                                </flux:tooltip>
                            </li>
                        @empty
                            <li class="flex items-center justify-between">
                                <flux:subheading>No tests yet</flux:subheading>
                                <flux:tooltip content="New test" position="left">
                                    <flux:button size="sm" as="link" variant="ghost" href="#"
                                        icon="plus" />
                                </flux:tooltip>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Last assignments -->
                <div class="flex-1">
                    <flux:heading>Recent assignments</flux:heading>
                    <ul>
                        @forelse ($subject->assignments()->latest()->take(2)->get() as $assignment)
                            <li class="flex items-center justify-between">
                                <flux:subheading>{{ $assignment->title }}</flux:subheading>
                                <flux:tooltip content="Finish assignment" position="left">
                                    <flux:button size="sm" as="link" variant="ghost" href="#"
                                        icon="chevron-right" />
                                </flux:tooltip>
                            </li>
                        @empty
                            <li class="flex items-center justify-between">
                                <flux:subheading>No assignments yet</flux:subheading>
                                <flux:tooltip content="New assignment" position="left">
                                    <flux:button size="sm" as="link" variant="ghost" href="#"
                                        icon="plus" />
                                </flux:tooltip>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <flux:separator variant="subtle" />

                <!-- Topics list -->
                <div class="flex-1">
                    <flux:heading class="mb-2">Topics</flux:heading>
                    <div class="flex flex-wrap gap-2">
                        @forelse($subject->topics as $topic)
                            <flux:badge size="sm">{{ $topic->name }}</flux:badge>
                        @empty
                            <flux:subheading>No topics yet</flux:subheading>
                        @endforelse
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:subheading>You don't have any subjects yet.</flux:subheading>
        @endforelse
    </div>

    <!-- Modal actions -->
    <livewire:subjects.create />
</div>

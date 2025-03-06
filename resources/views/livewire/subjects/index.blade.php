<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Subject;

new #[Layout('layouts.dashboard')] #[Title('Subjects • Practizly')] class extends Component {
    use WithoutUrlPagination;
    use WithPagination;

    public function with()
    {
        return [
            'subjects' => Auth::user()->subjects()->latest()->paginate(6),
        ];
    }

    #[On('subjectCreated')]
    #[On('subjectUpdated')]
    public function updatedSubjects()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Subjects</flux:heading>
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

                    <flux:select.option value="all" selected>Creation</flux:select.option>
                    <flux:select.option value="unapproved">Last exam</flux:select.option>
                    <flux:select.option value="approved">Latest assignment</flux:select.option>
                    <flux:select.option value="approved">Favorite</flux:select.option>
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
                <flux:modal.trigger name="create-subject">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New subject
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>

        {{-- <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
            <flux:tab selected value="grid" icon="squares-2x2" icon-variant="outline" />
            <flux:tab value="table" icon="list-bullet" icon-variant="outline" />
        </flux:tabs> --}}
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($subjects as $subject)
            <flux:card class="flex flex-col items-stretch flex-grow h-full space-y-6"
                wire:key="subject-{{ $subject->id }}">
                <!-- Subject heading -->
                <div>
                    <div class="flex items-center">
                        <flux:link href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}" wire:navigate
                            size="lg">{{ Str::of($subject->name)->ucfirst() }}</flux:link>
                        <span class="inline-block ml-2 size-2 bg-{{ $subject->color }}-500 rounded-full"></span>
                        <flux:spacer />
                        <flux:dropdown>
                            <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />

                            <flux:menu>
                                <flux:menu.item as="link" wire:navigate
                                    href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}"
                                    icon-trailing="chevron-right">View subject</flux:menu.item>
                                <flux:menu.separator />

                                <flux:modal.trigger name="edit-subject-{{ $subject->id }}">
                                    <flux:menu.item icon="pencil-square">Edit</flux:menu.item>
                                </flux:modal.trigger>

                                <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
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
                                    <flux:button size="sm" as="link" variant="ghost" href="#"
                                        icon="chevron-right" />
                                </flux:tooltip>
                            </li>
                        @empty
                            <li class="flex items-center justify-between">
                                <flux:subheading>No events yet</flux:subheading>
                                <flux:tooltip content="New event" position="left">
                                    <flux:button size="sm" as="link" variant="ghost" href="#"
                                        icon="plus" />
                                </flux:tooltip>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Update subject modal -->
                <livewire:subjects.edit :subject="$subject" wire:key="subject-{{ $subject->id }}" />


                <!-- Topics list -->
                <div class="mt-auto">
                    <flux:separator variant="subtle" class="mb-6" />

                    <div class="flex mb-2 items-center gap-3">
                        <flux:icon.tag variant="micro" />
                        <flux:heading>Topics</flux:heading>
                    </div>

                    <div class="flex flex-wrap gap-2 ml-7">
                        @forelse($subject->topics as $topic)
                            <flux:badge size="sm" wire:key="topic-{{ $topic->id }}">{{ $topic->name }}</flux:badge>
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

    <!-- Paginator -->
    <flux:table :paginate="$subjects" />

    <!-- Create subject modal -->
    <livewire:subjects.create />
</div>

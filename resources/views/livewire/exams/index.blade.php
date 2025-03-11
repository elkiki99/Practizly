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

            <div class="flex items-center justify-start gap-2">
                <flux:modal.trigger name="create-exam">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New exam
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    {{-- <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"> --}}
    <flux:table :paginate="$exams">
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Subject</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Difficulty</flux:table.column>
            <flux:table.column>Size</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($exams as $exam)
                <flux:table.row wire:key="exam-{{ $exam->id }}">
                    <!-- Name -->
                    <flux:table.cell variant="strong" class="flex items-center space-x-2 whitespace-nowrap">
                        <flux:icon.academic-cap variant="mini" inset="top bottom" />
                        <flux:link class="text-sm  font-medium text-zinc-800 dark:text-white whitespace-nowrap" wire:navigate
                            href="/{{ Auth::user()->username }}/exams/{{ $exam->slug }}">
                            {{ Str::of($exam->title)->ucfirst() }}</flux:link>
                    </flux:table.cell>

                    <!-- Subject -->
                    <flux:table.cell class="whitespace-nowrap">{{ Str::of($exam->subject->name)->ucfirst() }}
                    </flux:table.cell>

                    <!-- Type -->
                    <flux:table.cell class="whitespace-nowrap">{{ Str::of($exam->type)->replace('_', ' ')->ucfirst() }}
                    </flux:table.cell>

                    <!-- Difficulty -->
                    <flux:table.cell class="whitespace-nowrap">{{ Str::of($exam->difficulty)->ucfirst() }}
                    </flux:table.cell>

                    <!-- Size -->
                    <flux:table.cell class="whitespace-nowrap">{{ Str::of($exam->size)->ucfirst() }}</flux:table.cell>

                    <!-- Actions -->
                    <flux:table.cell align="end">
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
                    </flux:table.cell>

                    <livewire:exams.delete :$exam wire:key="delete-exam-{{ $exam->id }}" />
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6">No exams available.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- Modal actions -->
    <livewire:exams.create />
</div>

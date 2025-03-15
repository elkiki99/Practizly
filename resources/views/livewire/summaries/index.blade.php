<?php

use Livewire\Volt\Component;

use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Carbon\Carbon;

new #[Layout('layouts.dashboard')] #[Title('Summaries • Practizly')] class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'summaries' => Auth::user()->summaries()->latest()->paginate(12),
        ];
    }

    #[On('summaryCreated')]
    #[On('summaryDeleted')]
    public function updatedSummaries()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="space-y-3">
        <flux:heading level="1" size="xl">Summaries</flux:heading>
        <flux:subheading>Explore and study with your AI generated summaries.</flux:subheading>
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

                    <flux:select.option value="all" selected>Subject</flux:select.option>
                    <flux:select.option value="tests">Topic</flux:select.option>
                </flux:select>

                <flux:select variant="listbox" class="sm:max-w-fit">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>

                    <flux:select.option value="status" selected>Status</flux:select.option>
                    <flux:select.option value="pending">Pending</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                </flux:select>
            </div>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2">
                <flux:modal.trigger name="create-summary">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New
                        summary
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <flux:table :paginate="$summaries">
            <flux:table.columns>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column class="hidden sm:table-cell">Topics</flux:table.column>
                <flux:table.column class="hidden md:table-cell">Size</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($summaries as $summary)
                    <flux:table.row wire:key="summary-{{ $summary->id }}">
                        <flux:table.cell variant="strong">
                            <flux:link class="text-sm font-medium text-zinc-800 dark:text-white" wire:navigate
                                href="/{{ Auth::user()->username }}/summaries/{{ $summary->slug }}">
                                {{ Str::of($summary->title)->ucfirst() }}
                            </flux:link>
                        </flux:table.cell>

                        <!-- Subject -->
                        <flux:table.cell>
                            <flux:link class="text-sm  text-zinc-500 dark:text-zinc-300 whitespace-nowrap" wire:navigate
                                href="/{{ Auth::user()->username }}/subjects/{{ $summary->subject->slug }}">
                                {{ $summary->subject->name }}</flux:link>
                        </flux:table.cell>

                        <!-- Topics -->
                        <flux:table.cell class="hidden sm:table-cell">
                            @php
                                $topicsToShow = $summary->topics->take(2);
                                $hasMoreTopics = $summary->topics->count() > 2;
                            @endphp

                            @if ($topicsToShow->isEmpty())
                                <flux:badge inset="top bottom" size="sm" color="zinc">No topics yet</flux:badge>
                            @else
                                @foreach ($topicsToShow as $topic)
                                    <flux:badge inset="top bottom" size="sm" color="zinc">{{ $topic->name }}
                                    </flux:badge>
                                @endforeach

                                @if ($hasMoreTopics)
                                    <flux:badge inset="top bottom" size="sm" color="zinc">+
                                        {{ $summary->topics->count() - 2 }} more
                                    </flux:badge>
                                @endif
                            @endif
                        </flux:table.cell>

                        <!-- Size -->
                        <flux:table.cell class="whitespace-nowrap" class="hidden md:table-cell">
                            {{ Str::of($summary->size)->replace('_', ' ')->ucfirst() }}</flux:table.cell>

                        <!-- Actions -->
                        <flux:table.cell>
                            <flux:dropdown class="flex justify-end items-end space-x-2">
                                <flux:button inset="top bottom" size="sm" variant="ghost"
                                    icon="ellipsis-horizontal" />

                                <flux:menu>
                                    <flux:menu.item as="link" wire:navigate
                                        href="/{{ Auth::user()->username }}/summaries/{{ $summary->slug }}"
                                        icon-trailing="chevron-right">Show summary</flux:menu.item>
                                    <flux:menu.separator />

                                    <flux:modal.trigger name="download-summary-{{ $summary->id }}">
                                        <flux:menu.item icon="arrow-down-tray">Download summary</flux:menu.button>
                                    </flux:modal.trigger>
                                    <flux:modal.trigger name="delete-summary-{{ $summary->id }}">
                                        <flux:menu.item variant="danger" icon="trash">Delete summary
                                            </flux:menu.button>
                                    </flux:modal.trigger>
                                </flux:menu>
                            </flux:dropdown>

                            <livewire:summaries.delete :$summary wire:key="delete-summary-{{ $summary->id }}" />
                        </flux:table.cell>  
                    </flux:table.row>
                @empty
                    <flux:table.cell colspan="4" class="text-center">
                        You don't have any summaries yet.
                    </flux:table.cell>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <!-- Modal actions -->
    <livewire:summaries.create />
</div>

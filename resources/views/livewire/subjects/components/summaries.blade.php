<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On, Computed};
use Livewire\WithPagination;
use Illuminate\Support\Str;
use App\Models\Subject;
use Carbon\Carbon;

new #[Layout('layouts.dashboard-component')] #[Title('Summary • Practizly')] class extends Component {
    use WithPagination;

    public ?Subject $subject;

    public function mount(Subject $subject, $slug)
    {
        $this->subject = Subject::where('slug', $slug)->first();
    }

    #[Computed]
    public function summaries()
    {
        return $this->subject->summaries()->latest()->paginate(12);
    }

    #[On('summaryCreated')]
    #[On('summaryDeleted')]
    public function updatedSummary()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div class="space-y-3">
            <flux:heading level="1" size="xl" class="text-{{ $subject->color }}">
                {{ Str::of($subject->name)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">
                    {{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Summaries</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="create-summary">
                <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New summary
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Header & nav bar -->
    <livewire:subjects.components.nav-bar :subject="$subject" />

    <flux:table :paginate="$this->summaries">
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Topics</flux:table.column>
            <flux:table.column class="hidden sm:table-cell">Size</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($this->summaries as $summary)
                <flux:table.row wire:key="summary-{{ $summary->id }}">
                    <flux:table.cell variant="strong">
                        <flux:link class="text-sm font-medium text-zinc-800 dark:text-white" wire:navigate
                            href="/{{ Auth::user()->username }}/summaries/{{ $summary->slug }}">
                            {{ Str::of($summary->title)->ucfirst() }}
                        </flux:link>
                    </flux:table.cell>

                    <!-- Topics -->
                    <flux:table.cell>
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
                    <flux:table.cell class="whitespace-nowrap hidden sm:table-cell">
                        {{ Str::of($summary->size)->replace('_', ' ')->ucfirst() }}</flux:table.cell>

                    <!-- Actions -->
                    <flux:table.cell>
                        <flux:dropdown class="flex justify-end items-end space-x-2">
                            <flux:button inset="top bottom" size="sm" variant="ghost" icon="ellipsis-horizontal" />

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
                <flux:table.row class="text-center">
                    <flux:table.cell colspan="4">You don't have any summaries yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <livewire:summaries.create />
</div>

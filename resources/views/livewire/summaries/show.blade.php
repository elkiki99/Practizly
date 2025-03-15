<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Summary;
use Carbon\Carbon;

new #[Layout('layouts.dashboard-component')] #[Title('Summaries • Practizly')] class extends Component {
    public ?Summary $summary;

    public function mount($slug, Summary $summary)
    {
        $this->summary = Summary::where('slug', $slug)->first();
    }

    #[On('summaryUpdated')]
    public function updatedSummary()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-start justify-between gap-2">
        <div class="space-y-3">
            <flux:heading level="1" size="xl">
                {{ Str::of($summary->title)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate
                    href="/{{ Auth::user()->username }}/subjects/{{ $summary->subject->slug }}">
                    {{ Str::of($summary->subject->name)->ucfirst() }}
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate
                    href="/{{ Auth::user()->username }}/subjects/{{ $summary->subject->slug }}/summaries">Summaries
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ Str::of($summary->title)->ucfirst() }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        {{-- <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="edit-summary-{{ $summary->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="pencil-square" size="lg">
                    Edit&nbsp;<span class="hidden sm:inline">event</span>
                </flux:badge>
            </flux:modal.trigger> --}}

            <flux:modal.trigger name="delete-summary-{{ $summary->id }}">
                <flux:badge as="button" variant="pill" color="zinc" icon="trash" size="lg">
                    Delete&nbsp;<span class="hidden sm:inline">summary</span>
                </flux:badge>
            </flux:modal.trigger>
        {{-- </div> --}}
    </div>

    <livewire:subjects.components.nav-bar :subject="$summary->subject" />

    <!-- Subject card -->
    <flux:card class="flex flex-col items-stretch flex-grow h-full space-y-6 w-96">
        <div class="space-y-6">
            <div>
                <flux:subheading>{{ Str::of($summary->subject->name)->ucfirst() }}</flux:subheading>
                <flux:heading size="lg">{{ Str::of($summary->title)->ucfirst() }}</flux:heading>
            </div>

            <div class="space-y-3">
                <div class="gap-3 items-center flex">
                    <flux:icon.arrows-up-down variant="micro" />
                    <flux:heading>{{ Str::of($summary->size)->ucfirst() }}</flux:heading>
                </div>

                <div class="gap-3 items-center flex">
                    <flux:icon.paper-clip variant="micro" />
                    <flux:heading x-data="{ count: {{ $summary->attachments->count() }} }"
                        x-text="count === 1 ? count + ' attachment' : count + ' attachments'"></flux:heading>
                </div>
            </div>
        </div>

        <div class="mt-auto">
            <flux:separator variant="subtle" class="mb-6" />

            <div class="flex mb-2 items-center gap-3">
                <flux:icon.tag variant="micro" />
                <flux:heading>Topics</flux:heading>
            </div>

            <div class="flex flex-wrap gap-2 ml-7">
                @forelse($summary->topics as $topic)
                    <flux:badge size="sm" wire:key="topic-{{ $topic->id }}">{{ $topic->name }}
                    </flux:badge>
                @empty
                    <flux:subheading>No topics yet</flux:subheading>
                @endforelse
            </div>
        </div>
    </flux:card>

    <!-- Update subject modal -->
    {{-- <livewire:summaries.edit :$summary wire:key="edit-summary-{{ $summary->id }}" /> --}}

    <!-- Delete summary modal -->
    <livewire:summaries.delete :$summary wire:key="delete-summary-{{ $summary->id }}" />
</div>

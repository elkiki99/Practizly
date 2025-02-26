<?php

use Livewire\Volt\Component;

use Livewire\Attributes\{Layout, Title};
use Livewire\Attributes\On;
use App\Models\Summary;

new #[Layout('layouts.dashboard')] #[Title('Summaries • Practizly')] class extends Component {
    public $summaries = [];
    public $subject_id;

    public function mount()
    {
        $this->summaries = Summary::whereHas('topic.subject', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();
    }

    #[On('summaryCreated')]
    public function updatedSummaries()
    {
        $this->summaries = Summary::whereHas('topic.subject', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();
    }
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Summaries</flux:heading>
    <flux:separator variant="subtle" />

    <!-- Panel navbar -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <flux:subheading class="whitespace-nowrap">Filter by:</flux:subheading>

            <flux:select size="sm" class="">
                <option selected>Subject</option>
                <option>Topic</option>
                {{-- <option>Exam</option> --}}
            </flux:select>

            <flux:separator vertical class="mx-2 my-2 max-lg:hidden" />

            <div class="flex items-center justify-start gap-2 max-lg:hidden">
                <flux:modal.trigger name="create-summary">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New
                        summary
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
        @forelse ($this->summaries as $summary)
            <flux:card>
                <div class="space-y-6">
                    <div>
                        <flux:heading>{{ $summary->title }}</flux:heading>
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:subheading>You don't have any summaries yet.</flux:subheading>
        @endforelse
    </div>

    <!-- Modal actions -->
    <livewire:summaries.create />
</div>
</div>

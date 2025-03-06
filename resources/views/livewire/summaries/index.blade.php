<?php

use Livewire\Volt\Component;

use Livewire\Attributes\{Layout, Title};
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Models\Summary;
use Carbon\Carbon;

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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
        @forelse($summaries as $summary)
            <flux:card>
                <div class="space-y-6">
                    <div>
                        <div class="flex items-center">
                            <flux:subheading>{{ Str::of($summary->topic->name)->ucfirst() }} - {{ Str::of($summary->subject->name)->ucfirst() }}</flux:subheading>
                            <flux:spacer />
                            <flux:tooltip content="Options" position="left">
                                <flux:button size="sm" variant="ghost" icon="ellipsis-horizontal" />
                            </flux:tooltip>
                        </div>

                        <flux:heading size="lg">{{ Str::of($summary->title)->ucfirst() }}</flux:heading>
                    </div>
                    <div class="space-y-3">
                        <div class="gap-3 items-center flex">
                            <flux:icon.arrows-up-down variant="micro" />
                            <flux:heading>{{ Str::of($summary->size)->ucfirst() }}</flux:heading>
                        </div>

                        <div class="gap-3 items-center flex">
                            <flux:icon.paper-clip variant="micro" />
                            <flux:heading x-data="{ count: {{ $summary->attachments->count() }} }" x-text="count === 1 ? count + ' attachment' : count + ' attachments'"></flux:heading>
                        </div>
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

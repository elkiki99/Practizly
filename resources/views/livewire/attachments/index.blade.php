<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Livewire\Attributes\On;
use App\Models\Attachment;

new #[Layout('layouts.dashboard')] #[Title('Library • Practizly')] class extends Component {
    public $attachments = [];

    public function mount()
    {
        $subjectIds = Auth::user()->subjects()->pluck('id')->toArray();

        $this->attachments = Attachment::whereHas('attachable', function ($query) use ($subjectIds) {
            $query->whereHas('subject', function ($subjectQuery) use ($subjectIds) {
                $subjectQuery->whereIn('subjects.id', $subjectIds);
            });
        })
            ->latest()
            ->get();
    }

    #[On('attachmentCreated')]
    public function updatedAttachments()
    {
        $this->attachments = Attachment::whereHas('attachable', function ($query) {
            $query->whereHas('subject', function ($subjectQuery) {
                $subjectQuery->where('subjects.user_id', Auth::user()->id);
            });
        })
            ->latest()
            ->get();
    }
}; ?>

<div class="space-y-6">
    <flux:heading level="1" size="xl">Library</flux:heading>
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
                <flux:modal.trigger name="create-attachment">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New
                        attachment
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>

        <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
            <flux:tab selected value="grid" icon="squares-2x2" icon-variant="outline" />
            <flux:tab value="table" icon="list-bullet" icon-variant="outline" />
        </flux:tabs>
    </div>

    <div class="grid grid-cols-1    ">
        @forelse($attachments as $attachment)
            <flux:card>
                <div class="space-y-6">
                    <div>
                        {{-- <flux:subheading>{{ $attachment->topic->subject->name }}</flux:subheading> --}}
                        <flux:heading size="lg">{{ $attachment->file_name }}</flux:heading>
                    </div>
                    <div>
                        <flux:subheading>{{ $attachment->description }}</flux:subheading>
                        <flux:subheading>{{ $attachment->guidelines }}</flux:subheading>
                    </div>
                </div>
            </flux:card>
            <iframe src="{{ asset('storage/' . $attachment->file_path) }}" width="100%" height="600px"></iframe>
        @empty
            <flux:subheading>You don't have any attachments yet.</flux:subheading>
        @endforelse
    </div>

    <!-- Modal actions -->
    <livewire:attachments.create />
</div>

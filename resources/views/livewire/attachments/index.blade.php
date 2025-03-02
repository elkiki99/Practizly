<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Models\Attachment;

new #[Layout('layouts.dashboard')] #[Title('Library • Practizly')] class extends Component {
    public $attachments = [];

    #[On('attachmentCreated')]
    public function mount()
    {
        $subjectIds = Auth::user()->subjects()->pluck('id')->toArray();

        $this->attachments = Attachment::whereHas('attachable', function ($query) use ($subjectIds) {
            $query->whereHas('subject', function ($subjectQuery) use ($subjectIds) {
                $subjectQuery->whereIn('subjects.id', $subjectIds);
            });
        })
            ->latest()
            ->get()
            ->map(function ($attachment) {
                $filePath = $attachment->file_path;
                $attachment->isImage = Str::endsWith($filePath, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $attachment->isPDF = Str::endsWith($filePath, 'pdf');
                return $attachment;
            });
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
    <div class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-4 xl:grid-cols-5">
        @forelse($attachments as $attachment)
            <div
                class="relative group w-[120px] h-[180px] sm:w-[150px] sm:h-[200px] md:w-[180px] md:h-[220px] lg:w-[200px] lg:h-[250px] overflow-hidden bg-gray-900/10 rounded-lg flex items-center justify-center">
                @if ($attachment->isImage)
                    <img src="{{ asset('storage/' . $attachment->file_path) }}" class="w-full h-full object-cover">
                @elseif ($attachment->isPDF)
                    <iframe src="{{ asset('storage/' . $attachment->file_path) }}" scrolling="no"
                        class="w-full h-full"></iframe>
                @else
                    <p class="text-center text-xs text-gray-500">Archivo no soportado</p>
                @endif
            </div>
        @empty
            <flux:subheading>You don't have any attachments yet.</flux:subheading>
        @endforelse
    </div>

    <!-- Modal actions -->
    <livewire:attachments.create />
</div>

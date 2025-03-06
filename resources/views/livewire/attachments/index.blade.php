<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use App\Models\Attachment;

new #[Layout('layouts.dashboard')] #[Title('Library • Practizly')] class extends Component {
    use WithPagination, WithoutUrlPagination;

    public function with()
    {
        $subjectIds = Auth::user()->subjects()->pluck('id')->toArray();

        return [
            'attachments' => ($attachments = Attachment::whereHas('attachable', function ($query) use ($subjectIds) {
                $query->whereHas('subject', function ($subjectQuery) use ($subjectIds) {
                    $subjectQuery->whereIn('subjects.id', $subjectIds);
                });
            })
                ->latest()
                ->paginate(24)),

            $attachments->getCollection()->transform(function ($attachment) {
                $filePath = $attachment->file_path;
                $extension = Str::lower(pathinfo($filePath, PATHINFO_EXTENSION));
                $attachment->isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
                $attachment->isPDF = in_array($extension, ['pdf']);
                $attachment->isDOCX = in_array($extension, ['docs', 'docx']);

                return $attachment;
            }),
        ];
    }

    #[On('attachmentCreated')]
    public function updatedAttachments()
    {
        $this->dispatch('$refresh');
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

    <div class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-6 xl:grid-cols-8">
        @forelse($attachments as $attachment)
            <div
                class="relative group w-[100px] h-[130px] md:w-[110px] md:h-[140px] overflow-hidden bg-gray-900/10 rounded-lg flex items-center justify-center">
                @if ($attachment->isImage)
                    <img src="{{ asset('storage/' . $attachment->file_path) }}" class="w-full h-full object-cover">
                @elseif ($attachment->isPDF)
                    <iframe src="{{ asset('storage/' . $attachment->file_path) }}" scrolling="no"
                        class="w-full h-full"></iframe>
                @elseif ($attachment->isDOCX)
                    <a href="{{ asset('storage/' . $attachment->file_path) }}" download
                        class="flex flex-col items-center justify-center gap-2">
                        <flux:icon.document-arrow-down class="size-12" />
                        <flux:subheading class="text-xs text-center">{{ $attachment->file_name }}</flux:subheading>
                    </a>
                @else
                    <p class="text-center text-xs text-gray-500">Archivo no soportado</p>
                @endif
            </div>
        @empty
            <flux:subheading>You don't have any attachments yet.</flux:subheading>
        @endforelse
    </div>

    <!-- Paginator -->
    <flux:table :paginate="$attachments" />

    <!-- Modal actions -->
    <livewire:attachments.create />
</div>

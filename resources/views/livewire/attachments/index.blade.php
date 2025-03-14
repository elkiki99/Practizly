<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Attachment;

new #[Layout('layouts.dashboard')] #[Title('Library • Practizly')] class extends Component {
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
                ->paginate(12)),

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
    #[On('attachmentDeleted')]
    public function updatedAttachments()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="space-y-3">
        <flux:heading level="1" size="xl">Library</flux:heading>
        <flux:heading level="2">Available attachments</flux:heading>
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
                <flux:modal.trigger name="create-attachment">
                    <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New
                        attachment
                    </flux:badge>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <flux:table :paginate="$attachments">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Size</flux:table.column>
            </flux:table.columns>

            @forelse($attachments as $attachment)
                <flux:table.row>
                    <flux:table.cell variant="strong">
                        @if ($attachment->isPDF)
                            {{ $attachment->file_name }}.pdf
                        @elseif ($attachment->isDOCX)
                            {{ $attachment->file_name }}.pdf
                        @elseif ($attachment->isImage)
                            {{ $attachment->file_name }}.img
                        @endif
                    </flux:table.cell>
                    
                    <flux:table.cell>
                        {{ $attachment->formatted_size }}
                    </flux:table.cell>

                    <!-- Actions -->
                    <flux:table.cell>
                        <div class="flex justify-end items-end space-x-2">
                            <flux:modal.trigger name="download-attachment-{{ $attachment->id }}">
                                <flux:button inset="top bottom" download as="link"
                                    href="{{ asset('storage/' . $attachment->file_path) }}" icon="arrow-down-tray"
                                    size="sm" variant="ghost" />
                            </flux:modal.trigger>

                            <flux:modal.trigger name="delete-attachment-{{ $attachment->id }}">
                                <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom">
                                </flux:button>
                            </flux:modal.trigger>
                        </div>

                        <!-- Delete attachment modal -->
                        <livewire:attachments.delete :$attachment wire:key="delete-attachment-{{ $attachment->id }}" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="2" class="text-center">
                        You don't have any attachments yet.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table>
    </div>

    <!-- Modal actions -->
    <livewire:attachments.create />
</div>

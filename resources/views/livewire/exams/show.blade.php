<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Exam;
use Carbon\Carbon;

new #[Layout('layouts.dashboard-component')] #[Title('Exams • Practizly')] class extends Component {
    public ?Exam $exam;

    public function mount($slug, Exam $exam)
    {
        $this->exam = Exam::where('slug', $slug)->first();
    }

    #[On('examCreated')]
    #[On('examUpdated')]
    #[On('examDeleted')]
    public function updatedExam()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="space-y-3">
        <div class="flex items-start justify-between gap-2">
            <flux:heading level="1" size="xl">
                {{ Str::of($exam->title)->ucfirst() }}
            </flux:heading>

            <div class="flex items-center justify-start gap-2 no-print">
                <div onclick="printExam()">
                    <div class="hidden md:block">
                        <flux:badge as="button" variant="pill" color="zinc" icon="arrow-down-tray" size="lg">
                            Download
                        </flux:badge>
                    </div>
                    <flux:button variant="ghost" icon="arrow-down-tray" class="block md:hidden" />
                </div>

                <flux:modal.trigger name="delete-exam-{{ $exam->id }}">
                    <div class="hidden md:block">
                        <flux:badge as="button" variant="pill" color="zinc" icon="trash" size="lg">
                            Delete
                        </flux:badge>
                    </div>
                    <flux:button variant="ghost" icon="trash" class="block md:hidden" />
                </flux:modal.trigger>
            </div>
        </div>

        <flux:breadcrumbs class="no-print">
            <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item wire:navigate
                href="/{{ Auth::user()->username }}/subjects/{{ $exam->subject->slug }}">
                {{ Str::of($exam->subject->name)->ucfirst() }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item wire:navigate
                href="/{{ Auth::user()->username }}/subjects/{{ $exam->subject->slug }}/exams">Exams
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ Str::of($exam->title)->ucfirst() }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <livewire:subjects.components.nav-bar :subject="$exam->subject" />

    <!-- Action modals -->
    <livewire:exams.delete :exam="$exam" />

    @if ($exam->type === 'true_or_false')
        <livewire:exams.types.true-or-false :exam="$exam" />
    @elseif($exam->type === 'multiple_choice')
        <livewire:exams.types.multiple-choice :exam="$exam" />
    @elseif($exam->type === 'open_ended')
        <livewire:exams.types.open-ended :exam="$exam" />
    @endif
</div>

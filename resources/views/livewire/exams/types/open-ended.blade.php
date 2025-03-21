<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function reviewOpenEndedExam() {}
}; ?>

<flux:card class="print-remove-b">
    <form wire:submit.prevent="reviewMultipleChoiceExam" class="space-y-6 print">
        @forelse ($exam->openEndedExams as $exam)
            <flux:textarea class="no-print" wire:model.live label="{{ $exam->question }}" placeholder="Type your answer here" />
        @empty
            <p class="no-print">No multiple choice questions yet.</p>
        @endforelse

        <div class="flex items-center no-print">
            <flux:spacer class="lg:hidden" />
            <flux:button type="submit" variant="primary">Submit</flux:button>
        </div>
    </form>
</flux:card>

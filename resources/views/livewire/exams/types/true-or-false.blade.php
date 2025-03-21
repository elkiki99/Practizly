<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function reviewTrueOrFalseExam() {}
}; ?>

<flux:card class="print-remove-b">
    <form wire:submit.prevent="reviewTrueOrFalseExam" class="space-y-6 print">
        @forelse ($exam->trueOrFalseExams as $exam)
            <div>
                <flux:heading>{{ $exam->question }}</flux:heading>
                <flux:subheading class="no-print">Choose the correct answer.</flux:subheading>
            </div>

            <flux:radio.group name="question_{{ $exam->id }}" class="flex items-start gap-4">
                <flux:radio label="True" value="true" />
                <flux:radio label="False" value="false" />
            </flux:radio.group>
        @empty
            <flux:subheading class="no-print">No true or false questions yet.</flux:subheading>
        @endforelse

        <div class="flex items-center no-print">
            <flux:spacer class="lg:hidden" />
            <flux:button type="submit" variant="primary">Submit</flux:button>
        </div>
    </form>
</flux:card>

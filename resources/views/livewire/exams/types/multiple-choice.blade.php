<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function reviewMultipleChoiceExam() {}
}; ?>

<flux:card class="print-remove-b">
    <form wire:submit.prevent="reviewMultipleChoiceExam" class="space-y-6 print">
        @forelse ($exam->multipleChoiceExams as $exam)
            <div>
                <flux:heading level="2" size="lg">{{ $exam->question }}</flux:heading>
                <flux:subheading class="no-print">Choose the correct answer.</flux:subheading>
            </div>

            <flux:radio.group name="answers[{{ $exam->id }}]">
                @foreach (json_decode($exam->options, true) as $option)
                    <flux:radio label="{{ $option }}" value="{{ $option }}" />
                @endforeach
            </flux:radio.group>
        @empty
            <p>No multiple choice questions yet.</p>
        @endforelse

        <div class="flex items-center no-print">
            <flux:spacer class="lg:hidden" />
            <flux:button type="submit" variant="primary">Submit</flux:button>
        </div>
    </form>
</flux:card>

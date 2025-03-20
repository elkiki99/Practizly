<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function reviewMultipleChoice() {}
}; ?>

<flux:card>
    <form wire:submit.prevent="reviewMultipleChoice" class="space-y-6">
        @forelse ($exam->multipleChoiceExams as $exam)
            <div>
                <flux:heading level="2" size="lg">{{ $exam->question }}</flux:heading>
                <flux:subheading>Choose the correct answer.</flux:subheading>
            </div>

            <flux:radio.group name="answers[{{ $exam->id }}]">
                @foreach (json_decode($exam->options, true) as $option)
                    <flux:radio label="{{ $option }}" value="{{ $option }}" />
                @endforeach
            </flux:radio.group>
        @empty
            <p>No multiple choice questions yet.</p>
        @endforelse

        <flux:button type="submit" variant="primary">Submit</flux:button>
    </form>
</flux:card>

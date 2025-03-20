<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function reviewTrueOrFalse() {}
}; ?>

<flux:card>
    <form wire:submit.prevent="reviewTrueOrFalse" class="space-y-6">
        @forelse ($exam->trueOrFalseExams as $exam)
            <div>
                <flux:heading>{{ $exam->question }}</flux:heading>
                <flux:subheading>Choose the correct answer.</flux:subheading>
            </div>

            <flux:radio.group class="flex items-start gap-4">
                <flux:radio label="True" value="true" name="question_{{ $exam->id }}" />
                <flux:radio label="False" value="false" name="question_{{ $exam->id }}" />
            </flux:radio.group>
        @empty
            <flux:subheading>No true or false questions yet.</flux:subheading>
        @endforelse

        <flux:button type="submit" variant="primary">Submit</flux:button>
    </form>
</flux:card>

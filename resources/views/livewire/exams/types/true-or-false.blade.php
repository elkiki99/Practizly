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

<form wire:submit.prevent="reviewTrueOrFalse" class="space-y-6">
    <div>
        @forelse ($exam->trueOrFalseExams as $trueOrFalseExam)
            <div class="flex items-start gap-4 mb-2">
                <flux:heading class="w-2/3">{{ $trueOrFalseExam->question }}</flux:heading>

                <flux:radio.group class="flex items-center gap-4 w-1/3">
                    <flux:radio value="true" name="question_{{ $trueOrFalseExam->id }}" />
                    <label for="true">True</label>
                    <flux:radio value="false" name="question_{{ $trueOrFalseExam->id }}" />
                    <label for="false">False</label>
                </flux:radio.group>
            </div>
        @empty
            <p>No true or false questions yet.</p>
        @endforelse
    </div>
    <flux:button type="submit" variant="primary">Submit</flux:button>
</form>

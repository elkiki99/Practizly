<?php

namespace App\Livewire\Actions;

use App\Models\Exam;
use App\Livewire\Services\GenerateExam\TrueOrFalseExamGenerator;
use App\Livewire\Services\GenerateExam\MultipleChoiceExamGenerator;
use App\Livewire\Services\GenerateExam\OpenEndedExamGenerator;
use App\Livewire\Services\GenerateExam\QuizExamGenerator;

class GenerateExam
{
    /**
     * Generate an exam
     */
    public function __invoke(Exam $exam)
    {
        match ($exam->type) {
            'true_or_false' => (new TrueOrFalseExamGenerator())->generate($exam),
            'multiple_choice' => (new MultipleChoiceExamGenerator())->generate($exam),
            'open_ended' => (new OpenEndedExamGenerator())->generate($exam),
            'quiz' => (new QuizExamGenerator())->generate($exam),
            
            default => throw new \Exception("Exam type not supported"),
        };
    }
}

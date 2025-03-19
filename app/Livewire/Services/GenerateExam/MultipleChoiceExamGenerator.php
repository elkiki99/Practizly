<?php

namespace App\Livewire\Services\GenerateExam;

use App\Models\Exam;
use App\Models\MultipleChoiceExam;
use OpenAI\Laravel\Facades\OpenAI;

class MultipleChoiceExamGenerator
{
    public function generate(Exam $exam)
    {
        $size = $this->checkExamSize($exam);
        $numQuestions = rand($size['min'], $size['max']);

        $prompt = $this->generatePrompt($exam, $numQuestions);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a highly experienced exam generator specializing in multiple-choice questions. You are tasked with creating a high-quality, comprehensive multiple-choice exam based on the provided exam details.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $content = $response['choices'][0]['message']['content'];
        $questions = $this->parseQuestions($content);

        foreach ($questions as $questionData) {
            MultipleChoiceExam::create([
                'exam_id' => $exam->id,
                'question' => $questionData['question'],
                'options' => json_encode($questionData['options']),
                'correct_answer' => $questionData['correct_answer'],
            ]);
        }
    }

    private function checkExamSize(Exam $exam): array
    {
        return match ($exam->size) {
            'short' => ['min' => 5, 'max' => 10],
            'medium' => ['min' => 10, 'max' => 15],
            'long' => ['min' => 15, 'max' => 30],
            default => ['min' => 5, 'max' => 10],
        };
    }

    private function generatePrompt(Exam $exam, int $numQuestions): string
    {
        return "Generate a multiple-choice mock exam with the following specifications:\n" .
            "1. The exam should contain {$numQuestions} questions.\n" .
            "2. The questions should be related to the topic of {$exam->subject->name}.\n" .
            "3. The exam should vary in difficulty as follows: {$exam->difficulty}.\n" .
            "4. For each question, provide four answer options labeled A, B, C, D.\n" .
            "5. Each question should have only one correct answer.\n" .
            "6. The correct answer should be indicated clearly.\n" .
            "7. The questions should be diverse and test the range of knowledge in {$exam->subject->name}.\n" .
            "8. Format:\n" .
            "    1. Question text\n" .
            "    A) Option 1\n" .
            "    B) Option 2\n" .
            "    C) Option 3\n" .
            "    D) Option 4\n" .
            "    Correct answer: X (where X is the correct option)\n" .
            "9. Number of questions: {$numQuestions}\n" .
            "10. Title: {$exam->title}\n" .
            "11. Type: {$exam->type}\n" .
            "12. Difficulty: {$exam->difficulty}\n" .
            "Ensure that each question is challenging, clear, and relevant to the topic.";
    }

    private function parseQuestions(string $content): array
    {
        $lines = explode("\n", $content);
        $questions = [];
        $question = '';
        $options = [];
        $correct_answer = '';

        foreach ($lines as $line) {
            if (preg_match('/^(\d+\..+)\s*$/', $line, $matches)) {
                if ($question) {
                    $questions[] = [
                        'question' => $question,
                        'options' => $options,
                        'correct_answer' => $correct_answer,
                    ];
                }
                $question = trim($matches[1]);
                $options = [];
                $correct_answer = '';
            } elseif (preg_match('/^[A-D]\)\s*(.+)$/', $line, $matches)) {
                $options[] = $matches[1];
            } elseif (preg_match('/^Correct answer: ([A-D])$/', $line, $matches)) {
                $correct_answer = $matches[1];
            }
        }

        return $questions;
    }
}
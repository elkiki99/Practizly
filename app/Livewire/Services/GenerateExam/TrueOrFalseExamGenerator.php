<?php

namespace App\Livewire\Services\GenerateExam;

use App\Models\Exam;
use App\Models\TrueOrFalseExam;
use OpenAI\Laravel\Facades\OpenAI;

class TrueOrFalseExamGenerator
{
    public function generate(Exam $exam)
    {
        $size = $this->checkExamSize($exam);
        $numQuestions = rand($size['min'], $size['max']);

        $prompt = $this->generateTrueFalsePrompt($exam, $numQuestions);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a highly experienced exam generator specializing in true or false questions. You are tasked with creating a high-quality, comprehensive true or false exam based on the provided exam details..'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $content = $response['choices'][0]['message']['content'];
        $questions = $this->parseQuestions($content);

        foreach ($questions as $questionData) {
            TrueOrFalseExam::create([
                'exam_id' => $exam->id,
                'question' => $questionData['question'],
                'answer' => $questionData['answer'],
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

    private function parseQuestions(string $content): array
    {
        $lines = explode("\n", $content);
        $questions = [];

        foreach ($lines as $line) {
            if (preg_match('/^(\d+\..+)\s*-\s*(True|False)$/', $line, $matches)) {
                $questions[] = [
                    'question' => trim($matches[1]),
                    'answer' => $matches[2] === 'True',
                ];
            }
        }

        return $questions;
    }

    private function generateTrueFalsePrompt(Exam $exam, int $numQuestions): string
    {
        return "You are an exam generator. Generate a mock exam with true or false questions based on the following details:\n" .
            "Number of questions: {$numQuestions} (random between min and max based on exam size)\n" .
            "Title: {$exam->title}\n" .
            "Type: {$exam->type}\n" .
            "Difficulty: {$exam->difficulty} (please match the complexity to the difficulty)\n" .
            "Format:\n" .
            "1. Question - Answer (True or False)\n" .
            "Make sure the questions are relevant to the exam type and difficulty level.\n" .
            "Each question should have a clear and straightforward answer.\n";
    }
}

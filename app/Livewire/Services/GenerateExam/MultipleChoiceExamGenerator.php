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

        $cleanedContent = $this->cleanContent($response['choices'][0]['message']['content']);

        $questions = json_decode($cleanedContent, true);

        foreach ($questions as $questionData) {
            MultipleChoiceExam::create([
                'exam_id' => $exam->id,
                'question' => $questionData['question'],
                'options' => json_encode($questionData['options']),
                'correct_answer' => $questionData['correct_answer'],
            ]);
        }
    }

    private function cleanContent($content)
    {
        $content = preg_replace('/^"""\s*/', '', $content);
        $content = preg_replace('/\s*"""$/', '', $content);
    
        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
    
        return $content;
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
        return "Generate a multiple-choice exam with the following specifications:\n" .
            "1. The exam should contain {$numQuestions} questions.\n" .
            "2. The questions should be related to the following topics: " . implode(', ', $exam->topics->pluck('name')->toArray()) . ".\n" .
            "3. The difficulty of the exam should be {$exam->difficulty}.\n" .
            "4. For each question, provide four answer options in an array format.\n" .
            "5. Do **not** include any prefixes like 'A)', 'B)', etc., just provide the answer text.\n" .
            "6. Each question must have exactly one correct answer.\n" .
            "7. The format should be in JSON with the following structure:\n" .
            "    [\n" .
            "        {\n" .
            "            \"question\": \"Question text\",\n" .
            "            \"options\": [\"Option 1\", \"Option 2\", \"Option 3\", \"Option 4\"],\n" .
            "            \"correct_answer\": \"Option 1\"\n" .
            "        },\n" .
            "        ...\n" .
            "    ]\n" .
            "8. The JSON should be clean, with no additional text or explanation.\n" .
            "9. The correct answer must match exactly one of the provided options.";
    }
}
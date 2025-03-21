<?php

namespace App\Livewire\Services\GenerateExam;

use App\Models\Exam;
use App\Models\OpenEndedExam;
use OpenAI\Laravel\Facades\OpenAI;

class OpenEndedExamGenerator
{
    public function generate(Exam $exam)
    {
        $size = $this->checkExamSize($exam);
        $numQuestions = rand($size['min'], $size['max']);

        $prompt = $this->generatePrompt($exam, $numQuestions);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a highly experienced exam generator specializing in open ended questions. You are tasked with creating a high-quality, comprehensive open ended essay exam based on the provided exam details.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
        
        $cleanedContent = $this->cleanContent($response['choices'][0]['message']['content']);
        $questions = json_decode($cleanedContent, true);

        foreach ($questions as $questionData) {
            OpenEndedExam::create([
                'exam_id' => $exam->id,
                'question' => $questionData['question'],
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

    private function cleanContent($content)
    {
        $content = preg_replace('/^"""\s*/', '', $content);
        $content = preg_replace('/\s*"""$/', '', $content);

        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);

        return $content;
    }

    private function generatePrompt(Exam $exam, int $numQuestions): string
    {
        return "Generate an open-ended exam with the following specifications:\n" .
            "1. The exam should contain {$numQuestions} questions.\n" .
            "2. The questions should be related to the following topics: " . implode(', ', $exam->topics->pluck('name')->toArray()) . ".\n" .
            "3. The difficulty of the exam should be {$exam->difficulty}.\n" .
            "4. Each question should require a detailed, explanatory answer from the student.\n" .
            "5. The format should be in JSON with the following structure:\n" .
            "    [\n" .
            "        {\n" .
            "            \"question\": \"Question text\"\n" .
            "        },\n" .
            "        ...\n" .
            "    ]\n" .
            "6. The JSON should be clean, with no additional text or explanation.";
    }
}
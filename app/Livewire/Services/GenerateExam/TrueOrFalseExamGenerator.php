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

        $prompt = $this->generatePrompt($exam, $numQuestions);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a highly experienced exam generator specializing in true or false questions. You are tasked with creating a high-quality, comprehensive true or false exam based on the provided exam details.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $cleanedContent = $this->cleanContent($response['choices'][0]['message']['content']);
        $questions = json_decode($cleanedContent, true);

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
        return "Generate a true or false exam with the following specifications:\n" .
            "1. The exam should contain {$numQuestions} questions.\n" .
            "2. The questions should be related to the following topics: " . implode(', ', $exam->topics->pluck('name')->toArray()) . ".\n" .
            "3. The difficulty of the exam should be {$exam->difficulty}.\n" .
            "4. Each question should have only two possible answers: 'True' or 'False'.\n" .
            "5. The format should be in JSON with the following structure:\n" .
            "    [\n" .
            "        {\n" .
            "            \"question\": \"Question text\",\n" .
            "            \"answer\": true\n" .
            "        },\n" .
            "        ...\n" .
            "    ]\n" .
            "6. The JSON should be clean, with no additional text or explanation.";
    }
}

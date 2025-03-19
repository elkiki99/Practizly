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

        // Limpieza del contenido recibido para quitar comillas triples y bloques de código
        $cleanedContent = $this->cleanContent($response['choices'][0]['message']['content']);

        // Decodificar el contenido limpio a JSON
        $questions = json_decode($cleanedContent, true);

        // Guardar las preguntas en la base de datos
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
        // Eliminar las comillas triples al principio y al final
        $content = preg_replace('/^"""\s*/', '', $content);
        $content = preg_replace('/\s*"""$/', '', $content);
    
        // Eliminar el bloque de código JSON (```json) y (```)
        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
    
        // Retornar el contenido limpio
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
            "2. The questions should be related to the topic of {$exam->subject->name}.\n" .
            "3. The difficulty of the exam should be {$exam->difficulty}.\n" .
            "4. For each question, provide four answer options: A, B, C, D.\n" .
            "5. Each question must have exactly one correct answer.\n" .
            "6. The format should be in JSON with the following structure:\n" .
            "    [\n" .
            "        {\n" .
            "            \"question\": \"Question text\",\n" .
            "            \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],\n" .
            "            \"correct_answer\": \"A\"\n" .
            "        },\n" .
            "        ...\n" .
            "    ]\n" .
            "7. The JSON should be clean, with no additional text or explanation.\n" .
            "8. The correct answer must be one of the options: A, B, C, or D.";
    }
}
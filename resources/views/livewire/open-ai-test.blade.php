<?php

use Livewire\Volt\Component;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\TrueOrFalseExam;
use Livewire\Attributes\On;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;
    public array $mockExam = [];

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    #[On('genereateExam')]
    public function loadExam($examId)
    {
        $this->exam = Exam::find($examId);
        $this->generateMockExam();
    }

    public function generateMockExam()
    {
        if (!$this->exam) {
            return;
        }

        // Definición de la cantidad de preguntas según el tamaño del examen
        switch ($this->exam->size) {
            case 'short':
                $minQuestions = 5;
                $maxQuestions = 10;
                break;
            case 'medium':
                $minQuestions = 10;
                $maxQuestions = 15;
                break;
            case 'long':
                $minQuestions = 15;
                $maxQuestions = 30;
                break;
            default:
                $minQuestions = 5;
                $maxQuestions = 10;
                break;
        }

        $numQuestions = rand($minQuestions, $maxQuestions);

        // Solicitarle a OpenAI que genere un examen con preguntas y respuestas en formato "Pregunta - Respuesta"
        $prompt = "Genera un examen de prueba con preguntas tipo 'True or False'. El formato debe ser el siguiente:\n";
        $prompt .= "1. Pregunta - Respuesta\n";
        $prompt .= "Las respuestas deben ser 'Verdadero' o 'Falso', tratando de que siempre hayan respuestas falsas y verdaderas.\n";
        $prompt .= "Número de preguntas: {$numQuestions}\n\n";
        $prompt .= "Título: {$this->exam->title}\n";
        $prompt .= "Tipo: {$this->exam->type}\n";
        $prompt .= "Dificultad: {$this->exam->difficulty}\n\n";

        // Llamar a OpenAI
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'system', 'content' => 'Eres un generador de exámenes altamente eficiente.'], ['role' => 'user', 'content' => $prompt]],
        ]);

        $content = $response['choices'][0]['message']['content'];

        // Analizar las preguntas y respuestas generadas
        $questions = $this->parseTrueOrFalseQuestions($content);

        // Insertar las preguntas y respuestas en la base de datos
        foreach ($questions as $questionData) {
            TrueOrFalseExam::create([
                'exam_id' => $this->exam->id,
                'question' => $questionData['question'],
                'answer' => $questionData['answer'], // true o false
            ]);
        }

        // dd($content, $questions);
    }

    public function parseTrueOrFalseQuestions(string $content)
    {
        // Aquí analizamos el contenido generado por OpenAI
        $lines = explode("\n", $content);
        $questions = [];

        foreach ($lines as $line) {
            // Detectar si la línea tiene el formato de "Pregunta - Respuesta"
            if (preg_match('/^(\d+\..+)\s*-\s*(Verdadero|Falso)$/', $line, $matches)) {
                $questions[] = [
                    'question' => trim($matches[1]), // La pregunta
                    'answer' => $matches[2] === 'Verdadero' ? true : false, // True o False
                ];
            }
        }

        return $questions;
    }
}; ?>

<div></div>

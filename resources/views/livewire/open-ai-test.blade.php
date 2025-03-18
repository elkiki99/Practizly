<?php

use Livewire\Volt\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Livewire\Attributes\On;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;
    public array $mockExam = [];

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    #[On('create-new-ai-exam')]
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

        // Generar el número de preguntas aleatorio dentro del rango
        $numQuestions = rand($minQuestions, $maxQuestions);

        // Construir el prompt con el número de preguntas adecuado
        $prompt = "Genera un examen de prueba basado en el siguiente contenido:\n\n";
        $prompt .= "Título: {$this->exam->title}\n";
        $prompt .= "Tipo: {$this->exam->type}\n";
        $prompt .= "Dificultad: {$this->exam->difficulty}\n";
        $prompt .= "Número de preguntas: {$numQuestions}\n\n"; // Aquí usamos el rango calculado

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'system', 'content' => 'Eres un generador de exámenes altamente eficiente.'], ['role' => 'user', 'content' => $prompt]],
        ]);

        // dd($response);

        // Procesar la respuesta
        $content = $response['choices'][0]['message']['content'];
        
        dd($content);

        // $this->mockExam = $this->parseMockExamContent($content); // Parse the exam content

        // dd($this->mockExam); // Display parsed exam for debugging
    }
}; ?>

<div></div>

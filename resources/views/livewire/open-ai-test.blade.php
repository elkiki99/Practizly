<?php

use Livewire\Volt\Component;
use OpenAI\Laravel\Facades\OpenAI;

new class extends Component {
    public $result;

    public function with()
    {
        return [
            ($response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [['role' => 'user', 'content' => 'How much characters can you type per response?']],
            ])),
            ($this->result = $response->choices[0]->message->content),
        ];
    }
}; ?>

<div>
    {{ $result }}
</div>

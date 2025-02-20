<?php

use Livewire\Volt\Component;
use App\Models\Topic; // Asegúrate de importar el modelo Topic
use App\Models\Subject; // Asegúrate de importar el modelo Subject

new class extends Component {
    public string $variant = '';
    public string $name = '';
    public string $description = '';
    public string $color = '';
    public ?int $goal = null;
    public ?int $completion_percentage = null;
    public ?\DateTimeInterface $last_studied_at = null;
    public bool $is_favorite = false;
    public bool $createTopic = false;
    public string $topicName = '';
    public ?int $topicId = null; // Para almacenar el ID del tema recién creado
    public $topics; // Variable pública para almacenar los temas

    public function mount()
    {
        // Cargar todos los topics disponibles para mostrarlos en el select
        $this->topics = Topic::all(); // O usa el método adecuado para cargar los temas
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'color' => 'required|string|max:20',
            'goal' => 'nullable|integer|min:1',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'last_studied_at' => 'nullable|date',
            'is_favorite' => 'boolean|default:false',
            'topicName' => 'nullable|string|max:255', // Aseguramos que topicName sea opcional
        ];
    }

    public function createTopic()
    {
        dd('hi');
        
        if ($this->topicName) {
            $topic = Topic::create([
                'name' => $this->topicName,
            ]);

            $this->topicId = $topic->id; // Guardamos el ID del nuevo topic

            // Actualizar los topics disponibles después de crear un nuevo topic
            $this->topics = Topic::all(); // Recargar los temas
        }
    }

    public function createSubject()
    {
        $subject = Subject::create([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'goal' => $this->goal,
            'completion_percentage' => $this->completion_percentage,
            'last_studied_at' => $this->last_studied_at,
            'is_favorite' => $this->is_favorite,
        ]);

        if ($this->topicId) {
            $subject->topics()->attach($this->topicId); // Asociar el topic al subject
        }

        $this->reset(); // Limpiar campos
    }
}; ?>

<flux:modal variant="{{ $variant }}" name="create-subject" class="space-y-6 md:w-96" x-data="{ createTopic: false }"
    x-on:modal-close.window="createTopic = false">
    <div>
        <flux:heading size="lg">New subject</flux:heading>
        <flux:subheading>Create a new subject.</flux:subheading>
    </div>

    <flux:field>
        <flux:label class="mb-2">Subject name</flux:label>
        <flux:input placeholder="Physics" wire:model='name' autofocus required autocomplete="name" />
        <flux:error name="name" />
    </flux:field>

    <flux:field>
        <flux:label class="mb-2">Subject description</flux:label>
        <flux:input placeholder="Physics - 1st semester 2025" wire:model='description' autofocus required />
        <flux:error name="description" />
    </flux:field>

    <flux:field>
        <div class="flex items-center justify-between mb-2">
            <flux:label>Pick a topic</flux:label>
            <flux:button as="link" size="sm" variant="subtle" icon-trailing="plus"
                x-on:click="createTopic = true">New topic</flux:button>
        </div>

        <flux:select variant="listbox" placeholder="Subject topic" wire:model="topicId">
            @forelse($topics as $topic)
                <flux:select.option value="{{ $topic->id }}">{{ $topic->name }}</flux:select.option>
            @empty
                <flux:error>No topics found</flux:error>
            @endforelse
        </flux:select>
    </flux:field>

    <flux:field x-show="createTopic">
        <flux:label class="mb-2">New topic</flux:label>
        <flux:input placeholder="Quantum mechanics" @keydown.enter="$wire.createTopic(); createTopic = false"
            wire:model="topicName">
        </flux:input>
        <flux:error name="topicName" />
    </flux:field>

    <flux:field>
        <flux:label class="mb-2">Subject color</flux:label>

        <flux:select variant="listbox" placeholder="Select color">
            <flux:select.option value="green"><span
                    class="inline-block mr-2 bg-green-500 rounded-full size-2"></span>Green</flux:select.option>
            <flux:select.option value="red"><span class="inline-block mr-2 bg-red-500 rounded-full size-2"></span>Red
            </flux:select.option>
            <flux:select.option value="blue"><span
                    class="inline-block mr-2 bg-blue-500 rounded-full size-2"></span>Blue</flux:select.option>
            <flux:select.option value="yellow"><span
                    class="inline-block mr-2 bg-yellow-500 rounded-full size-2"></span>Yellow</flux:select.option>
            <flux:select.option value="orange"><span
                    class="inline-block mr-2 bg-orange-500 rounded-full size-2"></span>Orange</flux:select.option>
            <flux:select.option value="purple"><span
                    class="inline-block mr-2 bg-purple-500 rounded-full size-2"></span>Purple</flux:select.option>
            <flux:select.option value="black"><span class="inline-block mr-2 bg-black rounded-full size-2"></span>Black
            </flux:select.option>
            <flux:select.option value="gray"><span
                    class="inline-block mr-2 bg-gray-500 rounded-full size-2"></span>Gray</flux:select.option>
        </flux:select>
    </flux:field>

    <flux:field variant="inline" class="flex justify-between w-full">
        <flux:switch wire:model.live="is_favorite" />
        <flux:label>Mark as favorite</flux:label>
        <flux:error name="is_favorite" />
    </flux:field>

    <div class="flex">
        <flux:spacer />
        <flux:button type="submit" variant="primary" wire:click="createSubject">Create subject</flux:button>
    </div>
</flux:modal>

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On};
use App\Models\Assignment;

new #[Layout('layouts.dashboard-component')] #[Title('Assignments • Practizly')] class extends Component {
    public ?Assignment $assignment;

    public function mount($slug, Assignment $assignment)
    {
        $this->assignment = Assignment::where('slug', $slug)->first();
    }

    #[On('assignmentUpdated')]
    public function updatedAssignment()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div>
    Hi
</div>

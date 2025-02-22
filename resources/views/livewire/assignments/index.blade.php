<?php

use Livewire\Volt\Component;
use App\Models\Assignment;

new class extends Component {
    public $assignments = [];

    public function mount()
    {
        $this->assignments = Assignment::all();
    }
}; ?>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
    @forelse($assignments as $assignment)
        <flux:card>
            <div class="space-y-6">
                <flux:heading size="lg">{{ $assignment->title }}</flux:heading>
                <flux:subheading>{{ $assignment->subject->name }}</flux:subheading>
            </div>
            <flux:subheading>{{ $assignment->description }}</flux:subheading>
        </flux:card>
    @empty
        <flux:subheading>You don't have any assignments yet!</flux:subheading>
    @endforelse
</div>

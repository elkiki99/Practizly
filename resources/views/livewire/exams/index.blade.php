<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {
    public $exams;

    public function mount()
    {
        $this->exams = Exam::all();
    }
}; ?>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($exams as $exam)
        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $exam->title }}</flux:heading>
                <flux:subheading>{{ $exam->type }}</flux:subheading>
                <flux:subheading>{{ $exam->difficulty }}</flux:subheading>
                <flux:subheading>{{ $exam->size }}</flux:subheading>
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button as="link" variant="primary" href="{{-- route('exams.show', $exam) --}}">Show</flux:button>
            </div>
        </flux:card>
    @empty
        <flux:subheading>You don't have any exams yet!</flux:subheading>
    @endforelse
</div>

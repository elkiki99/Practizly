<?php

use Livewire\Volt\Component;
use App\Models\Subject;

new class extends Component {
    public $subjects;

    public function mount()
    {
        $this->subjects = Subject::where('user_id', auth()->user()->id)->get();
    }
}; ?>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($subjects as $subject)
        <flux:card class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $subject->name }}</flux:heading>
                <flux:subheading>{{ $subject->description }}</flux:subheading>
            </div>

            <div class="flex">
                <flux:spacer />
                <flux:button as="link" variant="primary" href="{{-- route('subjects.show', $subject) --}}">Show</flux:button>
            </div>
        </flux:card>
    @empty
        <flux:subheading>You don't have any subjects yet!</flux:subheading>
    @endforelse
</div>
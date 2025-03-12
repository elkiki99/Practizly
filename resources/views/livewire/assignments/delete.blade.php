<?php

use Livewire\Volt\Component;
use App\Models\Assignment;

new class extends Component {
    public ?Assignment $assignment;

    public function mount(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function deleteAssignment()
    {
        $this->assignment->delete();

        Flux::toast(heading: 'Assignment deleted', text: 'Your assignment was deleted successfully', variant: 'danger');

        $url = request()->header('Referer');

        if ($url === url()->route('assignments.index', [Auth::user()->username]) || $url === url()->route('subjects.components.assignments', [Auth::user()->username, $this->assignment->subject->slug])) {
            $this->dispatch('assignmentDeleted');
            Flux::modals()->close();
        } else {
            Flux::modals()->close();
            $this->redirectRoute('subjects.components.assignments', [Auth::user()->username, $this->assignment->subject->slug], navigate: true);
        }
    }
}; ?>

<form wire:submit.prassignment="deleteAssignment">
    <flux:modal name="delete-assignment-{{ $assignment->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete assignment?</flux:heading>

                <flux:subheading>
                    <p>You're about to delete the assignment {{ $assignment->name }}.</p>
                    <p>Are you sure you want to proceed?</p>
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger">Delete assignment</flux:button>
            </div>
        </div>
    </flux:modal>
</form>

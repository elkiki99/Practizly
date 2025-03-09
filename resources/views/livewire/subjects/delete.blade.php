<?php

use Livewire\Volt\Component;
use App\Models\Subject;

new class extends Component {
    public ?Subject $subject;

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
    }

    public function deleteSubject()
    {
        $this->subject->delete();
        
        $this->dispatch('subjectDeleted');

        Flux::toast(heading: 'Subject deleted', text: 'Your subject was deleted successfully', variant: 'danger');

        Flux::modals()->close();
    }
}; ?>

<form wire:submit.prevent="deleteSubject">
    <flux:modal name="delete-subject-{{ $subject->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete subject?</flux:heading>
    
                <flux:subheading>
                    <p>You're about to delete the subject {{ $subject->name }}.</p>
                    <p>All exams and assignments associated with this subject will be deleted.</p>
                </flux:subheading>
            </div>
    
            <div class="flex gap-2">
                <flux:spacer />
    
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
    
                <flux:button type="submit" variant="danger">Delete subject</flux:button>
            </div>
        </div>
    </flux:modal>
</form>

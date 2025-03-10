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

        Flux::toast(heading: 'Subject deleted', text: 'Your subject was deleted successfully', variant: 'danger');

        $url = request()->header('Referer');
        
        if($url  === url()->route('subjects.index', [Auth::user()->username])) {
            $this->dispatch('subjectDeleted');
            Flux::modals()->close();
        } else {
            Flux::modals()->close();
            $this->redirectRoute('subjects.index', [Auth::user()->username], navigate: true);
        }
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

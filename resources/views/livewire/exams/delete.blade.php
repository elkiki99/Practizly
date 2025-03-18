<?php

use Livewire\Volt\Component;
use App\Models\Exam;

new class extends Component {
    public ?Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function deleteExam()
    {
        $this->exam->delete();

        Flux::toast(heading: 'Exam deleted', text: 'Your exam was deleted successfully', variant: 'danger');

        $url = request()->header('Referer');

        if ($url === url()->route('exams.index', [Auth::user()->username]) || $url === url()->route('subjects.components.exams', [Auth::user()->username, $this->exam->subject->slug])) {
            $this->dispatch('examDeleted');
            Flux::modals()->close();
        } else {
            Flux::modals()->close();
            $this->redirectRoute('subjects.components.exams', [Auth::user()->username, $this->exam->subject->slug], navigate: true);
        }
    }
}; ?>

<form wire:submit.prevent="deleteExam">
    <flux:modal name="delete-exam-{{ $exam->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete exam?</flux:heading>
    
                <flux:subheading>
                    <p>You're about to delete the exam {{ $exam->title }}.</p>
                    <p>Are you sure you want to proceed?</p>
                </flux:subheading>
            </div>
    
            <div class="flex gap-2">
                <flux:spacer />
    
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
    
                <flux:button type="submit" variant="danger">Delete exam</flux:button>
            </div>
        </div>
    </flux:modal>
</form>


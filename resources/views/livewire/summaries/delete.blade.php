<?php

use Livewire\Volt\Component;
use App\Models\Summary;

new class extends Component {
    public ?Summary $summary;

    public function mount(Summary $summary)
    {
        $this->summary = $summary;
    }

    public function deleteSummary()
    {
        $this->summary->delete();

        Flux::toast(heading: 'Summary deleted', text: 'Your summary was deleted successfully', variant: 'danger');

        $url = request()->header('Referer');

        if ($url === url()->route('summaries.index', [Auth::user()->username]) || $url === url()->route('subjects.components.summaries', [Auth::user()->username, $this->summary->subject->slug])) {
            $this->dispatch('summaryDeleted');
            Flux::modals()->close();
        } else {
            Flux::modals()->close();
            $this->redirectRoute('subjects.components.summaries', [Auth::user()->username, $this->summary->subject->slug], navigate: true);
        }
    }
}; ?>

<form wire:submit.prevent="deleteSummary">
    <flux:modal name="delete-summary-{{ $summary->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete summary?</flux:heading>
    
                <flux:subheading>
                    <p>You're about to delete the summary {{ $summary->title }}.</p>
                    <p>Are you sure you want to proceed?</p>
                </flux:subheading>
            </div>
    
            <div class="flex gap-2">
                <flux:spacer />
    
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
    
                <flux:button type="submit" variant="danger">Delete summary</flux:button>
            </div>
        </div>
    </flux:modal>
</form>


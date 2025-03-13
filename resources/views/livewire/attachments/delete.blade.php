<?php

use Livewire\Volt\Component;
use App\Models\Attachment;

new class extends Component {
    public ?Attachment $attachment;

    public function mount(Attachment $attachment)
    {
        $this->attachment = $attachment;
    }

    public function deleteAttachment()
    {
        $this->attachment->delete();

        Flux::toast(heading: 'Attachment deleted', text: 'Your attachment was deleted successfully', variant: 'danger');

        $this->dispatch('attachmentDeleted');

        Flux::modals()->close();
    }
}; ?>

<form wire:submit.prevent="deleteAttachment">
    <flux:modal name="delete-attachment-{{ $attachment->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete attachment?</flux:heading>

                <flux:subheading>
                    <p>You're about to delete the attachment {{ $attachment->name }}.</p>
                    <p>Are you sure you want to proceed?</p>
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger">Delete attachment</flux:button>
            </div>
        </div>
    </flux:modal>
</form>

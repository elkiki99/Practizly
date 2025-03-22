<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Reactive;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\Attachment;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|file|mimes:pdf|max:10240')]
    public $file;

    #[Validate('required|exists:topics,id')]
    #[Reactive]
    public $topic_id = null;

    public function mount($topic_id)
    {
        $this->topic_id = $topic_id;
    }

    public function createAttachment()
    {
        $this->validate();

        $topic = Topic::where('id', $this->topic_id)->first();

        $fileName = Str::slug("{$topic->name} {$topic->subject->name} attachment-for-exam", '-');
        $filePath = $this->file->storeAs('attachments', "{$fileName}.{$this->file->getClientOriginalExtension()}", 'public');

        Attachment::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'attachable_type' => Topic::class,
            'attachable_id' => $topic->id,
            'size' => $this->file->getSize(),
        ]);

        if ($this->file) {
            $this->reset(['file']);
            
            $this->dispatch('attachmentCreated');
            
            Flux::toast(heading: 'Attachment created', text: 'Your attachment was created successfully', variant: 'success');
            
            $this->modal('create-attachment')->close();
        } else {
            $this->reset(['file']);

            Flux::toast(heading: 'Attachment error', text: 'There was an error creating your attachment', variant: 'warning');
            
            $this->modal('create-attachment')->close();
        }
    }
}; ?>

<flux:field x-show="createAttachment">
    <flux:label class="mb-2">New attachment</flux:label>
    
    <div class="flex items-center gap-2 mb-3">
        <flux:input accept=".pdf" type="file" wire:model="file"></flux:input>

        <flux:button class="px-2" variant="ghost" wire:click.prevent='createAttachment' icon="plus">
        </flux:button>
    </div>

    <flux:error name="file" />
</flux:field>

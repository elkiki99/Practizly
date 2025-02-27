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

    #[Validate('required|file|mimes:jpg,jpeg,png,webp,doc,docx,pdf|max:10240')]
    public $attachment;

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

        $topic = Topic::find($this->topic_id);

        $fileName = Str::slug("{$topic->name} {$topic->subject->name} attachment", '-');
        $filePath = $this->attachment->storeAs('attachments', "{$fileName}.{$this->attachment->getClientOriginalExtension()}", 'public');

        Attachment::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'attachable_type' => Topic::class,
            'attachable_id' => $topic->id,
        ]);

        $this->reset(['attachment']);

        $this->dispatch('attachmentCreated');

        Flux::toast(heading: 'Attachment created', text: 'Your attachment was created successfully', variant: 'success');
    }
}; ?>

<flux:field x-show="createAttachment">
    <flux:label class="mb-2">New attachment</flux:label>
    <div class="flex items-center gap-2 mb-3">
        <flux:input type="file" wire:model="attachment" required></flux:input>

        <flux:button class="px-2" variant="ghost" wire:click.prevent='createAttachment' icon="plus">
        </flux:button>
    </div>

    <flux:error name="attachment" />
</flux:field>

<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\Assignment;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Event;

new class extends Component {
    use WithFileUploads;

    public ?Assignment $assignment;

    public string $title = '';
    public string $guidelines = '';
    public $attachments = [];
    public ?Carbon $due_date = null;
    public string $status = '';
    public string $slug = '';

    public $topic;
    public $subject;
    public $subjects = [];
    public $topics = [];

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'guidelines' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,doc,docx,pdf|max:10240',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,completed',
            'topic' => 'required|exists:topics,id',
            'subject' => 'required|exists:subjects,id',
            'slug' => ['required', Rule::unique('assignments')->ignore($this->assignment)],
        ];
    }

    public function mount(Assignment $assignment)
    {
        $this->assignment = $assignment;
        $this->title = $assignment->title;
        $this->guidelines = $assignment->guidelines;
        $this->due_date = $assignment->due_date ? Carbon::parse($assignment->due_date) : null;
        $this->status = $assignment->status;
        $this->topic = $assignment->topic_id;
        $this->subject = $assignment->topic->subject_id;
        $this->slug = $assignment->slug;
        $this->subjects = Auth::user()->subjects()->latest()->get();
        $this->topics = Topic::where('subject_id', $this->subject)->get();
    }

    public function editAssignment()
    {
        $this->validate();

        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $counter = 1;

        if ($this->slug !== $baseSlug) {
            while (Event::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $this->assignment->update([
            'title' => $this->title,
            'slug' => $slug,
            'guidelines' => $this->guidelines,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'topic_id' => $this->topic,
        ]);

        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachmentFile) {
                $fileName = Str::slug("{$this->assignment->title} {$this->assignment->topic->name} {$this->assignment->topic->subject->name} assignment", '-');
                $filePath = $attachmentFile->storeAs('attachments', "{$fileName}.{$attachmentFile->getClientOriginalExtension()}", 'public');

                $this->assignment->attachments()->create([
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'size' => $attachmentFile->getSize(),
                ]);
            }
        }

        Flux::toast(heading: 'Assignment updated', text: 'Your assignment was updated successfully', variant: 'success');

        $url = request()->header('Referer');

        if ($url === url()->route('assignments.index', [Auth::user()->username]) || $url === url()->route('subjects.components.assignments', [Auth::user()->username, $this->assignment->subject->slug])) {
            $this->dispatch('assignmentUpdated');
            Flux::modals()->close();
        } else {
            // Check slug to redirect to new url
            if ($this->slug !== $slug) {
                Flux::modals()->close();
                $this->redirectRoute('assignments.show', ['slug' => $slug, 'user' => Auth::user()->username], navigate: true);
            } else {
                $this->dispatch('assignmentUpdated');
                Flux::modals()->close();
            }
        }
    }
}; ?>

<form wire:submit.prevent="editAssignment">
    <flux:modal variant="flyout" name="edit-assignment-{{ $assignment->id }}" class="space-y-6 w-96" x-data="{ createTopic: false }"
        x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
        <div>
            <flux:heading size="lg">New assignment</flux:heading>
            <flux:subheading>Create a new assignment.</flux:subheading>
        </div>

        <flux:field>
            <div class="flex items-center gap-2 mb-2">
                <flux:label>Assignment title</flux:label>
                <flux:tooltip toggleable position="left">
                    <flux:button icon="information-circle" size="sm" variant="ghost" />

                    <flux:tooltip.content class="max-w-[20rem] space-y-2">
                        <p>We recommend giving your assignment a descriptive name for better organization.</p>
                    </flux:tooltip.content>
                </flux:tooltip>
            </div>
            <flux:input type="text" required wire:model="title" placeholder="Calculate quarterly revenue" autofocus
                autocomplete="name" />
        </flux:field>

        <flux:textarea required wire:model="guidelines" label="Assignment guidelines"
            placeholder="Use financial statements to support your analysis, include charts, and ensure all calculations are accurate." />

        <flux:select required label="Assignment subject" searchable variant="listbox" wire:model.live="subject"
            placeholder="Select subject">
            @forelse($subjects as $item)
                <flux:select.option value="{{ $item->id }}">
                    {{ $item->name }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>Create a new subject first</flux:select.option>
            @endforelse
        </flux:select>

        <flux:field>
            <div class="flex items-center justify-between mb-2">
                <flux:label>Assignment topic</flux:label>
                <flux:button as="link" size="xs" variant="subtle" icon-trailing="plus"
                    x-on:click="createTopic = true">New topic</flux:button>
            </div>

            <flux:select wire:key="{{ $subject }}" required searchable variant="listbox" wire:model="topic"
                placeholder="Select topic">
                @forelse($topics as $topic)
                    <flux:select.option value="{{ $topic->id }}">{{ $topic->name }}
                    </flux:select.option>
                @empty
                    <flux:select.option disabled>No topics found</flux:select.option>
                @endforelse
            </flux:select>

            <flux:error name="topic" />
        </flux:field>

        @if ($subject)
            <livewire:components.topics.create :subject_id="$subject" />
        @endif

        <flux:date-picker required label="Assignment due date" wire:model="due_date">
            <x-slot name="trigger">
                <flux:date-picker.input />
            </x-slot>
        </flux:date-picker>

        <flux:radio.group required wire:model="status" label="Assignment status" variant="segmented">
            <flux:radio value="pending" icon="clock" label="Pending" />
            <flux:radio value="completed" icon="document-check" label="Completed" />
        </flux:radio.group>

        <flux:input type="file" wire:model="attachments" label="Assignment attachments" multiple />

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="primary">Update assignment</flux:button>
        </div>
    </flux:modal>
</form>

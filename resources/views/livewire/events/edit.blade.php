<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\Topic;
use Carbon\Carbon;

new class extends Component {
    public ?Event $event;

    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $type = '';
    public ?Carbon $date = null;
    public string $note = '';
    public string $status = '';

    public $topic;
    public $subject;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:test,exam,evaluation,oral_presentation,assignment',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,completed',
            'topic' => 'required|exists:topics,id',
            'subject' => 'required|exists:subjects,id',
            'slug' => ['required', Rule::unique('events')->ignore($this->event)],
        ];
    }

    public $subjects = [];
    public $topics = [];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->name = $this->event->name;
        $this->description = $this->event->description ?? '';
        $this->type = $this->event->type;
        $this->date = Carbon::parse($this->event->date);
        $this->note = $this->event->note ?? '';
        $this->status = $this->event->status;
        $this->topic = $this->event->topics->pluck('id');
        $this->subject = $this->event->subject->id;
        $this->slug = $this->event->slug;

        $this->subjects = Auth::user()->subjects()->latest()->get();
        $this->topics = Topic::where('subject_id', $this->subject)->get();
    }

    #[On('subjectCreated')]
    public function updatedSubject($subject = null)
    {
        $this->subjects = Auth::user()->subjects()->latest()->get();

        if ($this->subjects->count() === 1) {
            $this->subject = $this->subjects->first()->id;
        }

        if (!empty($subject)) {
            $this->topics = Topic::where('subject_id', $subject)->get();

            if ($this->topics->count() === 1) {
                $this->topic = $this->topics->first()->id;
            } else {
                $this->topic = null;
            }
        }
    }

    #[On('topicCreated')]
    public function updatedTopic($topic = null)
    {
        $this->topics = Topic::where('subject_id', $this->subject)->get();

        if ($this->topics->count() === 1) {
            $this->topic = $this->topics->first()->id;
        }
    }

    public function updateEvent()
    {
        $this->validate();

        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        if ($this->slug !== $baseSlug) {
            while (Event::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $this->event->update([
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description,
            'type' => $this->type,
            'date' => $this->date,
            'note' => $this->note,
            'status' => $this->status,
        ]);

        $this->event->topics()->sync($this->topic);

        Flux::toast(heading: 'Event updated', text: 'Your event was updated successfully', variant: 'success');

        $url = request()->header('Referer');

        if ($url === url()->route('calendar', [Auth::user()->username])) {
            $this->dispatch('eventUpdated');
            Flux::modals()->close();
        } else {
            // Check slug to redirect to new url
            if ($this->slug !== $slug) {
                Flux::modals()->close();
                $this->redirectRoute('events.show', ['slug' => $slug, 'user' => Auth::user()->username], navigate: true);
            } else {
                $this->dispatch('eventUpdated');
                Flux::modals()->close();
            }
        }
    }
};
?>

<form wire:submit.prevent="updateEvent">
    <flux:modal variant="flyout" name="edit-event-{{ $event->id }}" class="space-y-6 w-96" x-data="{ createTopic: false }"
        x-init="window.addEventListener('topicCreated', () => { createTopic = false })">
        <div>
            <flux:heading size="lg">Edit event</flux:heading>
            <flux:subheading>Edit {{ $event->name }} event.</flux:subheading>
        </div>

        <!-- Name -->
        <flux:input label="Event name" placeholder="Algebra test" wire:model='name' autofocus required
            autocomplete="name" />

        <!-- Description -->
        <flux:textarea label="Event description" placeholder="Algebra test, topics: functions, polynomials"
            wire:model='description' rows="2"></flux:textarea>

        <flux:select required label="Event subject" searchable variant="listbox" wire:model.live="subject"
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
                <flux:label>Event topic</flux:label>
                <flux:button as="link" size="xs" variant="subtle" icon-trailing="plus"
                    x-on:click="createTopic = true">New topic</flux:button>
            </div>

            <flux:select required multiple searchable variant="listbox" wire:model="topic" placeholder="Select topic"
                selected-suffix="{{ __('topics selected') }}">
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

        <!-- Type -->
        <flux:select required placeholder="Select type" variant="listbox" required wire:model='type' label="Event type">
            <flux:select.option value="test">Test</flux:select.option>
            <flux:select.option value="exam">Exam</flux:select.option>
            <flux:select.option value="evaluation">Evaluation</flux:select.option>
            <flux:select.option value="oral_presentation">Oral presentation</flux:select.option>
            <flux:select.option value="assignment">Assignment</flux:select.option>
        </flux:select>

        <!-- Date -->
        <flux:date-picker label="Event date" required wire:model='date'>
            <x-slot name="trigger">
                <flux:date-picker.input />
            </x-slot>
        </flux:date-picker>

        <!-- Note -->
        <flux:textarea label="Event note" placeholder="Remember to create a summary on polynomials for this test."
            wire:model='note' rows="2"></flux:textarea>

        <!-- Status -->
        <flux:radio.group wire:model='status' required variant="segmented" label="Status">
            <flux:radio icon="clock" value="pending" label="Pending" />
            <flux:radio icon="check-circle" value="completed" label="Completed" />
        </flux:radio.group>

        <div class="flex justify-between">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Edit event</flux:button>
        </div>
    </flux:modal>
</form>

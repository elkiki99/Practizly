<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On, Computed};
use App\Models\Subject;

new #[Layout('layouts.dashboard-component')] #[Title('Subjects • Practizly')] class extends Component {
    public ?Subject $subject;

    public function mount(Subject $subject, $slug)
    {
        $this->subject = Subject::where('slug', $slug)->first();
    }

    #[Computed]
    public function topics()
    {
        return $this->subject->topics()->paginate(12);
    }

    #[On('topicCreated')]
    #[On('topicUpdated')]
    #[On('topicDeleted')]
    public function updatedTopics()
    {
        $this->dispatch('$refresh');
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div class="space-y-3">
            <flux:heading level="1" size="xl" class="text-{{ $subject->color }}">
                {{ Str::of($subject->name)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">
                    {{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>Topics</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <div class="flex items-center justify-start gap-2">
            <flux:modal.trigger name="original-create-topic">
                <flux:badge as="button" variant="pill" color="zinc" icon="plus" size="lg">New topic
                </flux:badge>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Tabs -->
    <livewire:subjects.components.nav-bar :subject="$subject" />

    <flux:table :paginate="$this->topics">
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->topics as $topic)
                <flux:table.row>
                    <flux:table.cell>{{ $topic->name }}</flux:table.cell>

                    <!-- Actions -->
                    <flux:table.cell>
                        <div class="flex justify-end items-end space-x-2">
                            <flux:modal.trigger name="edit-topic-{{ $topic->id }}">
                                <flux:button variant="ghost" size="sm" icon="pencil-square" inset="top bottom">
                                </flux:button>
                            </flux:modal.trigger>

                            <flux:modal.trigger name="delete-topic-{{ $topic->id }}">
                                <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom">
                                </flux:button>
                            </flux:modal.trigger>
                        </div>

                        <!-- Edit topic modal -->
                        <livewire:topics.edit :$topic wire:key="edit-topic-{{ $topic->id }}" />

                        <!-- Delete topic modal -->
                        <livewire:topics.delete :$topic wire:key="delete-topic-{{ $topic->id }}" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row class="text-center">
                    <flux:table.cell colspan="1">You don't have any topics yet.</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <!-- Actions -->
    <livewire:topics.create />
</div>

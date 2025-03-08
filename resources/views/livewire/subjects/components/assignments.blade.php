<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\Subject;

new #[Layout('layouts.dashboard-component')] #[Title('Subjects • Practizly')] class extends Component {
    public string $slug;

    public function mount($slug)
    {
        $this->slug = Str::slug($slug);
    }

    public function with()
    {
        return [
            'subject' => Subject::where('slug', $this->slug)->first(),
        ];
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="space-y-3">
            <flux:heading level="1" size="xl" class="text-{{ $subject->color }}">
                {{ Str::of($subject->name)->ucfirst() }}
            </flux:heading>

            <flux:breadcrumbs>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/dashboard">Dashboard</flux:breadcrumbs.item>
                <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username }}/subjects">Subjects
                </flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        {{-- <flux:button icon="star" variant="{{ $subject->is_favorite ? 'primary' : 'ghost' }}"
            wire:click="toggleFavorite">
            {{ $subject->is_favorite ? 'Favorito' : 'Marcar como favorito' }}
        </flux:button> --}}
    </div>

    <!-- Tabs -->
    <flux:navbar class="my-0">
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}" name="overview">Overview</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/exams" name="exams">Exams</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/assignments" name="assignments">Assignments</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/topics" name="topics">Topics</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/events" name="events">Events</flux:navbar.item>
    </flux:navbar>

    <flux:separator />

    <flux:subheading>{{ $subject->name }} assignments</flux:subheading>
</div>

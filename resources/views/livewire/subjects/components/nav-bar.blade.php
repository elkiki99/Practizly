<?php

use Livewire\Volt\Component;
use App\Models\Subject;

new class extends Component {
    public ?Subject $subject;

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
    }
}; ?>

<div class="lg:sticky lg:top-[0rem] z-50 bg-white dark:bg-zinc-950">
    <!-- Desktop -->
    <flux:navbar class="my-0 max-md:hidden">
        <flux:navbar.item icon="book-open" wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">Overview</flux:navbar.item>
        <flux:navbar.item icon="academic-cap" wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/exams">Exams</flux:navbar.item>
        <flux:navbar.item icon="document-text" wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/assignments">Assignments</flux:navbar.item>
        <flux:navbar.item icon="tag" wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/topics">Topics</flux:navbar.item>
        <flux:navbar.item icon="calendar" wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/events">Events</flux:navbar.item>
        <flux:navbar.item icon="light-bulb" wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/summaries">Summaries</flux:navbar.item>
    </flux:navbar>

    <!-- Mobile -->
    <flux:navbar class="my-0 md:hidden flex justify-between">
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}">Overview</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/exams">Exams</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/assignments">Assignments</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/topics">Topics</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/events">Events</flux:navbar.item>
        <flux:navbar.item wire:navigate href="/{{ Auth::user()->username }}/subjects/{{ $subject->slug }}/summaries">Summaries</flux:navbar.item>
    </flux:navbar>

    <flux:separator />
</div>
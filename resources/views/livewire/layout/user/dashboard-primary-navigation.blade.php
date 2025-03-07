<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>
<flux:navbar
    class="z-50 px-2 max-lg:items-center max-lg:justify-center max-lg:flex border-b lg:dark:border-none dark:border-zinc-900 border-zinc-200 lg:bg-zinc-100 dark:lg:bg-zinc-900">
    <!-- Desktop -->
    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="book-open" href="/{{ Auth::user()->username }}/subjects"
        :current="request()->is(Auth::user()->username.
            '/subjects') || request()->is(Auth::user()->username.
            '/subjects/*')">
        Subjects
    </flux:navbar.item>

    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="academic-cap" href="/{{ Auth::user()->username }}/exams"
        :current="request()->is(Auth::user()->username.
            '/exams') || request()->is(Auth::user()->username.
            '/exams/*')">
        Exams
    </flux:navbar.item>

    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="document-text"
        href="/{{ Auth::user()->username }}/assignments"
        :current="request()->is(Auth::user()->username.
            '/assignments') || request()->is(Auth::user()->username.
            '/assignments/*')">
        Assignments
    </flux:navbar.item>

    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="calendar" href="/{{ Auth::user()->username }}/calendar"
        :current="request()->is(Auth::user()->username.
            '/calendar') || request()->is(Auth::user()->username.
            '/calendar/*')">
        Calendar
    </flux:navbar.item>

    <!-- Mobile -->
    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/{{ Auth::user()->username }}/subjects"
        :current="request()->is(Auth::user()->username.
            '/subjects') || request()->is(Auth::user()->username.
            '/subjects/*')">
        Subjects
    </flux:navbar.item>

    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/{{ Auth::user()->username }}/exams"
        :current="request()->is(Auth::user()->username.
            '/exams') || request()->is(Auth::user()->username.
            '/exams/*')">
        Exams
    </flux:navbar.item>

    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/{{ Auth::user()->username }}/assignments"
        :current="request()->is(Auth::user()->username.
            '/assignments') || request()->is(Auth::user()->username.
            '/assignments/*')">
        Assignments
    </flux:navbar.item>

    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/{{ Auth::user()->username }}/calendar"
        :current="request()->is(Auth::user()->username.
            '/calendar') || request()->is(Auth::user()->username.
            '/calendar/*')">
        Calendar
    </flux:navbar.item>
</flux:navbar>

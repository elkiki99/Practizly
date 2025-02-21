<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<flux:navbar x-data="{ atTop: true }" @scroll.window="atTop = window.pageYOffset <= 25"
    x-bind:class="{
        'bg-transparent border-b dark:border-zinc-700 border-zinc-200 lg:border-none': atTop,
        'border-b lg:dark:border-none dark:border-zinc-700 border-zinc-200 lg:bg-zinc-100 dark:lg:bg-zinc-800': !atTop,
    }"
    class="z-50 px-2  transition-[background-color] duration-300 max-lg:items-center max-lg:justify-center max-lg:flex">
    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="book-open" href="/subjects">Subjects</flux:navbar.item>
    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="academic-cap" href="/exams">Exams</flux:navbar.item>
    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="document-text" href="/assignments">Assignments
    </flux:navbar.item>
    <flux:navbar.item wire:navigate class="hidden sm:flex" icon="calendar" href="/calendar">Calendar
    </flux:navbar.item>

    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/subjects">Subjects</flux:navbar.item>
    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/exams">Exams</flux:navbar.item>
    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/assignments">Assignments</flux:navbar.item>
    <flux:navbar.item wire:navigate class="flex sm:hidden" href="/calendar">Calendar</flux:navbar.item>
</flux:navbar>

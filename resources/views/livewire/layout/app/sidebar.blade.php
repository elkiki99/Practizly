<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div >
    <flux:sidebar x-init="screenLg = window.innerWidth >= 1024" x-data="{ screenLg: true }" stashable sticky
        class="border-r md:hidden bg-zinc-100 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="md:hidden" icon="x-mark" />

        <flux:brand wire:navigate href="/" logo="{{ asset('practizly-logo-black.svg') }}" name="{{ config('app.name' )}}" class="px-2 dark:hidden" />
        <flux:brand wire:navigate href="/" logo="{{ asset('practizly-logo-white.svg') }}" name="{{ config('app.name' )}}"
            class="hidden px-2 dark:flex" />

        <flux:navlist variant="outline">
            <flux:navlist.item wire:navigate icon="user-group" href="/clients">Clients</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="banknotes" href="/pricing">Pricing</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="phone" href="/contact">Contact</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="clipboard-document" href="/docs">Docs</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="newspaper" href="/blog">Blog</flux:navlist.item>
        </flux:navlist>

        <flux:spacer />

        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
            aria-label="Toggle dark mode" />
    </flux:sidebar>
</div>

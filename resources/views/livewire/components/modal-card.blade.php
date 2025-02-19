<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $icon;
    public string $title;
    public string $subtitle;
    public string $modalEvent = '';
}; ?>

<flux:modal.trigger name="{{ $modalEvent }}">
    <flux:card class="flex gap-4 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:cursor-pointer">
        <flux:icon :icon="$icon" variant="mini" />
        <div class="flex flex-col items-start">
            <flux:heading level="2">{{ $title }}</flux:heading>
            <flux:subheading>{{ $subtitle }}</flux:subheading>
        </div>
    </flux:card>
</flux:modal.trigger>

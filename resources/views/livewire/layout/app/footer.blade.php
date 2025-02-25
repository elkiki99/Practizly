<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<flux:footer container>
    <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
        <div class="flex flex-col items-center gap-4 sm:flex-row max-sm:gap-0">
            <flux:subheading class="text-center sm:text-left">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved
            </flux:subheading>
        </div>

        <flux:subheading class="text-center sm:text-right">
            <div class="flex gap-4">
                <flux:link wire:navigate href="/terms" class="text-center max-sm:mb-1 !text-sm sm:text-left">Terms
                    of
                    service
                </flux:link>
                <flux:link wire:navigate href="/privacy" class="text-center max-sm:mb-1 !text-sm sm:text-left">
                    Privacy
                    policy
                </flux:link>
            </div>
        </flux:subheading>
    </div>
</flux:footer>

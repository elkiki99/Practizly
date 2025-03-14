<?php

use Livewire\Volt\Component;

new class extends Component {
    /**
     * Real time update on profile when profile information update form is submitted
     */
    protected $listeners = ['profileUpdated' => '$refresh'];

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:header
    class="flex items-center dark:bg-zinc-900 border-b bg-zinc-100 border-zinc-200 dark:border-zinc-800 lg:hidden">
    
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:dropdown position="top" align="start">
        <flux:profile avatar="{{ Auth::user()->profile_picture ?? asset('me.webp') }}" />

        <flux:menu>
            <div class="px-2 py-1.5">
                <flux:subheading class="!text-xs">
                    Signed in as
                </flux:subheading>

                <flux:heading>
                    {{ Auth::user()->email }}
                </flux:heading>
            </div>

            <flux:menu.separator />

            <flux:menu.item icon="home" wire:navigate href="/">Home</flux:menu.item>
            <flux:menu.item icon="user" wire:navigate href="/{{ Auth::user()->username }}/profile">Profile
            </flux:menu.item>

            <flux:menu.separator />

            <flux:menu.item wire:click='logout' icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:header>

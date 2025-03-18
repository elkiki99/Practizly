<?php

use Livewire\Volt\Component;
use App\Livewire\Actions\Logout;
use Livewire\Attributes\On;
use App\Models\Subject;

new class extends Component {
    public $subjects;

    #[On('subjectCreated')]
    public function mount()
    {
        $this->subjects = Subject::where('user_id', auth()->user()->id)->get();
    }

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

<div>
    <flux:sidebar sticky stashable
        class="h-screen border-r bg-zinc-100 dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:brand href="/" logo="{{ asset('practizly-logo-black.svg') }}" name="{{ config('app.name') }}"
            class="px-2 dark:hidden" />
        <flux:brand href="/" logo="{{ asset('practizly-logo-white.svg') }}" name="{{ config('app.name') }}"
            class="hidden px-2 dark:flex" />

        <flux:navlist variant="outline">
            <flux:navlist.item wire:navigate icon="squares-2x2" href="/{{ Auth::user()->username}}/dashboard">Dashboard</flux:navlist.item>

            <flux:navlist.group expandable heading="Subjects">
                @forelse($subjects as $subject)
                    <flux:navlist.item wire:navigate href="/{{ Auth::user()->username}}/subjects/{{ $subject->slug }}">{{ $subject->name }}</flux:navlist.item>
                @empty
                    <flux:navlist.item>No subjects yet</flux:navlist.item>
                @endforelse
            </flux:navlist.group>

            <flux:navlist.item wire:navigate icon="paper-clip" href="/{{ Auth::user()->username}}/library">Library</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="light-bulb" href="/{{ Auth::user()->username}}/summaries">Summaries</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="user" href="/{{ Auth::user()->username}}/profile">Profile</flux:navlist.item>
            <flux:navlist.item wire:navigate icon="cog-6-tooth" href="/{{ Auth::user()->username}}/settings">Settings</flux:navlist.item>
        </flux:navlist>

        <flux:spacer />

        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle"
            aria-label="Toggle dark mode" />

        <!-- User dropdown -->
        <flux:dropdown position="top" align="end" class="hidden lg:flex">
            <flux:profile name="{{ Auth::user()->name }}"
                avatar="{{ Auth::user()->profile_picture ?? asset('me.webp') }}" />

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

                <flux:menu.item wire:navigate icon="home" href="/">Home</flux:menu.item>
                <flux:menu.item wire:navigate icon="user" href="/{{ Auth::user()->username}}/profile">Profile</flux:menu.item>

                <flux:menu.separator />

                <flux:menu.item wire:click='logout' icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>
</div>

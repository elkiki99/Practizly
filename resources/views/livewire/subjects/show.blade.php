<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\Subject;

new #[Layout('layouts.dashboard')] #[Title('Subjects • Practizly')] class extends Component {
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
    <div class="space-y-3">
        <flux:heading level="1" size="xl">{{ Str::of($subject->name)->ucfirst() }}</flux:heading>

        <flux:breadcrumbs>
            <flux:breadcrumbs.item wire:navigate href="/{{ Auth::user()->username}}/subjects">Subjects</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ Str::of($subject->name)->ucfirst() }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <flux:separator variant="subtle" />
</div>

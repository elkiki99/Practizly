<x-panel-layout title="Panel • {{ config('app.name', 'Practizly') }}">
    <div class="self-stretch flex-1 space-y-6 max-lg:max-w-2xl max-lg:mx-auto">
        <div>
            <flux:heading level="1" size="lg">Welcome back {{ Auth::user()->name }}</flux:heading>
            <flux:subheading>Here's what's new today.</flux:subheading>
        </div>

        <flux:separator variant="subtle" />
    </div>
</x-panel-layout>

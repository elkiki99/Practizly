<x-dashboard-layout title="Subjects • {{ config('app.name', 'Practizly') }}">
    <div class="self-stretch flex-1 space-y-6 max-lg:max-w-2xl max-lg:mx-auto">
        <div>
            <flux:heading level="1" size="xl">Subjects</flux:heading>
            {{-- <flux:subheading>Manage your subjects.</flux:subheading> --}}
        </div>

        <flux:separator variant="subtle" />

        <livewire:subjects.index />
    </div>
</x-dashboard-layout>

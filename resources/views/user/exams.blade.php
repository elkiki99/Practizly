<x-dashboard-layout title="Exam builder • {{ config('app.name', 'Practizly') }}">
    <div class="self-stretch flex-1 space-y-6 max-lg:max-w-2xl max-lg:mx-auto">
        <div>
            <flux:heading level="1" size="lg">Exam builder</flux:heading>
            <flux:subheading>Simulate your exam.</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <livewire:exams.index />
    </div>
</x-dashboard-layout>

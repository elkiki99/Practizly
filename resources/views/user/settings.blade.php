<x-dashboard-layout title="Settings • {{ config('app.name', 'Practizly') }}">
    <div class="self-stretch flex-1 space-y-6 max-lg:max-w-2xl max-lg:mx-auto">
        <div>
            <flux:heading level="1" size="lg">Settings</flux:heading>
            <flux:subheading>Manage your account.</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <div class="max-w-xl space-y-6">
            <div>
                <flux:heading>Billing information</flux:heading>
                <flux:subheading>Manage your billing preferences and packages.</flux:subheading>
            </div>

            <flux:button variant="filled" icon-trailing="chevron-right" as="link" href="#">My plan</flux:button>

            <flux:separator variant="subtle" />
        </div>
        
        <div class="max-w-xl space-y-6">
            <div>
                <flux:heading>Delete account</flux:heading>
                <flux:subheading>Delete your account and all of its resources.</flux:subheading>
            </div>

            <livewire:profile.delete-user-form />
        </div>
    </div>
</x-dashboard-layout>

<x-dashboard-layout title="Profile • {{ config('app.name', 'Practizly') }}">
    <div class="self-stretch flex-1 space-y-6 max-lg:max-w-2xl max-lg:mx-auto">
        <div>
            <flux:heading level="1" size="lg">Profile</flux:heading>
            <flux:subheading>Your profile settings.</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <div class="max-w-xl space-y-6">
            <div class="space-y-6">
                <div>
                    <flux:heading>Profile information</flux:heading>
                    <flux:subheading>Update your accounts profile info and email.</flux:subheading>
                </div>

                <livewire:profile.update-profile-information-form />
            </div>

            <flux:separator variant="subtle" />

            <div class="space-y-6">
                <div>
                    <flux:heading>Update password</flux:heading>
                    <flux:subheading>Ensure your account is using a long, random password to stay secure.
                    </flux:subheading>
                </div>

                <livewire:profile.update-password-form />
            </div>
        </div>
    </div>
</x-dashboard-layout>

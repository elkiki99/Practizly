<x-app-layout title="Contact • {{ config('app.name', 'Practizly') }}">
    <flux:main container>
        <section class="space-y-6">
            <div>
                <flux:heading size="xl">Contact us</flux:heading>
                <flux:subheading>Let's get in touch</flux:subheading>
            </div>

            <div class="max-w-xl space-y-6">
                <livewire:contact.contact-form />
            </div>
        </section>
    </flux:main>
</x-app-layout>

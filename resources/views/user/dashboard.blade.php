<x-dashboard-layout title="Dashboard • {{ config('app.name', 'Practizly') }}">
    <div class="self-stretch flex-1 space-y-6">
        <div>
            <flux:heading level="1" size="xl">Welcome back {{ Auth::user()->name }}!</flux:heading>
            {{-- <flux:subheading>Let's study shall we?</flux:subheading> --}}
        </div>

        <flux:separator variant="subtle" />

        <div class="space-y-12">
            <!-- Actions (overview) -->
            <div class="space-y-6">
                <div class="">
                    <flux:heading level="2">Overview</flux:heading>
                    <flux:subheading>Let's get you organized!</flux:subheading>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- New subject -->
                    <livewire:components.modal-card :icon="'book-open'" :title="'New subject'" :subtitle="'Create and manage study subjects.'"
                        :modal-event="'create-subject'" />

                    <!-- New exam -->
                    <livewire:components.modal-card :icon="'academic-cap'" :title="'New exam prep'" :subtitle="'Generate AI-powered practice tests.'"
                        :modal-event="'create-exam'" />

                    <!-- New event -->
                    <livewire:components.modal-card :icon="'paper-clip'" :title="'New attachment'" :subtitle="'Attach files to your library.'"
                        :modal-event="'create-attachment'" />

                    <!-- New assignment -->
                    <livewire:components.modal-card :icon="'document-text'" :title="'New assignment'" :subtitle="'Create and track your tasks.'"
                        :modal-event="'create-assignment'" />

                    <!-- Summaries -->
                    <livewire:components.modal-card :icon="'light-bulb'" :title="'New summary'" :subtitle="'Save and organize study notes.'"
                        :modal-event="'create-summary'" />

                    <!-- New quizz -->
                    <livewire:components.modal-card :icon="'check-circle'" :title="'New quizz'" :subtitle="'Test your knowledge with quizzes.'"
                        :modal-event="'create-quizz'" />
                </div>
            </div>

            <!-- Modal actions -->
            <div>
                <livewire:subjects.create />
                <livewire:exams.create />
                <livewire:assignments.create />
                <livewire:attachments.create />
                <livewire:summaries.create />
                <livewire:quizzes.create />
            </div>

            <!-- Next events (exams, assignments, etc.) -->
            <div class="space-y-6">
                <div class="">
                    <div class="flex items-center gap-2 mb-2">
                        <flux:heading level="2">Next events</flux:heading>
                        <flux:button as="link" href="/calendar" wire:navigate icon-trailing="chevron-right"
                            size="xs" variant="ghost" />
                    </div>

                    <flux:subheading>Check out your upcoming events.</flux:subheading>
                </div>

                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable>Event</flux:table.column>
                        <flux:table.column sortable>Date</flux:table.column>
                        <flux:table.column sortable>Status</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        <!-- Example 1 -->
                        <flux:table.row>
                            <flux:table.cell class="flex items-center gap-3 whitespace-nowrap">
                                <flux:icon.book-open />Exam
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap">Jul 31, 11:15</flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>

                        <!-- Example 2 -->
                        <flux:table.row>
                            <flux:table.cell class="flex items-center gap-3 whitespace-nowrap">
                                <flux:icon.document-text />Assignment
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap">Aug 22, 12:25</flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="green" inset="top bottom">Submitted</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>

                        <!-- Example 3 -->
                        <flux:table.row>
                            <flux:table.cell class="flex items-center gap-3 whitespace-nowrap">
                                <flux:icon.check-circle />Quizz
                            </flux:table.cell>

                            <flux:table.cell class="whitespace-nowrap">September 25, 22:00</flux:table.cell>

                            <flux:table.cell>
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Uploaded files -->
            <div class="space-y-6">
                <div class="">
                    <div class="flex items-center gap-2 mb-2">
                        <flux:heading level="2">Last uploaded files</flux:heading>
                        <flux:button as="link" href="/library" wire:navigate icon-trailing="chevron-right"
                            size="xs" variant="ghost" />
                    </div>

                    <flux:subheading>Check out latest file uploads.</flux:subheading>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

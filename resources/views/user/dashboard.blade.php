<x-dashboard-layout title="Dashboard • {{ config('app.name', 'Practizly') }}">
    <div class="self-stretch flex-1 space-y-6 max-lg:max-w-2xl max-lg:mx-auto">
        <div>
            <flux:heading level="1" size="lg">Welcome back {{ Auth::user()->name }}!</flux:heading>
            <flux:subheading>Let's study shall we?</flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        {{-- <livewire:open-ai-test /> --}}

        <div class="space-y-12">
            <!-- Actions (overview) -->
            <div class="space-y-6">
                <div class="">
                    <flux:heading level="2" size="lg">Overview</flux:heading>
                    <flux:subheading>Let's get you organized!</flux:subheading>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <flux:card class="hover:bg-transparent hover:cursor-pointer">
                        <div class="flex gap-4">
                            <flux:icon.book-open variant="mini" />
                            <div>
                                <flux:heading level="2">New subjet</flux:heading>
                                <flux:subheading>Create a new subject.</flux:subheading>
                            </div>
                        </div>
                    </flux:card>
                    <flux:card class="hover:bg-transparent hover:cursor-pointer">
                        <div class="flex gap-4">
                            <flux:icon.academic-cap variant="mini" />
                            <div>
                                <flux:heading level="2">New exam prep</flux:heading>
                                <flux:subheading>Create a new exam preparation.</flux:subheading>
                            </div>
                        </div>
                    </flux:card>
                    <flux:card class="hover:bg-transparent hover:cursor-pointer">
                        <div class="flex gap-4">
                            <flux:icon.calendar variant="mini" />
                            <div>
                                <flux:heading level="2">New calendar event</flux:heading>
                                <flux:subheading>Organize your schedule.</flux:subheading>
                            </div>
                        </div>
                    </flux:card>
                    <flux:card class="hover:bg-transparent hover:cursor-pointer">
                        <div class="flex gap-4">
                            <flux:icon.document-text variant="mini" />
                            <div>
                                <flux:heading level="2">New assignments</flux:heading>
                                <flux:subheading>Add assignments and complete them.</flux:subheading>
                            </div>
                        </div>
                    </flux:card>
                    <flux:card class="hover:bg-transparent hover:cursor-pointer">
                        <div class="flex gap-4">
                            <flux:icon.light-bulb variant="mini" />
                            <div>
                                <flux:heading level="2">My summaries</flux:heading>
                                <flux:subheading>Organize your files with summaries.</flux:subheading>
                            </div>
                        </div>
                    </flux:card>
                    <flux:card class="hover:bg-transparent hover:cursor-pointer">
                        <div class="flex gap-4">
                            <flux:icon.check-circle variant="mini" />
                            <div>
                                <flux:heading level="2">My quizzes</flux:heading>
                                <flux:subheading>Study with quizzes.</flux:subheading>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>

            <!-- Next events (exams, assignments, etc.) -->
            <div class="space-y-6">
                <div class="">
                    <flux:heading level="2" size="lg">Next events</flux:heading>
                    <flux:subheading>Check out your upcoming events.</flux:subheading>
                </div>

                <flux:table>
                    <flux:columns>
                        <flux:column sortable>Event</flux:column>
                        <flux:column sortable>Date</flux:column>
                        <flux:column sortable>Status</flux:column>
                    </flux:columns>

                    <flux:rows>
                        <!-- Example 1 -->
                        <flux:row>
                            <flux:cell class="flex items-center gap-3 whitespace-nowrap">
                                <flux:icon.book-open />Exam
                            </flux:cell>

                            <flux:cell class="whitespace-nowrap">Jul 31, 11:15</flux:cell>

                            <flux:cell>
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            </flux:cell>

                            <flux:cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                            </flux:cell>
                        </flux:row>

                        <!-- Example 2 -->
                        <flux:row>
                            <flux:cell class="flex items-center gap-3 whitespace-nowrap">
                                <flux:icon.document-text />Assignment
                            </flux:cell>

                            <flux:cell class="whitespace-nowrap">Aug 22, 12:25</flux:cell>

                            <flux:cell>
                                <flux:badge size="sm" color="green" inset="top bottom">Submitted</flux:badge>
                            </flux:cell>

                            <flux:cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                            </flux:cell>
                        </flux:row>

                        <!-- Example 3 -->
                        <flux:row>
                            <flux:cell class="flex items-center gap-3 whitespace-nowrap">
                                <flux:icon.check-circle />Quizz
                            </flux:cell>

                            <flux:cell class="whitespace-nowrap">September 25, 22:00</flux:cell>

                            <flux:cell>
                                <flux:badge size="sm" color="yellow" inset="top bottom">Pending</flux:badge>
                            </flux:cell>

                            <flux:cell>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                            </flux:cell>
                        </flux:row>
                    </flux:rows>
                </flux:table>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use App\Models\Subject;
use App\Models\Topic;

new class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:1000')]
    public string $description = '';

    #[Validate('required|string|max:50')]
    public string $color = '';

    #[Validate('boolean')]
    public bool $is_favorite = false;

    public function createSubject()
    {
        $this->validate();

        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        while (Subject::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $subject = Subject::create([
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description,
            'color' => $this->color,
            'is_favorite' => $this->is_favorite,
            'user_id' => auth()->user()->id,
        ]);

        $this->reset();

        $this->dispatch('subjectCreated');

        Flux::toast(heading: 'Subject created', text: 'Your subject was created successfully', variant: 'success');

        $this->modal('create-subject')->close();
    }
}; ?>

<form wire:submit.prevent="createSubject">
    <flux:modal variant="flyout" name="create-subject" class="space-y-6 md:w-96">
        <div>
            <flux:heading size="lg">New subject</flux:heading>
            <flux:subheading>Create a new subject.</flux:subheading>
        </div>

        <!-- Name -->
        <flux:input label="Subject name" placeholder="Physics" wire:model='name' autofocus required autocomplete="name" />

        <!-- Description -->
        <flux:input label="Subject description" placeholder="Physics - 1st semester 2025" wire:model='description'
            autofocus required />

        <!-- Colors -->
        <flux:select required label="Subject color" wire:model="color" variant="listbox" placeholder="Select color">
            <flux:select.option value="green"><span
                    class="inline-block mr-2 bg-green-500 rounded-full size-2"></span>Green</flux:select.option>
            <flux:select.option value="red"><span class="inline-block mr-2 bg-red-500 rounded-full size-2"></span>Red
            </flux:select.option>
            <flux:select.option value="blue"><span
                    class="inline-block mr-2 bg-blue-500 rounded-full size-2"></span>Blue</flux:select.option>
            <flux:select.option value="yellow"><span
                    class="inline-block mr-2 bg-yellow-500 rounded-full size-2"></span>Yellow</flux:select.option>
            <flux:select.option value="orange"><span
                    class="inline-block mr-2 bg-orange-500 rounded-full size-2"></span>Orange</flux:select.option>
            <flux:select.option value="purple"><span
                    class="inline-block mr-2 bg-purple-500 rounded-full size-2"></span>Purple</flux:select.option>
            <flux:select.option value="black"><span class="inline-block mr-2 bg-black rounded-full size-2"></span>Black
            </flux:select.option>
            <flux:select.option value="gray"><span
                    class="inline-block mr-2 bg-gray-500 rounded-full size-2"></span>Gray</flux:select.option>
        </flux:select>

        <!-- Favourites -->
        <flux:switch required wire:model.live="is_favorite" label="Mark subject as favorite" />

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Create subject</flux:button>
        </div>
    </flux:modal>
</form>

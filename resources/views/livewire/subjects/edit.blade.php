<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use App\Models\Subject;

new class extends Component {
    public ?Subject $subject;

    public string $name = '';
    public string $description = '';
    public string $color = '';
    public string $slug = '';
    public bool $status = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'color' => 'required|string|max:50',
            'slug' => ['required', Rule::unique('subjects')->ignore($this->subject)],
            'status' => 'required|boolean',
        ];
    }

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
        $this->name = $this->subject->name;
        $this->description = $this->subject->description;
        $this->color = $this->subject->color;
        $this->status = $this->subject->status;
        $this->slug = $this->subject->slug;
    }

    public function editSubject()
    {
        $this->validate();

        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        if ($this->slug !== $baseSlug) {
            while (Subject::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $this->subject->update([
            'name' => $this->name,
            'slug' => $slug,
            'description' => $this->description,
            'color' => $this->color,
            'status' => $this->status,
            'user_id' => auth()->user()->id,
        ]);

        Flux::toast(heading: 'Subject updated', text: 'Your subject was updated successfully', variant: 'success');

        $url = request()->header('Referer');

        if ($url === url()->route('subjects.index', [Auth::user()->username])) {
            $this->dispatch('subjectUpdated');
            Flux::modals()->close();
        } else {
            // Check slug to redirect to new url
            if ($this->slug !== $slug) {
                Flux::modals()->close();
                $this->redirectRoute('subjects.show', ['slug' => $slug, 'user' => Auth::user()->username], navigate: true);
            } else {
                $this->dispatch('subjectUpdated');
                Flux::modals()->close();
            }
        }
    }
}; ?>

<form wire:submit.prevent="editSubject">
    <flux:modal variant="flyout" name="edit-subject-{{ $subject->id }}" class="space-y-6 md:w-96">
        <div>
            <flux:heading size="lg">Edit subject</flux:heading>
            <flux:subheading>Edit {{ Str::of($subject->name)->lower() }} subject.</flux:subheading>
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

        <!-- Status -->
        <flux:switch required wire:model.live="status" label="Subject status" description="Ongoing is marked by default" />

        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Update subject</flux:button>
        </div>
    </flux:modal>
</form>

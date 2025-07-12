<div class="flex flex-row justify-center fb-form-component filament-form-builder">
    <div class="max-w-[600px] min-w-[400px] fb-form-container">
        <h1 class="mb-2 text-xl font-bold">
            {{ $this->filamentForm->name }}
        </h1>
        <p class="mb-4">
            {{ $this->filamentForm->description }}
        </p>
        <form wire:submit="create">
            @csrf
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-6">
                Submit
            </x-filament::button>
        </form>

        <x-filament-actions::modals />
    </div>
</div>

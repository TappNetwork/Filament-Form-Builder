<div class="flex flex-row justify-center p-4 sm:p-8 md:p-16 fb-form-component filament-form-builder">
    <div class="max-w-[600px] min-w-[320px] sm:min-w-[400px] rounded-xl border-2 p-4 fb-form-container">
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

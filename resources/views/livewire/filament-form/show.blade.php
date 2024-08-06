<div class="p-16 flex flex-row justify-center fb-form-component filament-form-builder">
    <div class="max-w-[600px] min-w-[400px] rounded-xl border-2 p-4 fb-form-container">
        <h1 class="font-bold text-xl mb-2">
            {{ $this->filamentForm->name }}
        </h1>
        <p class="mb-4">
            {{ $this->filamentForm->description }}
        </p>
        <form wire:submit="create">
            @csrf
            {{ $this->form }}

            <button
                type="submit"
                class="filament-form-builder fb-form-submit-button bg-emerald-400 hover:bg-emerald-600 hover:scale-[1.03] shadow-lg px-2 py-1 mt-4 rounded-md text-white"
            >
                Submit
            </button>
        </form>

        <x-filament-actions::modals />
    </div>
</div>

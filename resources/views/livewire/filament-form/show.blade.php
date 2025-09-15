<div class="flex flex-row justify-center p-16 fb-form-component filament-form-builder">
    <div class="max-w-[600px] min-w-[400px] rounded-xl border-2 p-4 fb-form-container">
<<<<<<< Updated upstream
        <h1 class="mb-2 text-xl font-bold">
            {{ $this->filamentForm->name }}
        </h1>
        <p class="mb-4">
            {{ $this->filamentForm->description }}
        </p>
        <form wire:submit="create">
            @csrf
            {{ $this->form }}
=======
        @if($showGuestEntry)
            <livewire:tapp.filament-form-builder.livewire.filament-form-user.show :entry="$guestEntry" />
        @else
            <h1 class="mb-2 text-xl font-bold">
                {{ $this->filamentForm->name }}
            </h1>
            <p class="mb-4">
                {{ $this->filamentForm->description }}
            </p>
            <form wire:submit="create">
                @csrf
                {{ $this->form }}
>>>>>>> Stashed changes

            <x-filament::button type="submit" class="mt-6">
                Submit
            </x-filament::button>
        </form>

        <x-filament-actions::modals />
    </div>
</div>

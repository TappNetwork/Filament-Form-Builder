<div class="filament-form-builder-entry-layout">
    <div class="container mx-auto py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">Form Submission</h1>

            <div class="entry-content">
                @livewire('tapp.filament-form-builder.livewire.filament-form-user.show', ['entry' => $entry], key('entry-'.$entry->id))
            </div>
        </div>
    </div>
</div>

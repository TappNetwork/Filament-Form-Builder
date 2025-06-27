<div class="filament-form-builder-layout">
    <div class="container mx-auto py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="form-content">
                @livewire('tapp.filament-form-builder.livewire.filament-form.show', ['form' => $form], key('form-'.$form->uuid))
            </div>
        </div>
    </div>
</div>

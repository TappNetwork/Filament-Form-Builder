<div class="filament-form-builder">
    <div @class([
        'fb-form-user-container w-full',
        'max-w-2xl mx-auto px-4 py-8 sm:px-8 sm:py-16' => ! $compact,
        'px-0 py-0' => $compact,
    ])>
        {{ $this->entryInfoList }}
    </div>
</div>

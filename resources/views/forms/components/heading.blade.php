@php
    $heading = $getLabel();
    $subheading = $getHint();
@endphp

<div class="space-y-2">
    @if ($heading)
        <h2 class="text-2xl font-bold tracking-tight">
            {{ $heading }}
        </h2>
    @endif

    @if ($subheading)
        <p class="text-gray-500">
            {{ $subheading }}
        </p>
    @endif
</div>

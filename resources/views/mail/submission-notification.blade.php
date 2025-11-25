<x-mail::message>
# New Form Submission

A new submission has been received for **{{ $form->name }}**.

**Submitted by:** {{ $entry->user?->name ?? 'Guest' }}  
**Submitted at:** {{ $entry->created_at->format('F j, Y g:i A') }}

<x-mail::button :url="route(config('filament-form-builder.filament-form-user-show-route'), $entry)">
View Submission
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>





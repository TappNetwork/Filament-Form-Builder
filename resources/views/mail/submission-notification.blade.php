<x-mail::message>
# New Form Submission

A new submission has been received for **{{ $form->name }}**.

**Submitted by:** {{ $entry->user?->name ?? 'Guest' }}

**Submitted at:** {{ $entry->created_at->format('F j, Y g:i A') }}

---

## Submission Details

@foreach($entry->entry as $field)
**{{ $field['field'] }}**

@if(is_array($field['answer']))
{{ implode(', ', $field['answer']) }}
@else
{{ $field['answer'] ?: '(No answer provided)' }}
@endif

@endforeach

---

<x-mail::button :url="route('filament.admin.resources.filament-forms.edit', $form)">
View Form & Entries
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>





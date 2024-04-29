<?php

namespace Tapp\FilamentForms\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Tapp\FilamentForms\Models\FilamentForm;

class FilamentFormUsersExport implements FromCollection, WithHeadings, WithMapping
{
    public Collection $entries;

    public FilamentForm $form;

    public function __construct(Collection $entries)
    {
        $this->entries = $entries->load('user');

        $this->form = FilamentForm::where('id', $entries->first()->filament_form_id)
            ->with('filamentFormFields')
            ->firstOrFail();
    }

    public function collection()
    {
        return $this->entries;
    }

    public function map($entry): array
    {
        $mapping = [
            $entry->user->name,
            $entry->created_at,
            $entry->updated_at,
        ];

        foreach ($this->form->filamentFormFields as $field) {
            $entriesFieldKey = array_search($field->id, array_column($entry->entry, 'field_id'));

            if ($entriesFieldKey === false) {
                array_push($mapping, '');
            } else {
                array_push($mapping, $entry->entry[$entriesFieldKey]['answer']);
            }
        }

        return $mapping;
    }

    public function headings(): array
    {
        $headings = [
            'name',
            'created_at',
            'updated_at',
        ];

        foreach ($this->form->filamentFormFields as $field) {
            array_push($headings, $field->label);
        }

        return $headings;
    }
}

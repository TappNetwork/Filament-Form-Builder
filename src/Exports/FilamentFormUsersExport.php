<?php

namespace Tapp\FilamentFormBuilder\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Tapp\FilamentFormBuilder\Models\FilamentForm;

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

    public function map($row): array
    {
        /** @var \Tapp\FilamentFormBuilder\Models\FilamentFormUser $row */
        $data = [
            'id' => $row->id,
            'user' => $row->user->name ?? 'Guest',
            /** @phpstan-ignore-next-line */
            'created_at' => $row->created_at,
        ];

        foreach ($row->entry as $field) {
            /** @var array{field: string, answer: string} $field */
            $data[$field['field']] = $field['answer'];
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'id',
            'user',
            'created_at',
        ];

        foreach ($this->form->filamentFormFields as $field) {
            /** @phpstan-ignore-next-line */
            array_push($headings, $field->label);
        }

        return $headings;
    }
}

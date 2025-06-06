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

    public function map($entry): array
    {
        $mapping = [
            $entry->user->name ?? 'Guest',
            $entry->created_at,
            $entry->updated_at,
        ];

        // Get all headings after the first three default ones
        $fieldHeadings = array_slice($this->headings(), 3);

        // Create a lookup array of field labels to answers for this entry
        $entryAnswers = collect($entry->entry)->mapWithKeys(function ($fieldEntry) {
            return [$fieldEntry['field'] => $fieldEntry['answer']];
        });

        // For each field heading, add the corresponding answer or empty string
        foreach ($fieldHeadings as $heading) {
            $answer = $entryAnswers->get($heading);

            if (is_array($answer)) {
                array_push($mapping, json_encode($answer));
            } else {
                array_push($mapping, $answer ?? '');
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

        // Get all unique field labels from all entries
        $allFieldLabels = collect();
        foreach ($this->entries as $entry) {
            foreach ($entry->entry as $fieldEntry) {
                if (isset($fieldEntry['field'])) {
                    $allFieldLabels->push($fieldEntry['field']);
                }
            }
        }

        // Add unique field labels to headings
        $headings = array_merge($headings, $allFieldLabels->unique()->values()->toArray());

        return $headings;
    }
}

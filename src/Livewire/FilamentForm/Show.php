<?php

namespace Tapp\FilamentFormBuilder\Livewire\FilamentForm;

use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormField;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

class Show extends Component implements HasForms
{
    use InteractsWithForms;

    public FilamentForm $filamentForm;

    public ?array $data = [];

    public function mount(FilamentForm $form): void
    {
        $this->filamentForm = $form->load('filamentFormFields');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getSchema())
            ->statePath('data');
    }

    public function getSchema(): array
    {
        $schema = [];

        foreach ($this->filamentForm->filamentFormFields as $fieldData) {
            $filamentField = $fieldData->type->className()::make($fieldData->id);

            $filamentField = $this->parseField($filamentField, $fieldData->toArray());

            array_push($schema, $filamentField);
        }

        return $schema;
    }

    public function parseField(Field $filamentField, array $fieldData): Field
    {
        if (isset($fieldData['label'])) {
            $filamentField = $filamentField
                ->label($fieldData['label']);
        }

        if (isset($fieldData['required']) && $fieldData['required']) {
            $filamentField = $filamentField
                ->required();
        }

        if (isset($fieldData['options'])) {
            $filamentField = $filamentField
                ->options($fieldData['options']);
        }

        if (isset($fieldData['rules'])) {
            $filamentField = $filamentField
                ->rules($fieldData['rules']);
        }

        return $filamentField;
    }

    public function create()
    {
        $this->form->getState();

        $entry = [];

        foreach ($this->form->getState() as $key => $value) {
            $field = $this->filamentForm
                ->filamentFormFields
                ->find($key);

            $valueData = $this->parseValue($field, $value);

            array_push($entry, [
                'field' => $field->label,
                'field_id' => $field->id,
                'answer' => $valueData,
                'type' => $field->type->fieldName(),
            ]);
        }

        $entryModel = FilamentFormUser::updateOrCreate(
            [
                'user_id' => auth()->user()->id,
                'filament_form_id' => $this->filamentForm->id,
            ],
            [
                'entry' => $entry,
            ],
        );

        return redirect(route(config('filament-form-builder.filament-form-user-show-route'), $entryModel));
    }

    public function parseValue(FilamentFormField $field, ?string $value): string
    {
        if (! $value && ! $field->type->isBool()) {
            return '';
        }

        $valueData = '';

        if (! $field->type->hasOptions() && is_array($value)) {
            // !!! this needs to be tested
            $valuesArray = [];

            foreach ($value as $individualValue) {
                array_push($valuesArray, $field->options[$individualValue]);
            }

            $valueData = implode(', ', $valuesArray);
        } elseif ($field->type->hasOptions() && ! is_array($value)) {
            $valueData = $field->options[$value];
        } elseif ($field->type->isBool()) {
            $valueData = (bool) $value ? 'true' : 'false';
        } else {
            $valueData = $value;
        }

        return $valueData;
    }

    public function render()
    {
        return view('filament-form-builder::livewire.filament-form.show');
    }
}

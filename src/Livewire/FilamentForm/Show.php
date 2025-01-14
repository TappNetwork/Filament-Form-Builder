<?php

namespace Tapp\FilamentFormBuilder\Livewire\FilamentForm;

use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Components\Field;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;
use Tapp\FilamentFormBuilder\Models\FilamentFormField;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;

class Show extends Component implements HasForms
{
    use InteractsWithForms;

    public FilamentForm $filamentForm;

    public bool $blockRedirect;

    public ?array $data = [];

    public function mount(FilamentForm $form, bool $blockRedirect = false)
    {
        $this->filamentForm = $form->load('filamentFormFields');

        $this->form->fill($this->data);

        $this->blockRedirect = $blockRedirect;

        if (! $this->filamentForm->permit_guest_entries && ! auth()->check()) {
            return redirect('/', 401);
        }
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

            if ($fieldData->type === FilamentFieldTypeEnum::SELECT_MULTIPLE) {
                $filamentField = $filamentField
                    ->multiple()
                    // !!! remove this before deploy
                    ->live()
                    ->required()
                    ->default([]);
            }

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
                ->options(array_combine($fieldData['options'], $fieldData['options']));
        }

        if (isset($fieldData['hint'])) {
            $filamentField = $filamentField
                ->hint($fieldData['hint']);
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

        if (auth()->check()) {
            $entryModel = FilamentFormUser::updateOrCreate(
                [
                    'user_id' => auth()->user()->id ?? null,
                    'filament_form_id' => $this->filamentForm->id,
                ],
                [
                    'entry' => $entry,
                ],
            );
        } else {
            $entryModel = FilamentFormUser::create(
                [
                    'filament_form_id' => $this->filamentForm->id,
                    'entry' => $entry,
                ],
            );
        }

        $this->dispatch('entrySaved', $entryModel->id);

        if ($this->blockRedirect) {
            return;
        }

        if ($this->filamentForm->redirect_url) {
            return redirect($this->filamentForm->redirect_url);
        } else {
            return redirect()
                ->route(config('filament-form-builder.filament-form-user-show-route'), $entryModel);
        }

    }

    public function parseValue(FilamentFormField $field, string|array|null $value): string|array
    {
        if ($value === null && ! $field->type->isBool()) {
            return '';
        }

        $valueData = '';

        if ($field->type->hasOptions() && is_array($value)) {
            $valueData = implode(', ', $value);
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

<?php

namespace Tapp\FilamentFormBuilder\Livewire\FilamentForm;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step as WizardStep;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;
use Tapp\FilamentFormBuilder\Events\EntrySaved;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormUser;

/**
 * @property Form $form
 */
class Show extends Component implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    public FilamentForm $filamentForm;

    public bool $blockRedirect;

    public bool $preview;

    public ?FilamentFormUser $guestEntry = null;

    public bool $showGuestEntry = false;

    public ?array $data = [];

    public function mount(FilamentForm $form, bool $blockRedirect = false, bool $preview = false)
    {
        $this->preview = $preview;

        $this->filamentForm = $form->load('filamentFormFields');

        $this->form->fill($this->data);

        $this->blockRedirect = $blockRedirect;
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

        // Build components for each field first
        $componentsByFieldId = [];
        /** @var \Tapp\FilamentFormBuilder\Models\FilamentFormField $fieldData */
        foreach ($this->filamentForm->filamentFormFields as $fieldData) {
            $component = $fieldData->type->className()::make($fieldData->id);
            $component = $this->parseField($component, $fieldData->toArray());

            if ($fieldData->type === FilamentFieldTypeEnum::SELECT_MULTIPLE) {
                $component = $component
                    ->multiple()
                    ->live()
                    ->required()
                    ->default([]);
            } elseif ($fieldData->type === FilamentFieldTypeEnum::REPEATER) {
                $component = $component
                    ->schema(function () use ($fieldData) {
                        $subSchema = [];
                        foreach ($fieldData->schema ?? [] as $index => $subField) {
                            $subFieldId = $subField['id'] ?? $fieldData->id.'_'.$subField['type'].'_'.$index;
                            $subFieldComponent = FilamentFieldTypeEnum::fromString($subField['type'])->className()::make($subFieldId);

                            if (isset($subField['label'])) {
                                $subFieldComponent = $subFieldComponent->label($subField['label']);
                            }

                            if (isset($subField['required']) && $subField['required']) {
                                $subFieldComponent = $subFieldComponent->required();
                            }

                            if (isset($subField['options'])) {
                                $subFieldComponent = $subFieldComponent->options(array_combine($subField['options'], $subField['options']));
                            }

                            if (isset($subField['hint'])) {
                                $subFieldComponent = $subFieldComponent->hint($subField['hint']);
                            }

                            if (isset($subField['rules'])) {
                                $subFieldComponent = $subFieldComponent->rules($subField['rules']);
                            }

                            $subSchema[] = $subFieldComponent;
                        }

                        return $subSchema;
                    })
                    ->default([])
                    ->live();
            }

            $componentsByFieldId[$fieldData->id] = $component;
        }

        // If wizard is disabled, return flat schema as before
        if (! $this->filamentForm->is_wizard) {
            return array_values($componentsByFieldId);
        }

        // Group by step and build Wizard
        $steps = $this->filamentForm->getOrderedWizardSteps();
        $fieldsByStepIndex = [];
        foreach ($this->filamentForm->filamentFormFields as $fieldData) {
            $stepIndex = $fieldData->step ?? 0;
            if (! array_key_exists($stepIndex, $fieldsByStepIndex)) {
                $fieldsByStepIndex[$stepIndex] = [];
            }
            $fieldsByStepIndex[$stepIndex][] = $componentsByFieldId[$fieldData->id];
        }

        $wizardSteps = [];
        foreach ($steps as $index => $step) {
            $title = is_array($step) ? ($step['title'] ?? ('Step '.($index + 1))) : (string) $step;
            $description = is_array($step) ? ($step['description'] ?? null) : null;
            $schemaForStep = $fieldsByStepIndex[$index] ?? [];
            $wizardSteps[] = WizardStep::make($title)
                ->description($description)
                ->schema($schemaForStep);
        }

        return [
            Wizard::make()
                ->schema($wizardSteps)
                ->skippable(false),
        ];
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
        $formState = $this->form->getState();

        if ($this->preview) {
            return;
        }

        $entry = [];

        foreach ($formState as $key => $value) {
            /** @var \Tapp\FilamentFormBuilder\Models\FilamentFormField|null $field */
            $field = $this->filamentForm
                ->filamentFormFields
                ->find($key);

            if (! $field) {
                continue;
            }

            if ($field->type === FilamentFieldTypeEnum::REPEATER) {
                if (is_array($value)) {
                    foreach ($value as $index => $repeaterEntry) {
                        if (! is_array($repeaterEntry)) {
                            continue;
                        }

                        foreach ($repeaterEntry as $subKey => $subValue) {
                            // Extract the index from the key (e.g., "47_TEXT_0" -> "0")
                            preg_match('/_(\d+)$/', $subKey, $matches);
                            $fieldIndex = $matches[1] ?? 0;

                            // Get the field from the schema using the index
                            $subField = $field->schema[$fieldIndex] ?? null;

                            if (! $subField) {
                                continue;
                            }

                            $repeaterLabel = str_replace(' ', '_', strtolower($field->label));
                            $fieldLabel = str_replace(' ', '_', strtolower($subField['label']));

                            array_push($entry, [
                                'type' => FilamentFieldTypeEnum::fromString($subField['type'])->fieldName(),
                                'field' => $subField['label'].' ('.($index + 1).')',
                                'answer' => $subValue,
                                'field_id' => "{$repeaterLabel}_{$fieldLabel}_".($index + 1),
                            ]);
                        }
                    }
                }
            } else {
                $valueData = $this->parseValue($field, $value);

                array_push($entry, [
                    'type' => $field->type->fieldName(),
                    'field' => $field->label,
                    'answer' => $valueData,
                    'field_id' => $field->id,
                ]);
            }
        }

        if (Auth::check()) {
            $entryModel = FilamentFormUser::updateOrCreate(
                [
                    'user_id' => Auth::user()->id ?? null,
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

        // Handle file uploads
        foreach ($this->filamentForm->filamentFormFields as $field) {
            /** @var \Tapp\FilamentFormBuilder\Models\FilamentFormField $field */
            if ($field->type === FilamentFieldTypeEnum::FILE_UPLOAD) {
                $fileKey = $field->id;
                $fileData = $this->data[$fileKey] ?? null;

                if ($fileData && is_array($fileData)) {
                    $temporaryFile = collect($fileData)->first();
                    if ($temporaryFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        // Remove existing media with the same field_id
                        $entryModel->getMedia()
                            ->filter(fn ($media) => $media->getCustomProperty('field_id') === $field->id)
                            ->each(fn ($media) => $media->delete());

                        $media = $entryModel->addMedia($temporaryFile->getRealPath())
                            ->withCustomProperties([
                                'field_label' => $field->label,
                                'field_id' => $field->id,
                                'original_name' => $temporaryFile->getClientOriginalName(),
                            ])
                            ->toMediaCollection();
                    }
                }
            }
        }

        // dispatch laravel event and livewire event
        event(new EntrySaved($entryModel));
        $this->dispatch('entrySaved', $entryModel->id);

        return $this->handleFormSubmissionRedirect($entryModel);
    }

    protected function handleFormSubmissionRedirect(FilamentFormUser $entryModel): mixed
    {
        if ($this->blockRedirect) {
            return null;
        }

        if ($this->filamentForm->redirect_url) {
            return redirect($this->filamentForm->redirect_url);
        }

        if (Auth::check()) {
            return redirect()
                ->route(config('filament-form-builder.filament-form-user-show-route'), $entryModel);
        }

        $this->showGuestEntry = true;
        $this->guestEntry = $entryModel;

        return null;
    }

    public function parseValue(\Tapp\FilamentFormBuilder\Models\FilamentFormField $field, string|array|null $value): string|array
    {
        if ($value === null && ! $field->type->isBool()) {
            return '';
        }

        if ($field->type === FilamentFieldTypeEnum::REPEATER) {
            return $value;
        }

        $valueData = '';

        if ($field->type->hasOptions() && is_array($value)) {
            $valueData = implode(', ', $value);
        } elseif ($field->type->hasOptions() && ! is_array($value)) {
            $valueData = $value;
        } elseif ($field->type->isBool()) {
            $valueData = (bool) $value ? 'true' : 'false';
        } else {
            $valueData = $value;
        }

        return $valueData;
    }

    public function render()
    {
        /** @phpstan-ignore-next-line */
        return view('filament-form-builder::livewire.filament-form.show');
    }
}

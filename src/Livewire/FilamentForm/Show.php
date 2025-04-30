<?php

namespace Tapp\FilamentFormBuilder\Livewire\FilamentForm;

use Filament\Forms\Components\Field;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;
use Tapp\FilamentFormBuilder\Events\EntrySaved;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormField;
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

    public ?array $data = [];

    public function mount(FilamentForm $form, bool $blockRedirect = false, bool $preview = false)
    {
        $this->preview = $preview;

        $this->filamentForm = $form->load('filamentFormFields');

        $this->form->fill($this->data);

        $this->blockRedirect = $blockRedirect;

        if (! $this->filamentForm->permit_guest_entries && ! Auth::check()) {
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

        if ($this->preview) {
            return;
        }

        $entry = [];

        foreach ($this->form->getState() as $key => $value) {
            $field = $this->filamentForm
                ->filamentFormFields
                ->find($key);

            $valueData = $this->parseValue($field, $value);

            array_push($entry, [
                'type' => $field->type->fieldName(),
                'field' => $field->label,
                'answer' => $valueData,
                'field_id' => $field->id,
            ]);
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
        return view('filament-form-builder::livewire.filament-form.show');
    }
}

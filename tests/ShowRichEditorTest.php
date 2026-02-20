<?php

use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Collection;
use Tapp\FilamentFormBuilder\Livewire\FilamentForm\Show;
use Tapp\FilamentFormBuilder\Models\FilamentForm;
use Tapp\FilamentFormBuilder\Models\FilamentFormField;

it('disables the attachFiles toolbar button for rich editor fields in the form schema', function () {
    $field = new FilamentFormField;
    $field->setRawAttributes([
        'id' => 1,
        'filament_form_id' => 1,
        'type' => 'RICH_EDITOR',
        'label' => 'Content',
        'required' => false,
        'hint' => null,
        'rules' => null,
        'options' => null,
        'schema' => null,
        'step' => null,
        'order' => 1,
    ]);

    $form = new FilamentForm;
    $form->setRawAttributes([
        'id' => 1,
        'name' => 'Test Form',
        'description' => null,
        'redirect_url' => null,
        'permit_guest_entries' => false,
        'is_wizard' => false,
    ]);
    $form->setRelation('filamentFormFields', Collection::make([$field]));

    $show = new Show;
    $show->filamentForm = $form;

    $schema = $show->getFormSchema();

    expect($schema)->toHaveCount(1);

    $richEditor = $schema[0];
    expect($richEditor)->toBeInstanceOf(RichEditor::class);

    // In Filament v5, disableToolbarButtons() queues a modification rather than
    // immediately filtering the array. Inspect the queue via reflection to confirm
    // that attachFiles has been disabled without needing a mounted container.
    $modifications = (new \ReflectionProperty($richEditor, 'toolbarButtonsModifications'))->getValue($richEditor);
    expect($modifications)->toContain(['type' => 'disable', 'buttons' => ['attachFiles']]);
});

it('does not affect non-rich-editor fields', function () {
    $field = new FilamentFormField;
    $field->setRawAttributes([
        'id' => 2,
        'filament_form_id' => 1,
        'type' => 'TEXT',
        'label' => 'Name',
        'required' => false,
        'hint' => null,
        'rules' => null,
        'options' => null,
        'schema' => null,
        'step' => null,
        'order' => 1,
    ]);

    $form = new FilamentForm;
    $form->setRawAttributes([
        'id' => 1,
        'name' => 'Test Form',
        'description' => null,
        'redirect_url' => null,
        'permit_guest_entries' => false,
        'is_wizard' => false,
    ]);
    $form->setRelation('filamentFormFields', Collection::make([$field]));

    $show = new Show;
    $show->filamentForm = $form;

    $schema = $show->getFormSchema();

    expect($schema)->toHaveCount(1);
    expect($schema[0])->not->toBeInstanceOf(RichEditor::class);
});

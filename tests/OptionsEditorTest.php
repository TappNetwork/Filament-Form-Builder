<?php

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;
use Tapp\FilamentFormBuilder\Support\OptionsEditor;

it('defaults to tags for every type', function (): void {
    config(['filament-form-builder.options_editor' => 'tags']);

    expect(OptionsEditor::forType('RADIO'))->toBe(OptionsEditor::TAGS)
        ->and(OptionsEditor::isTags('SELECT'))->toBeTrue()
        ->and(OptionsEditor::isKeyValue(FilamentFieldTypeEnum::CHECKBOX_LIST))->toBeFalse();
});

it('applies a global key_value editor', function (): void {
    config(['filament-form-builder.options_editor' => 'key_value']);

    expect(OptionsEditor::forType('RADIO'))->toBe(OptionsEditor::KEY_VALUE)
        ->and(OptionsEditor::isKeyValue('SELECT_MULTIPLE'))->toBeTrue();
});

it('resolves a per-type map with default fallback', function (): void {
    config(['filament-form-builder.options_editor' => [
        'RADIO' => 'key_value',
        'SELECT' => 'tags',
        'default' => 'tags',
    ]]);

    expect(OptionsEditor::forType('RADIO'))->toBe(OptionsEditor::KEY_VALUE)
        ->and(OptionsEditor::forType('SELECT'))->toBe(OptionsEditor::TAGS)
        ->and(OptionsEditor::forType('CHECKBOX_LIST'))->toBe(OptionsEditor::TAGS)
        ->and(OptionsEditor::forType(null))->toBe(OptionsEditor::TAGS);
});

it('resolves a closure editor', function (): void {
    config([
        'filament-form-builder.options_editor' => fn (?string $type): string => $type === 'RADIO'
            ? OptionsEditor::KEY_VALUE
            : OptionsEditor::TAGS,
    ]);

    expect(OptionsEditor::forType('RADIO'))->toBe(OptionsEditor::KEY_VALUE)
        ->and(OptionsEditor::forType('SELECT'))->toBe(OptionsEditor::TAGS);
});

it('builds only the editor matching the field type', function (): void {
    config(['filament-form-builder.options_editor' => ['RADIO' => 'key_value', 'default' => 'tags']]);

    expect(OptionsEditor::components('RADIO'))
        ->toHaveCount(1)
        ->sequence(fn ($component) => $component->toBeInstanceOf(KeyValue::class));

    expect(OptionsEditor::components('SELECT'))
        ->toHaveCount(1)
        ->sequence(fn ($component) => $component->toBeInstanceOf(TagsInput::class));

    expect(OptionsEditor::components('TEXT'))->toBe([]);
    expect(OptionsEditor::components(null))->toBe([]);
});

it('normalizes list and keyed option payloads for each editor', function (): void {
    expect(OptionsEditor::normalizeForTags(['a' => 'Alpha', 'b' => 'Beta']))
        ->toBe(['Alpha', 'Beta'])
        ->and(OptionsEditor::normalizeForTags(['Alpha', 'Beta']))
        ->toBe(['Alpha', 'Beta'])
        ->and(OptionsEditor::normalizeForKeyValue(['Alpha', 'Beta']))
        ->toBe(['Alpha' => 'Alpha', 'Beta' => 'Beta'])
        ->and(OptionsEditor::normalizeForKeyValue(['a' => 'Alpha', 'b' => 'Beta']))
        ->toBe(['a' => 'Alpha', 'b' => 'Beta']);
});

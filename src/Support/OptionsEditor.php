<?php

namespace Tapp\FilamentFormBuilder\Support;

use Closure;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Tapp\FilamentFormBuilder\Enums\FilamentFieldTypeEnum;

/**
 * Resolves whether field options are edited as TagsInput ("tags") or KeyValue ("key_value").
 *
 * Config (`filament-form-builder.options_editor`) may be:
 * - string: `'tags'` or `'key_value'` (applies to every options-capable type)
 * - array: per-type map, e.g. `['RADIO' => 'key_value', 'default' => 'tags']`
 * - Closure: `fn (?string $type): string => ...`
 */
final class OptionsEditor
{
    public const string TAGS = 'tags';

    public const string KEY_VALUE = 'key_value';

    public static function forType(null|string|FilamentFieldTypeEnum $type): string
    {
        $typeName = $type instanceof FilamentFieldTypeEnum ? $type->name : $type;
        $config = config('filament-form-builder.options_editor', self::TAGS);

        if ($config instanceof Closure) {
            $resolved = $config($typeName);

            return self::normalize(is_string($resolved) ? $resolved : self::TAGS);
        }

        if (is_string($config)) {
            return self::normalize($config);
        }

        if (is_array($config)) {
            if (is_string($typeName) && isset($config[$typeName]) && is_string($config[$typeName])) {
                return self::normalize($config[$typeName]);
            }

            if (isset($config['default']) && is_string($config['default'])) {
                return self::normalize($config['default']);
            }
        }

        return self::TAGS;
    }

    public static function isTags(null|string|FilamentFieldTypeEnum $type): bool
    {
        return self::forType($type) === self::TAGS;
    }

    public static function isKeyValue(null|string|FilamentFieldTypeEnum $type): bool
    {
        return self::forType($type) === self::KEY_VALUE;
    }

    public static function typeHasOptions(mixed $type): bool
    {
        if (! is_string($type) || $type === '') {
            return false;
        }

        $fieldType = FilamentFieldTypeEnum::fromString($type);

        return $fieldType instanceof FilamentFieldTypeEnum && $fieldType->hasOptions();
    }

    /**
     * Only the editor matching the field type is built: two components sharing the
     * `options` state path would hydrate in sequence and overwrite each other's state.
     *
     * @return list<TagsInput|KeyValue>
     */
    public static function components(mixed $type): array
    {
        if (! self::typeHasOptions($type)) {
            return [];
        }

        if (self::isKeyValue(is_string($type) ? $type : null)) {
            return [
                KeyValue::make('options')
                    ->keyLabel('Key')
                    ->valueLabel('Label')
                    ->addActionLabel('Add option')
                    ->reorderable()
                    ->hint('Key is the stored value (for example a, b, c). Label is what respondents see.')
                    ->formatStateUsing(fn (mixed $state): array => self::normalizeForKeyValue($state))
                    ->columnSpanFull(),
            ];
        }

        return [
            TagsInput::make('options')
                ->placeholder('Add options')
                ->hint('Press enter after inputting each option')
                ->formatStateUsing(fn (mixed $state): array => self::normalizeForTags($state))
                ->columnSpanFull(),
        ];
    }

    /**
     * TagsInput expects a list of strings.
     *
     * @return list<string>
     */
    public static function normalizeForTags(mixed $state): array
    {
        if (! is_array($state)) {
            return [];
        }

        return array_values(array_filter(
            $state,
            fn (mixed $value): bool => is_string($value) && $value !== '',
        ));
    }

    /**
     * KeyValue expects an associative value => label map.
     *
     * @return array<string, string>
     */
    public static function normalizeForKeyValue(mixed $state): array
    {
        if (! is_array($state)) {
            return [];
        }

        if ($state === []) {
            return [];
        }

        if (array_is_list($state)) {
            $labels = array_values(array_filter(
                $state,
                fn (mixed $value): bool => is_string($value) && $value !== '',
            ));

            /** @var array<string, string> */
            return array_combine($labels, $labels) ?: [];
        }

        $normalized = [];

        foreach ($state as $key => $value) {
            if (! is_string($value) || $value === '') {
                continue;
            }

            $normalized[(string) $key] = $value;
        }

        return $normalized;
    }

    private static function normalize(string $editor): string
    {
        return $editor === self::KEY_VALUE ? self::KEY_VALUE : self::TAGS;
    }
}

<?php

namespace Tapp\FilamentFormBuilder\Enums;

use Filament\Support\Contracts\HasLabel;

enum FilamentFieldTypeEnum implements HasLabel
{
    case TEXT;
    case TEXTAREA;
    case SELECT;
    case SELECT_MULTIPLE;
    case RICH_EDITOR;
    case TOGGLE;
    case CHECKBOX;
    case RADIO;
    case DATE_TIME_PICKER;
    case DATE_PICKER;
    case TIME_PICKER;
    case MARKDOWN_EDITOR;
    case COLOR_PICKER;
    case FILE_UPLOAD;
    case REPEATER;
    case HEADING;

    public static function fromString(string $type): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $type) {
                return $case;
            }
        }

        return null;  // Return null if no match is found
    }

    public function getLabel(): string
    {
        return $this->fieldName();
    }

    public function fieldName(): string
    {
        return match ($this) {
            self::TEXT => __('Text Input'),
            self::TEXTAREA => __('Textarea'),
            self::SELECT => __('Select'),
            self::SELECT_MULTIPLE => __('Select Multiple'),
            self::RICH_EDITOR => __('Rich Editor'),
            self::TOGGLE => __('Toggle'),
            self::CHECKBOX => __('Checkbox'),
            self::RADIO => __('Radio'),
            self::DATE_TIME_PICKER => __('DateTime Picker'),
            self::DATE_PICKER => __('Date Picker'),
            self::TIME_PICKER => __('Time Picker'),
            self::MARKDOWN_EDITOR => __('Markdown Editor'),
            self::COLOR_PICKER => __('Color Picker'),
            self::FILE_UPLOAD => __('File Upload'),
            self::REPEATER => __('Repeater'),
            self::HEADING => __('Heading'),
        };
    }

    public function className(): string
    {
        return match ($this) {
            self::TEXT => 'Filament\Forms\Components\TextInput',
            self::TEXTAREA => 'Filament\Forms\Components\Textarea',
            self::SELECT => 'Filament\Forms\Components\Select',
            self::SELECT_MULTIPLE => 'Filament\Forms\Components\Select',
            self::RICH_EDITOR => 'Filament\Forms\Components\RichEditor',
            self::TOGGLE => 'Filament\Forms\Components\Toggle',
            self::CHECKBOX => 'Filament\Forms\Components\Checkbox',
            self::RADIO => 'Filament\Forms\Components\Radio',
            self::DATE_TIME_PICKER => 'Filament\Forms\Components\DateTimePicker',
            self::DATE_PICKER => 'Filament\Forms\Components\DatePicker',
            self::TIME_PICKER => 'Filament\Forms\Components\TimePicker',
            self::MARKDOWN_EDITOR => 'Filament\Forms\Components\MarkdownEditor',
            self::COLOR_PICKER => 'Filament\Forms\Components\ColorPicker',
            self::FILE_UPLOAD => 'Filament\Forms\Components\SpatieMediaLibraryFileUpload',
            self::REPEATER => 'Filament\Forms\Components\Repeater',
            self::HEADING => 'Tapp\FilamentFormBuilder\Filament\Forms\Components\Heading',
        };
    }

    public function hasOptions(): bool
    {
        return match ($this) {
            self::TEXT => false,
            self::TEXTAREA => false,
            self::SELECT => true,
            self::SELECT_MULTIPLE => true,
            self::RICH_EDITOR => false,
            self::TOGGLE => false,
            self::CHECKBOX => false,
            self::RADIO => true,
            self::DATE_TIME_PICKER => false,
            self::DATE_PICKER => false,
            self::TIME_PICKER => false,
            self::MARKDOWN_EDITOR => false,
            self::COLOR_PICKER => false,
            self::FILE_UPLOAD => false,
            self::REPEATER => false,
            self::HEADING => false,
        };
    }

    public function isBool(): bool
    {
        return match ($this) {
            self::TEXT => false,
            self::TEXTAREA => false,
            self::SELECT => false,
            self::SELECT_MULTIPLE => false,
            self::RICH_EDITOR => false,
            self::TOGGLE => true,
            self::CHECKBOX => true,
            self::RADIO => false,
            self::DATE_TIME_PICKER => false,
            self::DATE_PICKER => false,
            self::TIME_PICKER => false,
            self::MARKDOWN_EDITOR => false,
            self::COLOR_PICKER => false,
            self::FILE_UPLOAD => false,
            self::REPEATER => false,
            self::HEADING => false,
        };
    }
}

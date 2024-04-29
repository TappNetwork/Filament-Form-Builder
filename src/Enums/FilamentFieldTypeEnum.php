<?php

namespace Tapp\FilamentForms\Enums;

use Filament\Support\Contracts\HasLabel;

enum FilamentFieldTypeEnum implements HasLabel
{
    case TEXT;
    case TEXTAREA;
    case SELECT;
    case RICH_EDITOR;
    case TOGGLE;
    case CHECKBOX;
    case RADIO;
    case DATE_TIME_PICKER;
    case DATE_PICKER;
    case TIME_PICKER;
    case MARKDOWN_EDITOR;
    case COLOR_PICKER;

    public static function fromString(string $type): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $type) {
                return $case;
            }
        }
        return null;  // Return null if no match is found
    }

    public function getLabel(): ?string
    {
        return $this->fieldName();
    }

    public function fieldName(): string
    {
        return match($this)
        {
            self::TEXT => 'Text Input',
            self::TEXTAREA => 'Textarea',
            self::SELECT => 'Select',
            self::RICH_EDITOR => 'Rich Editor',
            self::TOGGLE  => 'Toggle',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio',
            self::DATE_TIME_PICKER => 'DateTime Picker',
            self::DATE_PICKER => 'Date Picker',
            self::TIME_PICKER => 'Time Picker',
            self::MARKDOWN_EDITOR => 'Markdown Editor',
            self::COLOR_PICKER => 'Color Picker',
        };
    }

    public function className(): string
    {
        return match($this)
        {
            self::TEXT => 'Filament\Forms\Components\TextInput',
            self::TEXTAREA => 'Filament\Forms\Components\Textarea',
            self::SELECT => 'Filament\Forms\Components\Select',
            self::RICH_EDITOR => 'Filament\Forms\Components\RichEditor',
            self::TOGGLE  => 'Filament\Forms\Components\Toggle',
            self::CHECKBOX => 'Filament\Forms\Components\Checkbox',
            self::RADIO => 'Filament\Forms\Components\Radio',
            self::DATE_TIME_PICKER => 'Filament\Forms\Components\DateTimePicker',
            self::DATE_PICKER => 'Filament\Forms\Components\DatePicker',
            self::TIME_PICKER => 'Filament\Forms\Components\TimePicker',
            self::MARKDOWN_EDITOR => 'Filament\Forms\Components\MarkdownEditor',
            self::COLOR_PICKER => 'Filament\Forms\Components\ColorPicker',
        };
    }

    public function hasOptions(): bool
    {
        return match($this)
        {
            self::TEXT => false,
            self::TEXTAREA => false,
            self::SELECT => true,
            self::RICH_EDITOR => false,
            self::TOGGLE  => false,
            self::CHECKBOX => false,
            self::RADIO => true,
            self::DATE_TIME_PICKER => false,
            self::DATE_PICKER => false,
            self::TIME_PICKER => false,
            self::MARKDOWN_EDITOR => false,
            self::COLOR_PICKER => false,
        };
    }

    public function isBool(): bool
    {
        return match($this)
        {
            self::TEXT => false,
            self::TEXTAREA => false,
            self::SELECT => false,
            self::RICH_EDITOR => false,
            self::TOGGLE  => true,
            self::CHECKBOX => true,
            self::RADIO => false,
            self::DATE_TIME_PICKER => false,
            self::DATE_PICKER => false,
            self::TIME_PICKER => false,
            self::MARKDOWN_EDITOR => false,
            self::COLOR_PICKER => false,
        };
    }
}

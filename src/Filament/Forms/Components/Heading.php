<?php

namespace Tapp\FilamentFormBuilder\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class Heading extends Field
{
    protected string $view = 'filament-form-builder::forms.components.heading';

    protected function setUp(): void
    {
        parent::setUp();

        $this->disabled();
    }
}

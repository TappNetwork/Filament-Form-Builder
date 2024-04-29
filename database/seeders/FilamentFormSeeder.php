<?php

namespace Database\Seeders;

use Faker\Factory;
use Faker\Generator;
use Tapp\FilamentForms\Models\FilamentForm;
use Illuminate\Database\Seeder;
use Tapp\FilamentForms\Models\FilamentFormField;
use Tapp\FilamentForms\Enums\FilamentFieldTypeEnum;

class FilamentFormSeeder extends Seeder
{
    public Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = FilamentForm::factory(10)->create();

        foreach ($forms as $form) {
            $this->generateQuestions($form);
        }
    }

    public function generateQuestions(FilamentForm $form): void
    {
        $count = rand(5,10);

        for ($i = 0; $i < $count; ++$i) {
            $field = FilamentFormField::factory()->create([
                'type' => $this->randomType(),
                'filament_form_id' => $form->id,
                'order' => $i,
            ]);

            if ($field->type->hasOptions()) {
                $field->options =  $this->generateOptions();
            }

            $field->save();
        }
    }

    public function generateOptions(): array
    {
        $count = rand(3,6);

        $options = [];

        for ($i = 0; $i < $count; ++$i) {
            array_push($options, $this->faker->bs());
        }

        return $options;
    }

    public function randomType(): FilamentFieldTypeEnum
    {
        $seededFieldTypes = [
            FilamentFieldTypeEnum::TEXT,
            FilamentFieldTypeEnum::SELECT,
            FilamentFieldTypeEnum::RADIO,
            FilamentFieldTypeEnum::CHECKBOX,
        ];

        return $seededFieldTypes[rand(0,3)];
    }
}

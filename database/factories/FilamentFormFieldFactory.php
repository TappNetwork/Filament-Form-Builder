<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FilamentFormField>
 */
class FilamentFormFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->text(rand(25, 60)).'?',
            'hint' => rand(0, 5) > 4 ? $this->faker->text(rand(50, 100)) : null,
            'required' => (rand(0, 2) > 1),
        ];
    }
}

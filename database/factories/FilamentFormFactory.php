<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FilamentFormFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->bs(),
            'description' => $this->faker->realText(),
        ];
    }
}

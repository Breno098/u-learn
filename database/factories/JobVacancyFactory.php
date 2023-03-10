<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JobVacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => fake()->jobTitle,
            'description' => fake()->realText(),
            'link' => fake()->url,
            'start_at' => fake()->dateTimeBetween('-30 days'),
            'end_at' => fake()->dateTimeBetween('now', '+30 days'),
            'display_to' => fake()->randomElement(['Grupo', 'Usuário'])
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->streetName(),
            'descritiption' => fake()->realText(100),
            'level' => fake()->randomElement(['iniciante', 'intermediário', 'avançado']),
            'duration' => fake()->date(),
            'video' => fake()->url(),
            'url_sale' => fake()->url(),
            'wallpaper_image' => \App\Helpers\Seeder\Random::image(),
            'tumb_image' => \App\Helpers\Seeder\Random::image(),
        ];
    }
}

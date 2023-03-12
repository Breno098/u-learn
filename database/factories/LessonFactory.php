<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->name,
            'description' => fake()->realText(100),
            'duration' => fake()->time(),
            'video' => fake()->url(),
            'wallpaper' => \App\Helpers\Seeder\Random::image()
        ];
    }
}

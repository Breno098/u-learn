<?php

namespace Database\Factories;

use App\Enums\ChapterPlayerEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ChapterFactory extends Factory
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
            'cast' => Arr::join([
                fake()->name(),
                fake()->name(),
                fake()->name()
            ], ', ', ' e '),
            'direction' => fake()->name(),
            'main_player' => fake()->randomElement(ChapterPlayerEnum::toValues()),
            'vimeo_link' => fake()->url(),
            'vimeo_embed' => fake()->unique()->slug(2),
            'sambatech_link' => fake()->url(),
            'sambatech_embed' => fake()->unique()->slug(2),
            'image' => \App\Helpers\Seeder\Random::image()
        ];
    }
}

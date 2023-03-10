<?php

namespace Database\Factories;

use App\Enums\ContentAuthorEnum;
use App\Enums\ContentProductionTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $author = fake()->randomElement(ContentAuthorEnum::toValues());

        return [
            'name' => fake()->streetName(),
            'launch_start_at' => now()->addDays(random_int(1, 5)),
            'launch_end_at' => now()->addDays(random_int(10, 15)),
            'author' => $author,
            'responsible_for_production' => $author == 'outro' ? fake()->name() : null,
            'production_type' => fake()->randomElement(ContentProductionTypeEnum::toValues()),
            'end_at' => now()->addDays(random_int(50, 55)),
            'highlight' => fake()->boolean(),
            'access_count' => fake()->numberBetween(0, 500),
            'main_image' => \App\Helpers\Seeder\Random::image(),
            'secondary_image' => \App\Helpers\Seeder\Random::image(),
            'descritiption_image' => \App\Helpers\Seeder\Random::image(),
            'screensaver_image' => \App\Helpers\Seeder\Random::image(),
        ];
    }
}

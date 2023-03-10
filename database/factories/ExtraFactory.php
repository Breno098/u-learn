<?php

namespace Database\Factories;

use App\Enums\ExtraPlayerEnum;
use App\Enums\ExtraTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExtraFactory extends Factory
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
            'type' => fake()->randomElement(ExtraTypeEnum::toValues()),
            'player' => fake()->randomElement(ExtraPlayerEnum::toValues()),
            'embed' => fake()->url,
        ];
    }
}

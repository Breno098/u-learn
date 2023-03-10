<?php

namespace Database\Factories;

use App\Enums\LiveEventTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiveEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $dateStart =  new Carbon(fake()->dateTimeBetween('-3 months', '+3 months'));

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->realText(rand(20, 100)),
            'event_type' => 'live',
            'type' => fake()->randomElement(LiveEventTypeEnum::toValues()),
            'start_at' => $dateStart,
            'end_at' => $dateStart->addHours(2),
            'embed' => fake()->url(),
            'image' => \App\Helpers\Seeder\Random::image()
        ];
    }
}

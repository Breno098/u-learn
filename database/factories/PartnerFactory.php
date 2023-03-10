<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerFactory extends Factory
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
            'company_name' => fake()->company,
            'phone' => fake()->phoneNumber,
            'email' => fake()->email,
            'link' => fake()->url,
            'start_at' => fake()->dateTimeBetween('-30 days'),
            'end_at' => fake()->dateTimeBetween('now', '+30 days'),
            'image' => \App\Helpers\Seeder\Random::image()
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
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
            'url_sale' => fake()->url,
            'terms_acceptance' => fake()->realText(150),
            'credit_card_accept' => fake()->boolean,
            'credit_card_value' => fake()->numberBetween(100, 10000),
            'boleto_accept'=> fake()->boolean,
            'boleto_value' => fake()->numberBetween(100, 10000),
            'pix_accept' => fake()->boolean,
            'pix_value' => fake()->numberBetween(100, 10000),
            'checkout_code' => fake()->randomKey,
            'checkout_link' => fake()->url,
            'image' => \App\Helpers\Seeder\Random::image()
        ];
    }
}

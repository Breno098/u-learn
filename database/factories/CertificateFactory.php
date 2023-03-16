<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
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
            'image' => \App\Helpers\Seeder\Random::image()
        ];
    }
}

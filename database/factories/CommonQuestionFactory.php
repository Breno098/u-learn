<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommonQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question_text' => fake()->realText(50) . ' ?',
            'answer_text' => fake()->realText(50),
            'show' => true,
        ];
    }
}

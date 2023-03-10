<?php

namespace Database\Factories;

use App\Enums\AnswerTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $hasVideo = fake()->boolean();
        $hasAudio = fake()->boolean();
        $hasImage = fake()->boolean();

        return [
            'title' => fake()->realText(30),
            'answer_type' => fake()->randomElement(AnswerTypeEnum::toValues()),
            'has_video' => $hasVideo,
            'has_audio' => $hasAudio,
            'has_image' => $hasImage,
            'video' => $hasVideo ? fake()->url() : null,
            'audio' => $hasAudio ? fake()->url() : null,
            'image' => $hasImage ? \App\Helpers\Seeder\Random::image() : null,
        ];
    }
}

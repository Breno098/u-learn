<?php

namespace Database\Seeders;

use App\Enums\AnswerTypeEnum;
use App\Models\Chapter;
use App\Models\Content;
use App\Models\Question;
use App\Models\Quizz;
use App\Models\Season;
use Illuminate\Database\Seeder;

class QuizzSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Quizz::factory(random_int(10, 20))->state(function() {
            $content = Content::where('has_seasons', false)->get()->random();

            return [
                'content_id' => $content->id,
            ];
        })->create();

        Quizz::factory(random_int(10, 20))
            ->state(function() {
                $content = Content::where('has_seasons', true)->get()->random();

                if (! $content)
                    return [];

                $season = $content->seasons()->get()->random();

                return [
                    'linkable_type' => Season::class,
                    'linkable_id' => $season->id,
                    'content_id' => $content->id,
                ];
            })
            ->create();

        Quizz::factory(random_int(10, 20))
            ->state(function() {
                $content = Content::where('has_seasons', true)->get()->random();

                if (! $content)
                    return [];

                $season = $content->seasons()->get()->random();

                if (! $season)
                    return [];

                $chapter = $season->chapters()->get()->random();

                if (! $chapter)
                    return [];

                return [
                    'linkable_type' => Chapter::class,
                    'linkable_id' => $chapter->id,
                    'content_id' => $content->id,
                ];
            })
            ->create();

        Quizz::get()->each(function(Quizz $quizz) {
            foreach (range(1, rand(5, 10)) as $number) {
                /** @var Question */
                $question = $quizz->questions()->create(Question::factory()->state(fn() => ['number'=>  $number])->make()->toArray());

                if ($question->answer_type === AnswerTypeEnum::fechada()->value) {
                    foreach (range(1, rand(3, 5)) as $alternativeNumber) {
                        $question->alternatives()->create([
                            'name'=> fake()->text(10),
                            'number' => $alternativeNumber,
                        ]);
                    }
                }
            }
        });
    }
}

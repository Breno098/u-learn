<?php

namespace Database\Seeders;

use App\Enums\AnswerTypeEnum;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Exam::factory(15)->create();

        Exam::get()->each(function(Exam $exam) {
            foreach (range(1, rand(5, 10)) as $number) {
                /** @var Exam */
                $question = $exam->questions()->create(Question::factory()->state(fn() => ['number'=>  $number])->make()->toArray());

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

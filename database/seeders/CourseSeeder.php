<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Genre;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Course::factory(10)
            ->hasAttached(Genre::all()->random(2))
            ->for(Category::all()->random())
            ->create()
            ->each(function(Course $course) {
                foreach (range(1, 5) as $number) {
                    $module = $course->modules()->create(Module::factory()->make([
                        'number' => $number,
                    ])->toArray());

                    $number = 1;
                    foreach (Lesson::all()->random(2) as $lesson) {
                        $module->lessons()->attach($lesson, ['number' => $number++]);
                    }

                    $module->exams()->attach(Exam::all()->random(), ['number' => $number++]);

                    foreach (Lesson::all()->random(3) as $lesson) {
                        $module->lessons()->attach($lesson, ['number' => $number++]);
                    }
                }
            });
    }
}

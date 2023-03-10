<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Genre;
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
                // foreach (range(1, $Course->number_of_seasons) as $number) {
                //     $season = $Course->seasons()->create(Season::factory()->make([
                //         'number' => $number,
                //         'number_of_chapters' => random_int(1, 5)
                //     ])->toArray());

                //     foreach (range(1, $season->number_of_chapters) as $chapterNumber) {
                //         $season->chapters()->create(Chapter::factory()->make(['number' => $chapterNumber])->toArray());
                //     }
                // }
            });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Content;
use App\Models\ContentTag;
use App\Models\Extra;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Content::factory(10)
            ->hasAttached(Section::all()->random(2))
            ->for(Category::all()->random())
            ->has(Extra::factory(random_int(5, 6)))
            ->state([
                'has_seasons' => true,
                'number_of_seasons' => random_int(1, 5)
            ])
            ->create()
            ->each(function(Content $content) {
                $content->genres()->sync(Genre::all()->random(2));

                foreach (range(1, $content->number_of_seasons) as $number) {
                    $season = $content->seasons()->create(Season::factory()->make([
                        'number' => $number,
                        'number_of_chapters' => random_int(1, 5)
                    ])->toArray());

                    foreach (range(1, $season->number_of_chapters) as $chapterNumber) {
                        $season->chapters()->create(Chapter::factory()->make(['number' => $chapterNumber])->toArray());
                    }
                }
            });

        Content::factory(10)
            ->hasAttached(Section::all()->random(2))
            ->for(Category::all()->random())
            ->has(Extra::factory(random_int(5, 6)))
            ->create()
            ->each(function(Content $content) {
                $content->chapter()->create(Chapter::factory()->make()->toArray());
                $content->genres()->sync(Genre::all()->random(2));
            });

        Content::get()
            ->each(function(Content $content) {
                $content->similarContents()->sync(Content::all()->random(random_int(5, 6)));
                $content->contentsOfTheSameCollection()->sync(Content::all()->random(random_int(5, 6)));
            });
    }
}

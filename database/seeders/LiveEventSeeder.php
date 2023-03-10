<?php

namespace Database\Seeders;

use App\Enums\LiveEventTypeEnum;
use App\Models\Chapter;
use App\Models\Content;
use App\Models\Group;
use App\Models\LiveEvent;
use App\Models\Role;
use App\Models\Season;
use App\Models\Student;
use Illuminate\Database\Seeder;

class LiveEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LiveEvent::factory(20)
            ->hasAttached(Student::all()->random())
            ->state(function() {
                $content = Content::where('has_seasons', true)->get()->random();

                if (! $content)
                    return [];

                $season = $content->seasons()->get()->random();

                return [
                    'type' => LiveEventTypeEnum::individual()->value,
                    'responsible_id' => Role::areTeachers()->first()->users->random()->id,
                    'number_of_students' =>  1,
                    'linkable_type' => Season::class,
                    'linkable_id' => $season->id,
                    'content_id' => $content->id,
                    'has_link_with_content' => true
                ];
            })
            ->create();

        LiveEvent::factory(20)
            ->hasAttached(Group::has('students')->inRandomOrder()->take(rand(1, 3))->get())
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
                    'type' => LiveEventTypeEnum::grupo()->value,
                    'responsible_id' => Role::areTeachers()->first()->users->random()->id,
                    'number_of_students' => rand(1, 10),
                    'linkable_type' => Chapter::class,
                    'linkable_id' => $chapter->id,
                    'content_id' => $content->id,
                    'has_link_with_content' => true
                ];
            })
            ->create()
            ->each(function(LiveEvent $liveEvent) {
                $liveEvent->students()->sync($liveEvent->groups->random()->students->random(rand(1, $liveEvent->number_of_students)));
            });
    }
}

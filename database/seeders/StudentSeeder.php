<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Student::factory(random_int(20, 40))->hasAttached(Group::all()->random())->create();

        Student::factory(random_int(20, 40))->hasAttached(Group::all()->random())->equalData()->create();
    }
}

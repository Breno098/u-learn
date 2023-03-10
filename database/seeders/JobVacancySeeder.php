<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\JobVacancy;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobVacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobVacancy::factory(random_int(10, 20))
            ->hasAttached(Student::all()->random(5))
            ->state(function() {
                return [
                    'display_to' => 'Usuário',
                ];
            })
            ->create();

        JobVacancy::factory(random_int(10, 20))
            ->hasAttached(Group::all()->random())
            ->state(function() {
                return [
                    'display_to' => 'Grupo',
                ];
            })
            ->create();
    }
}

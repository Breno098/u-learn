<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->hasAttached(Role::areAdmins()->first())
            ->admin()
            ->create();

        User::factory(random_int(10, 20))
            ->hasAttached(Role::areTeachers()->first())
            ->create();
    }
}

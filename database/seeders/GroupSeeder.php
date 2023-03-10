<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::factory(5)->create();
    }
}

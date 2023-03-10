<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::factory()->hasAttached(Permission::all())->create(['name' => 'Admin']);
        Role::factory()->create(['name' => 'Colaboradores']);
    }
}

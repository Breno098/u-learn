<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()
            ->count(5)
            ->state(new Sequence(
                ['name' => 'Filme'],
                ['name' => 'Série'],
                ['name' => 'Documentário'],
                ['name' => 'Técnico'],
                ['name' => 'Extra'],
            ))
            ->create();
    }
}

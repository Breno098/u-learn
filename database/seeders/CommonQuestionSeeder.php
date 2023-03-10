<?php

namespace Database\Seeders;

use App\Models\CommonQuestion;
use Illuminate\Database\Seeder;

class CommonQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommonQuestion::factory(random_int(10, 20))->create();
    }
}

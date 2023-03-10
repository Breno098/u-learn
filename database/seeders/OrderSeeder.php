<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Order;
use App\Models\Student;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::factory(30)
            ->state(function() {
                return [
                    'student_id' => Student::all()->random()->id,
                    'campaign_id' => Campaign::all()->random()->id,
                ];
            })
            ->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::factory(random_int(10, 20))
            ->state(function() {
                return [
                    'campaign_id' => Campaign::all()->random()->id,
                ];
            })
            ->create();
    }
}

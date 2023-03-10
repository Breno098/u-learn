<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            GroupSeeder::class,
            StudentSeeder::class,
            SectionSeeder::class,
            CategorySeeder::class,
            GenreSeeder::class,
            ContentSeeder::class,
            LiveEventSeeder::class,
            MeetingSeeder::class,
            JobVacancySeeder::class,
            PartnerSeeder::class,
            CampaignSeeder::class,
            CommonQuestionSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            ItemSeeder::class,
            QuizzSeeder::class
        ]);
    }
}

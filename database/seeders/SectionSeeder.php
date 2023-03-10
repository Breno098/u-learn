<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->fixedSections();

        Section::factory(random_int(10, 20))->create();
    }

    /**
     * Run the fixed sections.
     *
     * @return void
     */
    private function fixedSections()
    {
        Section::create([
            'name' => 'Lançamento',
            'can_delete' => false
        ]);

        Section::create([
            'name' => 'Destaques',
            'can_delete' => false
        ]);

        Section::create([
            'name' => 'Mais vistos na semana',
            'can_delete' => false
        ]);

        Section::create([
            'name' => 'Indicados para você',
            'can_delete' => false
        ]);

        Section::create([
            'name' => 'Minha lista',
            'can_delete' => false
        ]);

        Section::create([
            'name' => 'TOP 10',
            'can_delete' => false
        ]);

        Section::create([
            'name' => 'Continuar assistindo',
            'can_delete' => false
        ]);

        Section::create([
            'name' => 'Últimos dias para assistir esse título',
            'can_delete' => false
        ]);
    }
}

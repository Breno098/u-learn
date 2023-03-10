<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Genre::factory()
            ->count(26)
            ->state(new Sequence(
                ['name' => 'Ação'],
                ['name' => 'Aventura'],
                ['name' => 'Cinema de arte'],
                ['name' => 'Chanchada'],
                ['name' => 'Comédia'],
                ['name' => 'Comédia de ação'],
                ['name' => 'Comédia de terror'],
                ['name' => 'Comédia dramática'],
                ['name' => 'Comédia romântica'],
                ['name' => 'Dança'],
                ['name' => 'Documentário'],
                ['name' => 'Docuficção'],
                ['name' => 'Drama'],
                ['name' => 'Espionagem'],
                ['name' => 'Faroeste'],
                ['name' => 'Fantasia'],
                ['name' => 'Fantasia científica'],
                ['name' => 'Ficção científica'],
                ['name' => 'Filmes com truques'],
                ['name' => 'Filmes de guerra'],
                ['name' => 'Mistério'],
                ['name' => 'Musical'],
                ['name' => 'Filme policial'],
                ['name' => 'Romance'],
                ['name' => 'Suspense'],
                ['name' => 'Terror']
    ))
            ->create();
    }
}

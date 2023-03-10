<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->commonPermission('conteúdos', 'content');
        $this->commonPermission('campanhas', 'campaign');
        $this->commonPermission('categorias', 'category');
        $this->commonPermission('usuários', 'user');
        $this->commonPermission('grupos', 'group');
        $this->commonPermission('brindes', 'item');
        $this->commonPermission('seções', 'section');
        $this->commonPermission('vagas', 'job_vacancy');
        $this->commonPermission('parceiros', 'partner');
        $this->commonPermission('perguntas frenquêntes', 'common_question');
        $this->commonPermission('encontros', 'meeting');
        $this->commonPermission('eventos ao vivo', 'live_event');
        $this->commonPermission('quizz', 'quizz');
        $this->commonPermission('grupos de permissão', 'role');
        $this->commonPermission('alunos', 'student');

        Permission::create(['label' => "Agenda", "key" => "schedule_index", "level" => 'Eventos']);
        Permission::create(['label' => "Notificações", "key" => "notification_index", "level" => 'Eventos']);

        Permission::create(['label' => "Financeiro", "key" => "financial_index", "level" => 'Administrativo']);
        Permission::create(['label' => "Comercial", "key" => "commercial_index", "level" => 'Administrativo']);

        Permission::create(['label' => "Cancelar pedido", "key" => "order_cancel", "level" => 'Pedido']);
        Permission::create(['label' => "Visualizar situação do pedido", "key" => "order_situation_show", "level" => 'Pedido']);
        Permission::create(['label' => "Editar situação do pedido", "key" => "order_situation_update", "level" => 'Pedido']);
    }


    private function commonPermission(string $name, string $partOfKey): void
    {
        Permission::create(['label' => "Listar {$name}", "key" => "{$partOfKey}_index", "level" => Str::ucfirst($name)]);
        Permission::create(['label' => "Cadastrar {$name}", "key" => "{$partOfKey}_store", "level" => Str::ucfirst($name)]);
        Permission::create(["label" => "Editar {$name}", "key" => "{$partOfKey}_update", "level" => Str::ucfirst($name)]);
        Permission::create(["label" => "Visualizar {$name}", "key" => "{$partOfKey}_show", "level" => Str::ucfirst($name)]);
        Permission::create(["label" => "Excluir {$name}", "key" => "{$partOfKey}_destroy", "level" => Str::ucfirst($name)]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('number')->default(1);
            $table->time('duration')->nullable();
            $table->string('wallpaper')->nullable();
            $table->string('video')->nullable();
            $table->enum('release_type', ['livre', 'data', 'data_apos_criacao_curso', 'conclusao_aula_anterior'])->default('livre');
            $table->boolean('can_comments')->default(true);
            $table->foreignId('module_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}

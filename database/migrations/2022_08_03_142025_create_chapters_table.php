<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('number')->default(1);
            $table->time('duration')->nullable();
            $table->text('cast')->nullable()->comment('Elenco');
            $table->text('direction')->nullable();
            $table->string('image')->nullable();

            $table->enum('main_player', ['vimeo', 'sambatech'])->default('vimeo');
            $table->string('vimeo_link')->nullable();
            $table->string('vimeo_embed')->nullable();
            $table->string('sambatech_link')->nullable();
            $table->string('sambatech_embed')->nullable();

            $table->nullableMorphs('chapterable');

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
        Schema::dropIfExists('chapters');
    }
}

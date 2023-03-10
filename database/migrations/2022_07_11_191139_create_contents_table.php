<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('launch_start_at');
            $table->date('launch_end_at');
            $table->enum('author', ['confeccao_propria', 'outro']);
            $table->string('responsible_for_production')->nullable();
            $table->enum('production_type', ['licenciamento', 'parceria'])->nullable();
            $table->date('end_at')->nullable();
            $table->boolean('has_seasons')->default(false);
            $table->integer('number_of_seasons')->nullable();
            $table->boolean('highlight')->default(false);
            $table->foreignId('category_id')->nullable()->constrained();
            $table->integer('top_position')->nullable();
            $table->bigInteger('access_count')->default(0);
            $table->string('main_image')->nullable();
            $table->string('secondary_image')->nullable();
            $table->string('descritiption_image')->nullable();
            $table->string('screensaver_image')->nullable();
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
        Schema::dropIfExists('contents');
    }
}

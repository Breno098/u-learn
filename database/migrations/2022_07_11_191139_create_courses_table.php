<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('descritiption')->nullable();
            $table->enum('level', ['iniciante', 'intermediario', 'avancado'])->nullable();
            $table->timestamp('duration')->nullable();
            $table->string('video')->nullable();
            $table->text('url_sale')->nullable();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('certificate_id')->nullable()->constrained();
            $table->string('wallpaper_image')->nullable();
            $table->string('tumb_image')->nullable();
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
        Schema::dropIfExists('courses');
    }
}

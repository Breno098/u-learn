<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_elements', function (Blueprint $table) {
            $table->id();
            $table->integer('coordinate_x')->default(0);
            $table->integer('coordinate_y')->default(0);
            $table->text('html')->nullable();
            $table->foreignId('certificate_id')->constrained();
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
        Schema::dropIfExists('certificate_elements');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_installments', function (Blueprint $table) {
            $table->id();
            $table->decimal('value')->default(0);
            $table->integer('order');
            $table->foreignId('product_id')->constrained();
            $table->enum('to', ['boleto', 'credit_card'])->default('boleto');
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
        Schema::dropIfExists('product_installments');
    }
}

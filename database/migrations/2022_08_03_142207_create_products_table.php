<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('url_sale')->nullable();
            $table->longText('terms_acceptance')->nullable();
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('credit_card_accept')->default(true);
            $table->decimal('credit_card_value')->default(0);
            $table->integer('credit_card_installments')->default(1);
            $table->boolean('boleto_accept')->default(true);
            $table->decimal('boleto_value')->default(0);
            $table->integer('boleto_installments')->default(1);
            $table->boolean('pix_accept')->default(true);
            $table->decimal('pix_value')->default(0);
            $table->string('checkout_code')->nullable();
            $table->string('checkout_link')->nullable();
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
        Schema::dropIfExists('products');
    }
}

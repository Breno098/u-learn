<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained();
            $table->foreignId('campaign_id')->nullable()->constrained();
            $table->enum('status', ['adimplente', 'cancelado', 'nao_renovou', 'vencido'])->nullable();
            $table->decimal('payment_value')->default(0);
            $table->enum('payment_method', ['boleto', 'cartao', 'pix'])->nullable();
            $table->boolean('term_accepted')->default(false);
            $table->longText('text_terms_acceptance')->nullable();
            $table->enum('registration_type', ['anual', 'mensal'])->default('anual');
            $table->date('hiring_start_at')->nullable();
            $table->date('hiring_end_at')->nullable();
            $table->date('canceled_at')->nullable();
            $table->string('invoices')->nullable();
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
        Schema::dropIfExists('orders');
    }
}

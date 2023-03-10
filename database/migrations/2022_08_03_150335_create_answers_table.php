<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->string('answer')->nullable();
            $table->string('correct_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->foreignId('question_id')->constrained();
            $table->foreignId('alternative_selected_id')->nullable()->constrained('alternatives');
            $table->foreignId('order_id')->constrained();
            $table->foreignId('quizz_id')->constrained('quizzes');
            $table->integer('attemp')->default(1);
            $table->longText('answer_text')->nullable();
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
        Schema::dropIfExists('answers');
    }
}

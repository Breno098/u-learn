<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('answer_type', ['aberta', 'fechada', 'imagem', 'video', 'audio']);
            $table->foreignId('quizz_id')->constrained('quizzes');
            $table->integer('number')->default(1);
            $table->string('video')->nullable();
            $table->string('audio')->nullable();
            $table->string('image')->nullable();
            $table->boolean('has_video')->default(false)->nullable();
            $table->boolean('has_audio')->default(false)->nullable();
            $table->boolean('has_image')->default(false)->nullable();
            $table->timestamps();

            /**
             * @todo
             * Se a questão poderá ter apenas um anexo:
             *  - Criar campos [attachment_link] e [attachment_type];
             *  - [attachment_type] poderá receber os valores: null, video, image, audio
             *
             * Se a questão poderá ter todos os tipos de anexo:
             *  - Criar campos [attachment_types], [attachment_video_link], [attachment_image_link] e [attachment_audio_link]
             *  - [attachment_types] deverá ser o campo que informa quais foram selecionados: video e/ou image e/ou audio
             */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}

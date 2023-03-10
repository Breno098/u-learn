<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('tags')->nullable();
            $table->string('image')->nullable();
            $table->enum('type', ['agendamento', 'grupo', 'extra', 'imersao', 'individual'])->default('individual');
            $table->integer('number_of_students')->nullable();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('student_id')->nullable()->constrained();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('live_offer')->default(false);
            $table->string('name_offer')->nullable();
            $table->text('description_offer')->nullable();
            $table->string('embed_offer')->nullable();
            $table->boolean('has_link_with_content')->default(false)->nullable();
            $table->foreignId('content_id')->nullable()->constrained();
            $table->nullableMorphs('linkable');
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
        Schema::dropIfExists('meetings');
    }
}

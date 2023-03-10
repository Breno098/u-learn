<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('event_type')->default('live');
            $table->enum('type', ['individual', 'grupo'])->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('embed')->nullable();
            $table->foreignId('responsible_id')->nullable()->constrained('users');
            $table->integer('number_of_students')->nullable();
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
        Schema::dropIfExists('live_events');
    }
}

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
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->uuidMorphs('commentable');
            $table->text('body');
            $table->uuid('author_id');
            $table->uuid('in_reply_to_id')->nullable();
            $table->uuid('root_comm_id')->nullable();
            $table->enum('type', ['comment', 'note']);
            $table->uuid('serie_id')->nullable();

            $table->foreign('serie_id')->references('id')->on('series');
            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();

            $table->timestamps();
        });
        Schema::table('comments', function (Blueprint $table)
        {
            $table->foreign('in_reply_to_id')->references('id')->on('comments');
            $table->foreign('root_comm_id')->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};

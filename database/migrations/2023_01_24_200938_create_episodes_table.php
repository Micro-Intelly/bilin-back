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
        Schema::create('episodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('path');
            $table->enum('type', ['video', 'podcast']);
            $table->uuid('section_id');
            $table->uuid('user_id');
            $table->uuid('serie_id');

            $table->foreign('section_id')->references('id')->on('sections')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('serie_id')->references('id')->on('series');
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
        Schema::dropIfExists('episodes');
    }
};

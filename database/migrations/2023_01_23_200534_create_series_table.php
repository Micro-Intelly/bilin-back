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
        Schema::create('series', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->default('public/image/application/defaultImage.png');
            $table->enum('type', ['video', 'podcast']);
            $table->enum('access', ['public', 'registered','org'])->default('public');
            $table->enum('level', ['basic', 'intermediate','advanced'])->default('basic');
            $table->uuid('author_id');
            $table->uuid('language_id');
            $table->uuid('organization_id')->nullable();

            $table->foreign('author_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('organization_id')->references('id')->on('organizations');
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
        Schema::dropIfExists('series');
    }
};

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
        Schema::create('tests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->uuid('series_id')->nullable();
            $table->enum('access', ['public', 'registered','org']);
            $table->enum('level', ['basic', 'intermediate','advanced']);
            $table->uuid('language_id');
            $table->uuid('user_id');
            $table->uuid('organization_id')->nullable();

            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('series_id')->references('id')->on('series')->cascadeOnDelete();
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
        Schema::dropIfExists('tests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('strength')->default(50);
            $table->integer('home_advantage')->default(10);
            $table->integer('away_disadvantage')->default(5);
            $table->integer('goalkeeper_index')->default(50);
            $table->integer('striker_index')->default(50);
            $table->integer('supporter_strength')->default(25);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
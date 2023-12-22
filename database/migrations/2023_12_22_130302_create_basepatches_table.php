<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('basepatches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('build')->unique();
            $table->char('ages_hash', 32);
            $table->char('seasons_hash', 32);

            $table->index('build');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basepatches');
    }
};

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
        Schema::table('seeds', function (Blueprint $table) {
            $table->char('hash', 10)->nullable()->collation('utf8_bin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seeds', function (Blueprint $table) {
            $table->char('hash', 10)->nullable()->collation('utf8_unicode_ci')->change();
        });
    }
};

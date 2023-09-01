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
        Schema::table('gsic_teams', function (Blueprint $table) {
            //
            $table->string('referral')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gsic_teams', function (Blueprint $table) {
            //
            $table->dropColumn('referral');
        });
    }
};

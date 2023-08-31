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
        Schema::table('bcc_users', function (Blueprint $table) {
            $table->string('stream')->nullable()->change();
            $table->renameColumn('stream', 'referral');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bcc_users', function (Blueprint $table) {
            $table->string('referral')->change();
            $table->renameColumn('referral', 'stream');
        });
    }
};

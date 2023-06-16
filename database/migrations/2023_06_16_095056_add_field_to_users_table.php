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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->after('email')->default('USER');
            $table->string('phone')->after('email');
            $table->string('username')->after('email')->unique();
            $table->string('university')->after('email');
            $table->string('major')->after('university');
            $table->string('batch')->after('major');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('phone');
            $table->dropColumn('username');
            $table->dropColumn('university');
            $table->dropColumn('major');
            $table->dropColumn('batch');
        });
    }
};

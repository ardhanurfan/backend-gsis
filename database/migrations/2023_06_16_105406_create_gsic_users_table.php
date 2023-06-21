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
        Schema::create('gsic_users', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('team_id')->constrained('gsic_teams')->onDelete('cascade')->onUpdate('cascade');
            $table->string('ktm_url');
            $table->string('ss_follow_url');
            $table->string('ss_poster_url');
            $table->string('approve_ktm')->default('WAITING');
            $table->string('approve_follow')->default('WAITING');
            $table->string('approve_poster')->default('WAITING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gsic_users');
    }
};

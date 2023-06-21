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
        Schema::create('gsic_teams', function (Blueprint $table) {
            $table->id();
            $table->string('team_name')->unique();
            $table->foreignId('leader_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('payment_url');
            $table->string('status')->default('ACTIVE');
            $table->string('approve_payment')->default('WAITING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gsic_teams');
    }
};

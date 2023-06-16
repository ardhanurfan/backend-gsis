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
        Schema::create('bcc_users', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('team_id')->nullable()->constrained('bcc_teams')->onDelete('cascade')->onUpdate('cascade');
            $table->string('status')->default('ACTIVE');
            $table->string('papper_url')->nullable();
            $table->string('stream');
            $table->string('ktm_url');
            $table->string('ss_follow_url');
            $table->string('ss_poster_url');
            $table->string('payment_url');
            $table->timestamp('approve_ktm')->nullable();
            $table->timestamp('approve_follow')->nullable();
            $table->timestamp('approve_poster')->nullable();
            $table->timestamp('approve_payment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bcc_users');
    }
};

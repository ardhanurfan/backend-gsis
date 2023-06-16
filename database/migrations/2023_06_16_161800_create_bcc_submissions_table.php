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
        Schema::create('bcc_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('bcc_teams')->onDelete('cascade')->onUpdate('cascade');
            $table->string('url');
            $table->integer('round');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bcc_submissions');
    }
};

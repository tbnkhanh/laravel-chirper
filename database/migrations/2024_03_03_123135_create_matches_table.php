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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained("tournaments")->cascadeOnDelete();
            $table->int('round_number');
            $table->int('match_number');
            $table->foreignId('team1_id')->constrained("teams")->cascadeOnDelete();
            $table->foreignId('team2_id')->constrained("teams")->cascadeOnDelete();
            $table->foreignId('winner_team_id')->constrained("teams")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'tournament_id',
        'round_number',
        'match_number',
        'team1_id',
        'team2_id',
        'winner_team_id',
        'start_date',
        'end_date',
        'status',
        'created_at',
        'updated_at'
    ];

    public function team1()
    {
        return $this->belongsTo(Team::class);
    }

    public function team2()
    {
        return $this->belongsTo(Team::class);
    }

    public function winnerTeam()
    {
        return $this->belongsTo(Team::class);
    }
}

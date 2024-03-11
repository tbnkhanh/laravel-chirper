<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tournament_name',
        'tournament_description',
        'game_played',
        'is_generate_bracket',
        'team_number',
        'team_size',
        'start_date',
        'end_date',
    ];
}

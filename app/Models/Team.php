<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'team_name',
        'tournament_id'
    ];
    public function players()
    {
        return $this->hasMany(Player::class);
    }
}

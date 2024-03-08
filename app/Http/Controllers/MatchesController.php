<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;

class MatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($tournamentId, Request $request)
    {
        $tournament = Tournament::find($tournamentId);
        $teams = Team::where('tournament_id', $tournamentId)->get();
        $array_teams_id = [];
        $tournament->team_number = count($teams);
        $tournament->save();
        foreach ($teams as $key => $team) {
            array_push($array_teams_id, $team->id);
        }
        for ($i = 1; $i <= (count($teams) / 2); $i++) {
            $randomIndex = array_rand($array_teams_id);
            $randomElement = $array_teams_id[$randomIndex];
            unset($array[$randomIndex]);
            
        }
        return redirect(route('tournament.show', $tournamentId));
    }

    /**
     * Display the specified resource.
     */
    public function show(Matches $matches)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matches $matches)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matches $matches)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matches $matches)
    {
        //
    }
}

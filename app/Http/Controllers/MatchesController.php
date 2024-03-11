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
            $array_id_added = [];
            for ($j = 1; $j <= 2; $j++) {
                $randomIndex = array_rand($array_teams_id);
                $randomElement = $array_teams_id[$randomIndex];
                array_push($array_id_added, $randomElement);
                unset($array_teams_id[$randomIndex]);
            }
            Matches::create(['tournament_id' => $tournamentId, 'round_number' => 1, 'match_number' => $i, 'team1_id' => $array_id_added[0], 'team2_id' => $array_id_added[1]]);
        }
        $tournament->is_generate_bracket = 1;
        $tournament->save();
        return redirect(route('tournament.show', $tournamentId));
    }

    public function selectWinningTeam($matchId, Request $request)
    {
        //update winner team
        $winningTeamId = $request->input('winning_team');
        $match = Matches::find($matchId);
        if($match->winner_team_id === null){
            $match->winner_team_id = $winningTeamId;
            $match->save();    
        }

        //create next round
        $current_match_number = $match->match_number;
        $next_round_number = $match->round_number + 1;
        $next_match_number = 0;
        if ($current_match_number % 2 === 0) {
            $next_match_number = $current_match_number / 2;
        } else {
            $next_match_number = ($current_match_number + 1) / 2;
        }

        $match_for_next_round = Matches::where([
            ['round_number', '=', $next_round_number],
            ['match_number', '=', $next_match_number]
        ])->first();

        //match for next round already exist
        if ($match_for_next_round !== null) {
            if ($match_for_next_round->team1_id === null) {
                $match_for_next_round->team1_id = $winningTeamId;
                $match_for_next_round->save();
            }
            if ($match_for_next_round -> team2_id === null) {
                $match_for_next_round->team2_id = $winningTeamId;
                $match_for_next_round->save();
            }
        } else {
        //create new match for next round
            $match_new_for_next_round = Matches::create([
                'tournament_id' => $match->tournament_id,
                'round_number' => $next_round_number,
                'match_number' => $next_match_number,
                'team1_id' => null,
                'team2_id' => null
            ]);
            if($current_match_number % 2 !== 0){
                $match_new_for_next_round -> team1_id = $winningTeamId;
                $match_new_for_next_round -> save();
            }else{
                $match_new_for_next_round -> team2_id = $winningTeamId;
                $match_new_for_next_round -> save();    
            }
        }
        return back();
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

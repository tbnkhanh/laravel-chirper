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
        $team_number = count($teams);
        $tournament->team_number = $team_number;
        $tournament->save();

        $players = range(1, $team_number);
        $numberOfRounds = log($team_number, 2);

        for ($i = 0; $i < $numberOfRounds - 1; $i++) {
            $out = array();
            $splice = pow(2, $i);

            while (count($players) > 0) {
                $out = array_merge($out, array_splice($players, 0, $splice));
                $out = array_merge($out, array_splice($players, -$splice));
            }

            $players = $out;
        }
        // 1 2 3 4 5 6 7 8

        $j = 1;
        for ($i = 0; $i < $team_number; $i++) {
            $team1 = Team::where([
                ['tournament_id', '=', $tournamentId],
                ['seed', '=', $players[$i]]
            ])->first();
            $team2 = Team::where([
                ['tournament_id', '=', $tournamentId],
                ['seed', '=', $players[++$i]]
            ])->first();
            Matches::create(['tournament_id' => $tournamentId, 'round_number' => 1, 'match_number' => $j++, 'team1_id' => $team1->id, 'team2_id' => $team2->id]);
        }

        $tournament->is_generate_bracket = 1;
        $tournament->save();
        return redirect(route('tournament.show', $tournamentId));
    }




    public function selectWinningTeam($matchId, Request $request)
    {
        //update winner team to match
        $winningTeamId = $request->input('winning_team');
        $match = Matches::find($matchId);
        if ($match->winner_team_id === null) {
            $match->winner_team_id = $winningTeamId;
            $match->save();
        }

        //update winner team to tournament
        $tournament = Tournament::find($match->tournament_id);
        $round = intval(log($tournament->team_number, 2));;
        $teamWinner = Team::find($winningTeamId);
        if ($match->round_number === $round) {
            $tournament->winner_team = $teamWinner->team_name;
            $tournament->save();
        } else{
            $current_match_number = $match->match_number;
            $next_round_number = $match->round_number + 1;
            $next_match_number = 0;
            if ($current_match_number % 2 === 0) {
                $next_match_number = $current_match_number / 2;
            } else {
                $next_match_number = ($current_match_number + 1) / 2;
            }
            
            $match_for_next_round = Matches::where([
                ['tournament_id', '=', $tournament->id],
                ['round_number', '=', $next_round_number],
                ['match_number', '=', $next_match_number]
            ])->first();
    
            //match for next round already exist
            if ($match_for_next_round !== null) {
                if ($match_for_next_round->team1_id === null) {
                    $match_for_next_round->team1_id = $winningTeamId;
                    $match_for_next_round->save();
                }
                if ($match_for_next_round->team2_id === null) {
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
                if ($current_match_number % 2 !== 0) {
                    $match_new_for_next_round->team1_id = $winningTeamId;
                    $match_new_for_next_round->save();
                } else {
                    $match_new_for_next_round->team2_id = $winningTeamId;
                    $match_new_for_next_round->save();
                }
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

    // 1 vs 16
    // 8 vs 9
    // 4 vs 13
    // 5 vs 12
    // 2 vs 15
    // 7 vs 10
    // 3 vs 14
    // 6 vs 11

    // 1 vs 32
    // 16 vs 17

    // 8 vs 25
    // 9 vs 24

    // 4 vs 29
    // 13 vs 20         

    // 5 vs 28
    // 12 vs 21

    // 2 vs 31
    // 15 vs 18

    // 7 vs 26
    // 10 vs 23

    // 3 vs 30
    // 14 vs 19

    // 6 vs 27
    // 11 vs 22
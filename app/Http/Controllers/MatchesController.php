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
        // add loser bracket, ko gap nhau

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
            Matches::create(['tournament_id' => $tournamentId, 'round_number' => 1, 'match_number' => $j++, 'team1_id' => $team1->id, 'team2_id' => $team2->id, 'type_bracket' => "winner"]);
        }

        $tournament->is_generate_bracket = 1;
        $tournament->save();
        return redirect(route('tournament.show', $tournamentId));
    }


    private function matchForNextRound($tournamentId, $roundNumber, $matchNumber, $typeBracket, $teamId, $currentMatchNumber)
    {
        $match_for_next_round = Matches::where([
            ['tournament_id', '=', $tournamentId],
            ['round_number', '=', $roundNumber],
            ['match_number', '=', $matchNumber],
            ['type_bracket', '=', $typeBracket]
        ])->first();

        //match for next round already exist
        if ($match_for_next_round !== null) {
            if ($match_for_next_round->team1_id === null) {
                $match_for_next_round->team1_id = $teamId;
                $match_for_next_round->save();
            }
            if ($match_for_next_round->team2_id === null) {
                $match_for_next_round->team2_id = $teamId;
                $match_for_next_round->save();
            }
        } else {
            //create new match for next round
            $match_new_for_next_round = Matches::create([
                'tournament_id' => $tournamentId,
                'round_number' => $roundNumber,
                'match_number' => $matchNumber,
                'type_bracket' => $typeBracket,
                'team1_id' => null,
                'team2_id' => null
            ]);
            if ($currentMatchNumber % 2 !== 0) {
                $match_new_for_next_round->team1_id = $teamId;
                $match_new_for_next_round->save();
            } else {
                $match_new_for_next_round->team2_id = $teamId;
                $match_new_for_next_round->save();
            }
        }
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
        $tournament = Tournament::find($match->tournament_id);
        //number round of loser bracket
        $soMu = log($tournament->team_number, 2);
        $roundNumberLoser = ($soMu - 1) * 2;

        //number round of winner bracket
        $roundNumberWinner = intval(log($tournament->team_number, 2));;



        //handle team for loser bracket 
        if ($match->type_bracket == 'loser') {

            //neu round hien tai la round cuoi => push len winner bracket 
            if ($match->round_number == $roundNumberLoser) {
                $this->matchForNextRound($tournament->id, $roundNumberWinner + 1, $match->match_number, 'winner', $winningTeamId, $match->match_number);
            } else {
                // add winner team to next round
                $current_match_number = $match->match_number;
                if ($match->round_number % 2 !== 0) {
                    $this->matchForNextRound($tournament->id, $match->round_number + 1, $match->match_number, 'loser', $winningTeamId, $current_match_number);
                } else {
                    $next_match_number = ($current_match_number % 2 === 0) ? $current_match_number / 2 : ($current_match_number + 1) / 2;
                    $this->matchForNextRound($tournament->id, $match->round_number + 1, $next_match_number, 'loser', $winningTeamId, $current_match_number);
                }
            }
        }

        //handle team for winner bracket
        if ($match->type_bracket == 'winner') {

            //add loser team to next round
            $loserTeamId = ($match->team1_id != $winningTeamId) ? $match->team1_id : $match->team2_id;
            $current_match_number = $match->match_number;
            $next_round_number = $match->round_number;
            if ($match->round_number <= $roundNumberWinner) {
                if ($match->round_number == 1) {
                    $next_match_number = ($current_match_number % 2 === 0) ? $current_match_number / 2 : ($current_match_number + 1) / 2;
                } else if ($match->round_number == 2) {
                    $next_match_number = ($tournament->team_number / 4) - ($match->match_number) + 1;
                } else if ($match->round_number > 2 && $match->round_number <= $roundNumberWinner) {
                    //add loser team to loser bracket: round 3-4 4-6 5-8 6-10
                    $init_round_index = 3;
                    $next_round_number = 4;
                    for ($x = 1; $x <= ($match->round_number - $init_round_index); $x++) {
                        $next_round_number = $next_round_number + 2;
                    }
                    $next_match_number = $match->match_number;
                }
                $this->matchForNextRound($tournament->id, $next_round_number, $next_match_number, 'loser', $loserTeamId, $current_match_number);
            }


            //add winner team to next round
            $teamWinner = Team::find($winningTeamId);
            //neu round hien tai la round cuoi, add winner cho tournament
            if ($match->round_number === $roundNumberWinner + 1 || $match->round_number === $roundNumberWinner + 2) {

                if ($match->round_number === $roundNumberWinner + 1) {
                    $previous_match = Matches::where([
                        ['tournament_id', '=', $tournament->id],
                        ['round_number', '=', $match->round_number - 1],
                        ['type_bracket', '=', 'winner']
                    ])->first();
                    if ($previous_match->winner_team_id == $winningTeamId) {
                        $tournament->winner_team = $teamWinner->team_name;
                        $tournament->save();
                    } else {
                        $this->matchForNextRound($tournament->id, $match->round_number + 1, $match->match_number, 'winner', $winningTeamId, $current_match_number);
                        $this->matchForNextRound($tournament->id, $match->round_number + 1, $match->match_number, 'winner', $loserTeamId, $current_match_number);
                    }
                }
                if ($match->round_number === $roundNumberWinner + 2) {
                    $tournament->winner_team = $teamWinner->team_name;
                    $tournament->save();
                }
            }
            // neu ko thi add winner vao next round
            else {
                $current_match_number = $match->match_number;
                $next_round_number = $match->round_number + 1;
                $next_match_number = ($current_match_number % 2 === 0) ? $current_match_number / 2 : ($current_match_number + 1) / 2;
                $this->matchForNextRound($tournament->id, $next_round_number, $next_match_number, 'winner', $winningTeamId, $current_match_number);
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
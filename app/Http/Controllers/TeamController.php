<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $tournament_id = $id;
        return view('team.create', compact('tournament_id'));
    }

    private function validatePlayers(Request $request)
    {
        $data = $request->validate([
            'team_name' => 'required|string|min:3',
            'player1_email' => 'required|email',
            'in_game1' => 'required',
            'player2_email' => 'required|email',
            'in_game2' => 'required',
        ]);

        if ($data['player1_email'] === $data['player2_email']) {
            throw ValidationException::withMessages([
                'duplicate_email' => 'Player1 email and Player2 email are duplicate'
            ]);
        }

        $user1 = User::where('email', $data['player1_email'])->first();
        $user2 = User::where('email', $data['player2_email'])->first();
        if (is_null($user1)) {
            throw ValidationException::withMessages([
                'email1_invalid' => "Email for Player1 doesn't exists"
            ]);
        }
        if (is_null($user2)) {
            throw ValidationException::withMessages([
                'email2_invalid' => "Email Player2 doesn't exists"
            ]);
        }

        $usersRemain = User::where('user_type', 'player')
            ->whereDoesntHave('player')
            ->pluck('email')
            ->toArray();
        $player1_email_valide = in_array($data['player1_email'], $usersRemain);
        $player2_email_valide  = in_array($data['player2_email'], $usersRemain);
        $isValid = $player1_email_valide && $player2_email_valide;
        return [
            'data' => $data,
            'isValid' => $isValid,
            'user1' => $user1,
            'user2' => $user2,
        ];
    }


    public function store($tournamentId, Request $request)
    {
        $response = $this->validatePlayers($request);

        if ($response['isValid']) {
            $team = Team::create([
                'tournament_id' => $tournamentId,
                'team_name' => $response['data']['team_name'],
            ]);
            Player::insert([
                ['team_id' => $team->id, 'in_game_name' => $response['data']['in_game1'], 'user_id' => $response['user1']->id],
                ['team_id' => $team->id, 'in_game_name' => $response['data']['in_game2'], 'user_id' => $response['user2']->id]
            ]);
        } else {
            throw ValidationException::withMessages([
                'users_in_other_team' => "Player1 or Player2 are in other teams"
            ]);
        }
        return redirect(route('tournament.show', $tournamentId));
    }


    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $teamsWithPlayers = Team::with('players.user')
            ->where('id', $team->id)
            ->first();
        return view('team.edit', compact('teamsWithPlayers'));
    }

    /**
     * Update the specified resource in storage.    
     */
    public function update(Request $request, $id)
    {
        $response = $this->validatePlayers($request);

        if ($response['isValid']) {
            $team = Team::where('id', $id)->update(['team_name' => $response['data']['team_name']]);
            Player::where('id', $response['user1']->id)->update(['team_name' => $response['data']['team_name']]);
            
        } else {
            throw ValidationException::withMessages([
                'users_in_other_team' => "Player1 or Player2 are in other teams"
            ]);
        }
        return back();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($tournamentId, $teamId)
    {
        DB::table('teams')->where('id', $teamId)->delete();
        return redirect(route('tournament.show', $tournamentId));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        $tournament = Tournament::find($id);
        return view('team.create', compact('tournament'));
    }

    private function validatePlayers(Request $request)
    {
        // $data = $request->validate([
        //     'team_name' => 'required|string|min:3',
        //     'player1_email' => 'nullable|email',
        //     'in_game1' => 'nullable',
        //     'player2_email' => 'nullable|email',
        //     'in_game2' => 'nullable',
        // ]); 
        $data = $request->validate([
            'team_name' => 'required|string|min:3',
            'player_email.*' => 'required|email',
            'in_game.*' => 'required|string',
        ]);
        // dd($data);

        $usersRemain = User::where('user_type', 'player')
            ->whereDoesntHave('player')
            ->pluck('email')
            ->toArray();
        $users=[];
        $emails = $request->input('player_email');
        $checkedEmails = [];
        foreach ($emails as $email) {
            if (in_array($email, $checkedEmails)) {
                throw ValidationException::withMessages([
                    'email_duplicate' => "Email $email is duplicate"
                ]);
            }

            $user = User::where('email', $email)->first();
            if (is_null($user)) {
                throw ValidationException::withMessages([
                    'email_invalid' => "Email $email doesn't exist"
                ]);
            }

            if (!in_array($email, $usersRemain)) {
                throw ValidationException::withMessages([
                    'email_invalid' => "User has $email already in other team"
                ]);
            }
            array_push($users, $user->id);
            array_push($checkedEmails, $email);
        }

        return [
            'data' => $data,
            'users_id' => $users
        ];

        //  $player1_email_valide = in_array($data['player1_email'], $usersRemain);
        // $player2_email_valide  = in_array($data['player2_email'], $usersRemain);    
        // $isValid = $player1_email_valide && $player2_email_valide;

        // if ($data['player1_email'] !== null && $data['player1_email'] === $data['player2_email']) {
        //     throw ValidationException::withMessages([
        //         'duplicate_email' => 'Player1 email and Player2 email are duplicate'
        //     ]);
        // }

        // if($request->input('type') === 'update'){
        //     $id = $request->input('id');    
        //     $teamsWithPlayers = Team::with('players.user')
        //     ->where('id', $id)
        //     ->first();
        //     dd($teamsWithPlayers);
        //     if($teamsWithPlayers->players[0]->user['email'] === $data['player1_email']){
        //         $player1_update='true';
        //     }
        //     if($teamsWithPlayers->players[1]->user['email'] === $data['player2_email']){
        //         $player2_update='true';
        //     }
        // }


    }


    public function store($tournamentId, Request $request)
    {
        // dd($request->all());
        $response = $this->validatePlayers($request);
        // dd($response);
        $team = Team::create([
            'tournament_id' => $tournamentId,
            'team_name' => $response['data']['team_name'],
        ]);
        $data = [];
        for ($i=1 ; $i <= count($response['data']['in_game']) ; $i++) { 
            array_push($data, [ 'team_id' => $team->id,'in_game_name' =>$response['data']['in_game'][$i], 'user_id' => $response['users_id'][$i-1] ]);
        }
        // dd($data);
        Player::insert($data);
        return redirect(route('tournament.show', $tournamentId));
    }

    /**
     * Update the specified resource in storage.    
     */
    public function update($id, Request $request)
    {
        // dd($teamsWithPlayers);
        $request->request->add(['type' => 'update']);
        $request->request->add(['id' => $id]);
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
     * Remove the specified resource from storage.
     */
    public function destroy($tournamentId, $teamId)
    {
        DB::table('teams')->where('id', $teamId)->delete();
        return redirect(route('tournament.show', $tournamentId));
    }
}

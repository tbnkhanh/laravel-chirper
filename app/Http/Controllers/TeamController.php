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
        $data = $request->validate([
            'team_name' => 'required|string|min:3',
            'player_email.*' => 'required|email',
            'in_game.*' => 'required|string',
        ]);
        // dd($data);
        $users = [];
        $emails = $request->input('player_email');
        $checkedEmails = [];

        $usersRemain = User::where('user_type', 'player')
            ->whereDoesntHave('player')
            ->pluck('email')
            ->toArray();

        foreach ($emails as $email) {
            if (in_array($email, $checkedEmails)) {
                throw ValidationException::withMessages([
                    'email_duplicate' => "Email $email is duplicate"
                ]);
            }
            $user = User::where('email', $email)->first();
            if (is_null($user)) {
                throw ValidationException::withMessages([
                    'email_not_exist' => "Email $email doesn't exist"
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
        for ($i = 1; $i <= count($response['data']['in_game']); $i++) {
            array_push($data, ['team_id' => $team->id, 'in_game_name' => $response['data']['in_game'][$i], 'user_id' => $response['users_id'][$i - 1]]);
        }
        Player::insert($data);
        return redirect(route('tournament.show', $tournamentId));
    }

    /**
     * Update the specified resource in storage.    
     */
    public function update($tournamentId, $teamId, Request $request)
    {
        $data = $request->validate([
            'team_name' => 'required|string|min:3',
            'player_email.*' => 'required|email',
            'in_game.*' => 'required|string',
        ]);

        $emailsRequest = $request->input('player_email');
        $in_game = $request->input('in_game');
        $checkedEmails = [];
        $emailsNeedUpdate = [];
        $emailsNeedDelete = [];
        
        $emailsCurrent = Team::with('players.user')
            ->where('id', $teamId)
            ->get()
            ->pluck('players.*.user.email')
            ->flatten()
            ->toArray();
        
        foreach ($emailsRequest as $index => $email) {
            if ($email !== $emailsCurrent[$index - 1]) {
                array_push($emailsNeedDelete, $emailsCurrent[$index - 1]);
                unset($emailsCurrent[$index - 1]);
                array_push($emailsNeedUpdate, $email);
            }
        }

        $usersRemain = User::where('user_type', 'player')
            ->whereDoesntHave('player')
            ->pluck('email')
            ->toArray();

        if (count($emailsCurrent) > 0) {
            foreach ($emailsCurrent as $email) {
                $players = Player::whereHas('user', function ($query) use ($email) {
                    $query->where('email', $email);
                })->get()->first();
                // dd($in_game);
                $index = array_search($email, $emailsRequest);
                // dd($index);
                $players->in_game_name = $in_game[$index];
                $players->save();
            }
        }
        if (count($emailsNeedUpdate) > 0) {
            foreach ($emailsNeedUpdate as $index => $email) {
                // dd($index); 
                $indexDelete = $index;
                if (in_array($email, $checkedEmails)) {
                    throw ValidationException::withMessages([
                        'email_duplicate' => "Email $email is duplicate"
                    ]);
                }
                
                $user = User::where('email', $email)->first();
                if (is_null($user)) {
                    throw ValidationException::withMessages([
                        'email_not_exist' => "Email $email doesn't exist"
                    ]);
                }
                if (!in_array($email, $usersRemain)) {
                    throw ValidationException::withMessages([
                        'email_invalid' => "User has $email already in other team"
                    ]);
                } else if ($user) {
                    $index = array_search($email, $emailsRequest);
                    Player::whereHas('user', function ($query) use ($emailsNeedDelete, $indexDelete) {
                        $query->where('email', $emailsNeedDelete[$indexDelete]);
                    })->delete();
                    Player::insert(['user_id' => $user->id, "team_id" => $teamId, "in_game_name" => $in_game[$index]]);
                }
                array_push($checkedEmails, $email);
            }
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
        // dd(count($teamsWithPlayers->players));
        $tournamentSize = Tournament::where('id', $teamsWithPlayers->tournament_id)->get("team_size")->first()->toArray();
        return view('team.edit', compact('teamsWithPlayers', 'tournamentSize'));
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

<?php

namespace App\Http\Controllers;

use App\Models\Matches;
use App\Models\Team;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tournaments = DB::table("tournaments")->get();
        return view('tournament.index', compact('tournaments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tournament_name' => 'required|string|min:3',
            'tournament_description' => 'required|string',
            'game_played' => 'required|string',
            'team_size' => 'required',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
        ]);

        $tournament = new Tournament();
        $tournament->user_id = Auth::id();
        $tournament->tournament_name = $request->input('tournament_name');
        $tournament->tournament_description = $request->input('tournament_description');
        $tournament->game_played = $request->input('game_played');
        $tournament->team_size = $request->input('team_size');
        $tournament->start_date = $request->input('start_date');
        $tournament->end_date = $request->input('end_date');
        $tournament->save();

        return redirect('/tournament/index');
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tournament = Tournament::find($id);
        $teamsWithPlayers = Team::with('players.user')
            ->where('tournament_id', $id)   
            ->get();

        $matches = Matches::with(['team1', 'team2', 'winnerTeam'])
        ->where('tournament_id', $id)
        ->orderBy('round_number')
        ->orderBy('match_number')
        ->get();

        return view('tournament.detail', compact('tournament', 'teamsWithPlayers', 'matches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tournament = Tournament::find($id);
        return view('tournament.edit', compact('tournament'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tournament_name' => 'required|string|min:3',
            'tournament_description' => 'required|string',
            'game_played' => 'required|string',
            'team_size' => 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
        $tournament = Tournament::find($id);
        $tournament->update($data);
        return redirect(route('tournament.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::table('tournaments')->where('id', $id)->delete();
        return redirect(route('tournament.index'));
    }
}

<?php

namespace App\Http\Controllers;

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

        // dd($data['game_mode']);
        // $tournament = Tournament::create([
        //     'user_id' => Auth::id(),
        //     'tournament_name' => $data['tournament_name'],
        //     'tournament_description' => $data['tournament_description'],
        //     'game_played' => $data['game_played'],
        //     'game_mode' => $data['game_mode'],
        //     'start_date' => $data['start_date'],
        //     'end_date' => $data['end_date'],
        // ]);
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
            // ->toArray();
        // dd($teamsWithPlayers);
        return view('tournament.detail', compact('tournament','teamsWithPlayers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tournament = Tournament::find($id);
        // $start_date = Carbon::parse($tournament->start_date);
        // $end_date = Carbon::parse($tournament->end_date);
        // $tournament->start_date = $start_date->toDateString();
        // $tournament->end_date = $end_date->toDateString();
        // dd($tournament);
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

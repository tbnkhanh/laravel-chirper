<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChirpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatchesController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TeamController;
use App\Models\Matches;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->prefix("tournament")->group(function () {
    Route::get('/index', [TournamentController::class, 'index'])->name('tournament.index');
    Route::get('/detail/{id}', [TournamentController::class, 'show'])->name('tournament.show');
    Route::middleware('admin')->group(function () {
        Route::get('/create', function () {
            return view('tournament.create');
        })->name('tournament.create');
        Route::post('/store', [TournamentController::class, 'store'])->name('tournament.store');
        Route::get('/edit/{id}', [TournamentController::class, 'edit'])->name('tournament.edit');
        Route::post('/update/{id}', [TournamentController::class, 'update'])->name('tournament.update');
        Route::delete('/delete/{id}', [TournamentController::class, 'destroy'])->name('tournament.destroy');
    });
});

Route::middleware(['auth', 'admin'])->prefix("team")->group(function () {
    Route::get('/create/{id}', [TeamController::class, 'create'])->name('team.create');
    Route::post('/store/{id}', [TeamController::class, 'store'])->name('team.store');
    Route::get('/edit/{team}', [TeamController::class, 'edit'])->name('team.edit');
    Route::post('/update/{tournamentId}/{teamId}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/delete/{tournamentId}/{teamId}', [TeamController::class, 'destroy'])->name('team.destroy');
});

Route::middleware(['auth','admin'])->prefix("match")->group(function () {
    Route::post('/generateBracket/{tournamentId}', [MatchesController::class, 'store'])->name('match.store');
    Route::post('/selectWinningTeam/{matchId}', [MatchesController::class, 'selectWinningTeam'])->name('match.selectWinningTeam');
});

Route::resource('chirps', ChirpController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified', 'admin']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

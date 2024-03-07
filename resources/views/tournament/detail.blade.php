<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">
            Welcome to the Tournament: {{ $tournament->tournament_name }}
            </h>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="text-align: center; font-size: 20px">
                <b>Tournament Information</b>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="card text-center mb-2">
                        <div class="card-header">Tournament name: {{ $tournament->tournament_name }}</div>
                        <div class="card-body">
                            <div>
                                <b class="card-title">Tournament Description:</b>
                                <p class="card-text"> {{ $tournament->tournament_description }} </p>
                            </div>
                            <div class='mt-2'>
                                <b class="card-title ">Game Played:</b>
                                <p class="card-text">{{ $tournament->game_played }}</p>
                            </div>
                            <div class='mt-2'>
                                <b class="card-title ">Team Size</b>
                                <p class="card-text">{{ $tournament->team_size }}</p>
                            </div>
                            <div class='mt-2'>
                                <b class="card-title ">Tournament Time:</b>
                                <p class="card-text">Start: {{ $tournament->start_date }} - End:
                                    {{ $tournament->end_date }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="text-align: center; font-size: 20px">
                <b class="test">All Teams Participate</b>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-5">
                <div class="p-6 text-gray-900">
                    @if (count($teamsWithPlayers) === 0)
                        <div>There are currently no teams added</div>
                    @endif

                    @if (Auth::user()->user_type == 'admin')
                        <a href="{{ route('team.create', $tournament) }}" class="btn btn-primary my-3"
                            data-mdb-ripple-init>Add Team</a>
                    @endif

                    @foreach ($teamsWithPlayers as $team)
                        <h1 style="text-align: center; font-size: 20px">{{ $team->team_name }}</h1>
                        <div style="float: right">
                            @if (Auth::user()->user_type == 'admin')
                                <a href="{{ route('team.edit', $team) }}" class="btn btn-warning "
                                    data-mdb-ripple-init>Edit Team</a>
                                <button class="btn btn-danger" id="btn-delete" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal" data-team-name="{{ $team->team_name }}"
                                    data-team-id="{{ $team->id }}" data-tournament-id="{{ $tournament->id }}">
                                    Delete Team
                                </button>
                            @endif
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Player_ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">In Game Name</th>
                                    <th scope="col">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($team->players as $player)
                                    <tr>
                                        <th scope="row">{{ $player->id }}</th>
                                        <td>{{ $player->user['name'] }}</td>
                                        <td>{{ $player->in_game_name }}</td>
                                        <td>{{ $player->user['email'] }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @endforeach

                </div>
            </div>
        </div>
    </div>


    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="text-align: center; font-size: 20px">
                <b class="test">Tournament Bracket</b>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-5">
                <div class="p-6 text-gray-900">
                    <div class="theme theme-dark">
                        <div class="bracket disable-image">

                            <div class="column one">
                                @foreach ($matches as $match)
                                    <div class="match winner-top">
                                        <div class="match-top team">
                                            <span class="image"></span>
                                            <span class="seed">{{ $match->team1_id }}</span>
                                            <span class="name">Orlando Jetsetters</span>
                                            <span class="score"></span>
                                        </div>
                                        <div class="match-bottom team">
                                            <span class="image"></span>
                                            <span class="seed">{{ $match->team2_id }}</span>
                                            <span class="name">D.C. Senators</span>
                                            <span class="score"></span>
                                        </div>
                                        <div class="match-lines">
                                            <div class="line one"></div>
                                            <div class="line two"></div>
                                        </div>
                                        <div class="match-lines alt">
                                            <div class="line one"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- <div class="column two">
                                <div class="match winner-bottom">
                                    <div class="match-top team">
                                        <span class="image"></span>
                                        <span class="seed">1</span>
                                        <span class="name">Orlando Jetsetters</span>
                                        <span class="score">1</span>
                                    </div>
                                    <div class="match-bottom team">
                                        <span class="image"></span>
                                        <span class="seed">5</span>
                                        <span class="name">West Virginia Runners</span>
                                        <span class="score">2</span>
                                    </div>
                                    <div class="match-lines">
                                        <div class="line one"></div>
                                        <div class="line two"></div>
                                    </div>
                                    <div class="match-lines alt">
                                        <div class="line one"></div>
                                    </div>
                                </div>
                                <div class="match winner-bottom">
                                    <div class="match-top team">
                                        <span class="image"></span>
                                        <span class="seed">2</span>
                                        <span class="name">Denver Demon Horses</span>
                                        <span class="score">1</span>
                                    </div>
                                    <div class="match-bottom team">
                                        <span class="image"></span>
                                        <span class="seed">3</span>
                                        <span class="name">San Francisco Porters</span>
                                        <span class="score">2</span>
                                    </div>
                                    <div class="match-lines">
                                        <div class="line one"></div>
                                        <div class="line two"></div>
                                    </div>
                                    <div class="match-lines alt">
                                        <div class="line one"></div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="column three">
                                <div class="match winner-top">
                                    <div class="match-top team">
                                        <span class="image"></span>
                                        <span class="seed">5</span>
                                        <span class="name">West Virginia Runners</span>
                                        <span class="score">3</span>
                                    </div>
                                    <div class="match-bottom team">
                                        <span class="image"></span>
                                        <span class="seed">3</span>
                                        <span class="name">San Francisco Porters</span>
                                        <span class="score">2</span>
                                    </div>
                                    <div class="match-lines">
                                        <div class="line one"></div>
                                        <div class="line two"></div>
                                    </div>
                                    <div class="match-lines alt">
                                        <div class="line one"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="margin-top:15%">
        <div class="modal-content">
            <div class="modal-body" style="text-align: center">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var deleteButtons = document.querySelectorAll('#btn-delete');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var teamId = this.getAttribute('data-team-id');
                var tournamentId = this.getAttribute('data-tournament-id');
                var teamName = this.getAttribute('data-team-name');
                var modalBody = document.querySelector('.modal-body');
                modalBody.innerHTML = '<h1>Do you confirm delete: ' +
                    teamName + '?</h1>';
                var deleteForm = document.querySelector('#deleteForm');
                deleteForm.action = '/team/delete/' + tournamentId + '/' + teamId;
            });
        });
    });
</script>

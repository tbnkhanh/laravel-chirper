<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">
            Welcome to the Tournament: {{ $tournament->tournament_name }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="card text-center mb-2">
                        <div class="card-header">Tournament name: {{ $tournament->tournament_name }}</div>
                        <div class="card-body">
                            <h5 class="card-title">Tournament Description:</h5>
                            <p class="card-text"> {{ $tournament->tournament_description }} </p>
                            <h5 class="card-title mt-3">Tournament Time:</h5>
                            <p class="card-text">{{ $tournament->start_date }} - {{ $tournament->end_date }}</p>
                        </div>
                        <div class="card-footer text-muted">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="text-align: center; font-size: 25px">
                <b>All Team</b>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-5">
                <div class="p-6 text-gray-900">
                    @if (count($teamsWithPlayers) === 0)
                        <div>There are currently no teams added</div>
                    @endif

                    @if (Auth::user()->user_type == 'admin')
                        <a href="{{ route('tournament.create') }}" class="btn btn-primary my-3"
                            data-mdb-ripple-init>Add Team</a>
                    @endif

                    @foreach ($teamsWithPlayers as $team)
                        <h1 style="text-align: center; font-size: 20px">{{ $team->team_name }}</h1>
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
                                    <th scope="row">{{$player->id}}</th>
                                    <td>{{$player->user['name']}}</td>
                                    <td>{{$player->in_game_name}}</td>
                                    <td>{{$player->user['email']}}</td>
                                </tr>
                                @endforeach
                               
                            </tbody>
                        </table>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

</x-app-layout>

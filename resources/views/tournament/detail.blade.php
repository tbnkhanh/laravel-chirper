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
                            <h5 class="card-title">Tournament Description:</h5>
                            <p class="card-text"> {{ $tournament->tournament_description }} </p>
                            <h5 class="card-title mt-3">Tournament Time:</h5>
                            <p class="card-text">{{ $tournament->start_date }} - {{ $tournament->end_date }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div style="text-align: center; font-size: 20px">
                <b>All Teams Participate</b>
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
                                    data-bs-target="#exampleModal"
                                    data-team-name="{{ $team->team_name }}"
                                    data-team-id="{{ $team->id }}"
                                    data-tournament-id="{{ $tournament->id }}">
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

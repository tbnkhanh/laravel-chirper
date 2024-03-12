<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">
            Welcome to the Tournament: {{ $tournament->tournament_name }}
        </h1>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="max-width: 85rem">
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="max-width: 85rem">
            <div style="text-align: center; font-size: 20px">
                <b class="test">Tournament Bracket</b>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-5">
                <div class="p-6 text-gray-900">
                    @if (Auth::user()->user_type == 'admin' && $tournament->is_generate_bracket === '0')
                        <div style="text-align: center">
                            <form action="{{ route('match.store', $tournament->id) }}" method="post">
                                @csrf
                                <button class="btn btn-success">Generate Bracket</button>
                            </form>
                        </div>
                    @endif

                    <div class="theme theme-dark">

                        <div class="bracket">
                            @php
                                $round = log($tournament->team_number, 2);
                            @endphp
                            @for ($i = 1; $i <= $round; $i++)
                                <div class="column">
                                    @php
                                        $matchesInThisRound = collect([]);
                                    @endphp

                                    {{-- Lấy danh sách các match trong round --}}
                                    @foreach ($matches as $match)
                                        @if ($match->round_number == $i)
                                            @php
                                                $matchesInThisRound->push($match);
                                            @endphp
                                        @endif
                                    @endforeach

                                    @foreach ($matchesInThisRound as $match)
                                        <div class="match winner-top" id="{{ $match->id }}"
                                            data-team1-id="{{ $match->team1_id }}"
                                            data-team2-id="{{ $match->team2_id }}">

                                            <div class="match-infor">Round: {{ $match->round_number }} - Match:
                                                {{ $match->match_number }}</div>
                                            <div class="match-top team">
                                                @if ($match->team1_id !== null)
                                                    <span class="team1Seed">{{ $match->team1['seed'] }}</span>
                                                    <span class="name">{{ $match->team1['team_name'] }}</span>
                                                @else
                                                    <span class="name"></span>
                                                @endif
                                                <span class="winner">
                                                    @if ($match->winner_team_id === $match->team1_id && $match->winner_team_id !== null)
                                                        Winner
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="match-bottom team">
                                                @if ($match->team2_id !== null)
                                                    <span class="team2Seed">{{ $match->team2['seed'] }}</span>
                                                    <span class="name">{{ $match->team2['team_name'] }}</span>
                                                @else
                                                    <span class="name"></span>
                                                @endif
                                                <span class="winner">
                                                    @if ($match->winner_team_id === $match->team2_id && $match->winner_team_id !== null)
                                                        Winner
                                                    @endif
                                                </span>
                                            </div>

                                            @if ($match->round_number != $round)
                                                <div class="match-lines">
                                                    <div class="line one"></div>
                                                    <div class="line two"></div>
                                                </div>
                                            @endif
                                            <div class="match-lines alt">
                                                <div class="line one"></div>
                                            </div>

                                        </div>
                                    @endforeach

                                    {{-- Nếu số lượng match trong round ít hơn số lượng match yêu cầu, tạo ra các div rỗng --}}
                                    @if ($matchesInThisRound->count() < $tournament->team_number / 2 ** $i)
                                        @for ($j = $matchesInThisRound->count() + 1; $j <= $tournament->team_number / 2 ** $i; $j++)
                                            <div class="match winner-top">
                                                <div class="match-top team">

                                                    <span class="team1Id"></span>
                                                    <span class="name"></span>
                                                    <span class="winner"></span>
                                                </div>
                                                <div class="match-bottom team">

                                                    <span class="team2Id"></span>
                                                    <span class="name"></span>
                                                    <span class="winner"></span>
                                                </div>
                                                @if ($i != $round)
                                                    <div class="match-lines">
                                                        <div class="line one"></div>
                                                        <div class="line two"></div>
                                                    </div>
                                                @endif
                                                <div class="match-lines alt">
                                                    <div class="line one"></div>
                                                </div>
                                            </div>
                                        @endfor
                                    @endif
                                </div>
                            @endfor
                            <div class="column">
                                {{-- <div class="match winner-top"> --}}
                                @if ($tournament->winner_team !== null)
                                    <div style=" text-align: center; width: 150px">
                                        <b>Champion</b>
                                        <img src="{{ asset('img/trophy-cup.png') }}">
                                        <b>{{ $tournament->winner_team }}</b>
                                    </div>
                                @endif

                                {{-- </div> --}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="max-width: 85rem">
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


</x-app-layout>


<!-- Modal Select Team Win-->
<div class="modal fade" id="selectTeamWin" tabindex="-1" aria-labelledby="selectTeamWin" aria-hidden="true">
    <div class="modal-dialog" style="margin-top:15%">
        <div class="modal-content">
            <div class="modal-body" id="modal-body-team-win" style="text-align: center">
                <p>Select the winning team:</p>
                <div id="winningTeams"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form id="btnSelectTeamWin" method="POST" action="">
                    @csrf
                    <button class="btn btn-danger">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal Delete-->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="margin-top:15%">
        <div class="modal-content">
            <div class="modal-body" id ="modal-body-delete" style="text-align: center">
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

@php
    $userType = Auth::user()->user_type;
@endphp

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var deleteButtons = document.querySelectorAll('#btn-delete');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var teamId = this.getAttribute('data-team-id');
                var tournamentId = this.getAttribute('data-tournament-id');
                var teamName = this.getAttribute('data-team-name');
                var modalBody = document.querySelector('#modal-body-delete');
                modalBody.innerHTML = '<h1>Do you confirm delete: ' +
                    teamName + '?</h1>';
                var deleteForm = document.querySelector('#deleteForm');
                deleteForm.action = '/team/delete/' + tournamentId + '/' + teamId;
            });
        });

        var userType = "{{ $userType }}";
        if (userType === 'admin') {
            var matches = document.querySelectorAll('.match.winner-top');
            matches.forEach(function(match) {
                match.addEventListener('click', function() {
                    var matchId = match.getAttribute('id');
                    var team1Id = match.getAttribute('data-team1-id');
                    var team2Id = match.getAttribute('data-team2-id');

                    var team1Name = match.querySelector('.match-top .name').innerText;
                    var team2Name = match.querySelector('.match-bottom .name').innerText;
                    var winnerTeamId = match.querySelector('.match-top .winner').innerText;
                    var winnerTeamId2 = match.querySelector('.match-bottom .winner').innerText;

                    if (winnerTeamId.trim() !== '' || winnerTeamId2.trim() !== '') {
                        return;
                    }

                    var modalBody = document.getElementById('modal-body-team-win');
                    modalBody.innerHTML = '<p>Select the winning team:</p>';
                    modalBody.innerHTML +=
                        '<div><label><input type="radio" name="winning_team" value="' +
                        team1Id +
                        '"> ' + team1Name + '</label></div>';
                    modalBody.innerHTML +=
                        '<div><label><input type="radio" name="winning_team" value="' +
                        team2Id +
                        '"> ' + team2Name + '</label></div>';

                    var btnSelectTeamWin = document.getElementById('btnSelectTeamWin');

                    $('#selectTeamWin').modal('show'); // Hiển thị modal

                    btnSelectTeamWin.addEventListener('submit', function(event) {
                        event.preventDefault();

                        var winningTeamId = document.querySelector(
                            'input[name="winning_team"]:checked').value;

                        btnSelectTeamWin.action = '/match/' + 'selectWinningTeam/' +
                            matchId + '?winning_team=' + winningTeamId;

                        btnSelectTeamWin.submit();
                    });
                });
            });
        }


    });
</script>

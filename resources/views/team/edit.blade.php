<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">
            Editting {{ $teamsWithPlayers->team_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('team.update', $teamsWithPlayers->id ) }}" method="post"
                        style="width: 26rem; margin:auto">
                        @csrf
                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Team Name</label>
                            <input type="text" id="form4Example1" class="form-control" name="team_name" value="{{ $teamsWithPlayers->team_name }}"/>
                            @if ($errors->has('team_name'))
                                <p class="text-danger">
                                    {{ $errors->first('team_name') }}
                                </p>
                            @endif
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Player1 Email</label>
                            <input type="text" id="form4Example1" class="form-control" name="player1_email" value="{{ $teamsWithPlayers->players[0]->user['email'] }}"  />
                            @if ($errors->has('player1_email'))
                                <p class="text-danger">
                                    {{ $errors->first('player1_email') }}
                                </p>
                            @endif
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Player1 In Game Name</label>
                            <input type="text" id="form4Example1" class="form-control" name="in_game1" value="{{ $teamsWithPlayers->players[0]->in_game_name }}" />
                            @if ($errors->has('in_game1'))
                                <p class="text-danger">
                                    {{ $errors->first('in_game1') }}
                                </p>
                            @endif
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Player2 Email</label>
                            <input type="text" id="form4Example1" class="form-control" name="player2_email"  value="{{ $teamsWithPlayers->players[1]->user['email'] }}"/>
                            @if ($errors->has('player2_email'))
                                <p class="text-danger">
                                    {{ $errors->first('player2_email') }}
                                </p>
                            @endif
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Player2 In Game Name</label>
                            <input type="text" id="form4Example1" class="form-control" name="in_game2" value="{{ $teamsWithPlayers->players[1]->in_game_name }}" />
                            @if ($errors->has('in_game2'))
                                <p class="text-danger">
                                    {{ $errors->first('in_game2') }}
                                </p>
                            @endif
                        </div>

                        @foreach (['duplicate_email', 'users_in_other_team', 'email1_invalid', 'email2_invalid'] as $error)
                            @if ($errors->has($error))
                                <p class="text-danger mb-2">{{ $errors->first($error) }}</p>
                            @endif
                        @endforeach

                        <div style="text-align: center;">
                            <button data-mdb-ripple-init class="btn btn-warning btn-block mb-4">Save Infor Team</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

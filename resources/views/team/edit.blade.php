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
                    <form action="{{ route('team.update', $teamsWithPlayers->id) }}" method="post"
                        style="width: 26rem; margin:auto">
                        @csrf
                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Team Name</label>
                            <input type="text" id="form4Example1" class="form-control" name="team_name"
                                value="{{ $teamsWithPlayers->team_name }}" />
                            @if ($errors->has('team_name'))
                                <p class="text-danger">
                                    {{ $errors->first('team_name') }}
                                </p>
                            @endif
                        </div>

                        @for ($i = 1; $i <= count($teamsWithPlayers->players); $i++)
                            <div data-mdb-input-init class="form-outline mb-4">
                                <label class="form-label"
                                    for="player_email{{ $i }}">Player{{ $i }} Email</label>
                                <input type="text" id="player_email{{ $i }}" class="form-control"
                                    name="player_email[{{ $i }}]" value="{{ $teamsWithPlayers->players[$i-1]->user['email'] }}"/>
                                @if ($errors->has('player_email.' . $i))
                                    <p class="text-danger">
                                        {{ $errors->first('player_email.' . $i) }}
                                    </p>
                                @endif
                            </div>

                            <div data-mdb-input-init class="form-outline mb-4">
                                <label class="form-label" for="in_game{{ $i }}">Player{{ $i }}
                                    Name In Game </label>
                                <input type="text" id="in_game{{ $i }}" class="form-control"
                                    name="in_game[{{ $i }}]" value="{{ $teamsWithPlayers->players[$i-1]->in_game_name }}"/>
                                @if ($errors->has('in_game.' . $i))
                                    <p class="text-danger">
                                        {{ $errors->first('in_game.' . $i) }}
                                    </p>
                                @endif
                            </div>
                        @endfor


                        @foreach (['duplicate_email', 'users_in_other_team', 'email1_invalid', 'email2_invalid'] as $error)
                            @if ($errors->has($error))
                                <p class="text-danger mb-2">{{ $errors->first($error) }}</p>
                            @endif
                        @endforeach

                        <div style="text-align: center;">
                            <button data-mdb-ripple-init class="btn btn-warning btn-block mb-4">Save Infor
                                Team</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

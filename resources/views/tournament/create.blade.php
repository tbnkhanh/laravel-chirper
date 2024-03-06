<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">
            {{ __('Create New Tournament') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('tournament.store') }}" method="post" style="width: 26rem; margin:auto">
                        @csrf
                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Tournament Name</label>
                            <input type="text" id="form4Example1" class="form-control" name="tournament_name" />
                            @if ($errors->has('tournament_name'))
                                <p class="text-danger">
                                    {{ $errors->first('tournament_name') }}
                                </p>
                            @endif
                        </div>


                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example3">Tournament Description</label>
                            <textarea class="form-control" id="form4Example3" rows="4" name="tournament_description"></textarea>
                            @if ($errors->has('tournament_description'))
                                <p class="text-danger">
                                    {{ $errors->first('tournament_description') }}
                                </p>
                            @endif
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="cars" class="form-label" for="form4Example3">Select Game Played</label>
                            <select name="game_played" id="cars" class="form-control">
                                <option value="PUBG">PUBG</option>
                                <option value="FIFA">FIFA</option>
                                <option value="LOL">LOL</option>
                                <option value="CS:GO">CS:GO</option>
                            </select>
                        </div>


                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="cars" class="form-label" for="form4Example3">Select Team Size</label>
                            <select name="team_size" id="cars" class="form-control">
                                <option value=1>1</option>
                                <option value=2>2</option>
                                <option value=3>3</option>
                                <option value=4>4</option>
                                <option value="5">5</option>
                            </select>
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="birthday">Start date:</label>
                            <input type="date" id="birthday" name="start_date" style="width: 100%;">
                            @if ($errors->has('start_date'))
                                <p class="text-danger">
                                    {{ $errors->first('start_date') }}
                                </p>
                            @endif
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="birthday" style="margin-right:6px">End date:</label>
                            <input type="date" id="birthday" name="end_date" style="width: 100%;">
                            @if ($errors->has('end_date'))
                                <p class="text-danger">
                                    {{ $errors->first('end_date') }}
                                </p>
                            @endif
                        </div>

                        <!-- Submit button -->
                        <div style="text-align: center;">
                            <button data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Create
                                Tournament</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

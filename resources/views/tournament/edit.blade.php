<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">
            Edit '{{ $tournament->tournament_name }}' Tournament
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="/tournament/update/{{ $tournament->id }}" method="POST"
                        style="width: 26rem; margin:auto">
                        @csrf
                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example1">Tournament Name</label>
                            <input type="text" class="form-control" name="tournament_name"
                                value='{{ $tournament->tournament_name }}' />
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label class="form-label" for="form4Example3">Tournament Description</label>
                            <textarea class="form-control" id="form4Example3" rows="4" name="tournament_description">{{ $tournament->tournament_description }}</textarea>
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="cars" class="form-label" for="form4Example3">Select Game Played</label>
                            <select name="game_played" id="cars" class="form-control">
                                <?php foreach(['PUBG', 'FIFA', 'LOL', 'CS:GO'] as $game): ?>
                                <?php if($game == $tournament->game_played): ?>
                                <option value="<?php echo $game; ?>" selected><?php echo $game; ?></option>
                                <?php else: ?>
                                <option value="<?php echo $game; ?>"><?php echo $game; ?></option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="cars" class="form-label" for="form4Example3">Select Team Size</label>
                            <select name="team_size" id="cars" class="form-control">
                                <?php foreach(['1', '2', '3', '4', '5'] as $size): ?>
                                <?php if($size == $tournament->team_size): ?>
                                <option value="<?php echo $size; ?>" selected><?php echo $size; ?></option>
                                <?php else: ?>
                                <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="birthday">Start date:</label>
                            <input type="date" id="birthday" name="start_date" value="{{ $tournament->start_date }}"
                                style="width: 100%;">
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <label for="birthday" style="margin-right:6px">End date:</label>
                            <input type="date" id="birthday" name="end_date" value="{{ $tournament->end_date }}"
                                style="width: 100%;">
                        </div>

                        <!-- Submit button -->
                        <div style="text-align: center;">
                            <button data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Save Infomation
                                Tournament</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

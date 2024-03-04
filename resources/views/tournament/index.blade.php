<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight" style="text-align: center">
            {{ __('All Tournament') }}
        </h2>
    </x-slot>

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


    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (Auth::user()->user_type == 'admin')
                <a href="{{ route('tournament.create') }}" class="btn btn-primary my-3" data-mdb-ripple-init>Create new
                    tournament</a>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @foreach ($tournaments as $tournament)
                        <div class="card text-center mb-4">
                            <div class="card-header">Tournament name: {{ $tournament->tournament_name }}</div>
                            <div class="card-body">
                                <h5 class="card-title">Tournament Description:</h5>
                                <p class="card-text"> {{ $tournament->tournament_description }} </p>
                                <h5 class="card-title mt-3">Tournament Time:</h5>
                                <p class="card-text">{{ $tournament->start_date }} - {{ $tournament->end_date }}</p>
                            </div>
                            <div class="card-footer text-muted">
                                <a href="/tournament/detail/{{ $tournament->id }}" class="btn btn-success "
                                    data-mdb-ripple-init>Detail</a>
                                @if (Auth::user()->user_type == 'admin')
                                    <a href="/tournament/edit/{{ $tournament->id }}" class="btn btn-warning "
                                        data-mdb-ripple-init>Edit</a>
                                    <button class="btn btn-danger" id="btn-delete" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"
                                        data-tournament-name="{{ $tournament->tournament_name }}"
                                        data-tournament-id="{{ $tournament->id }}">
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var deleteButtons = document.querySelectorAll('#btn-delete');
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var tournamentId = this.getAttribute('data-tournament-id');
                var tournamentName = this.getAttribute('data-tournament-name');
                var modalBody = document.querySelector('.modal-body');
                modalBody.innerHTML = '<h3>Do you confirm delete tournament: ' +
                    tournamentName + '?</h3>';
                var deleteForm = document.querySelector('#deleteForm');
                deleteForm.action = '/tournament/delete/' + tournamentId;
            });
        });
    });
</script>

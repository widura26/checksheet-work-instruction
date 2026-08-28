@extends('layouts.app')

@section('title', 'Bon Permintaan Material')

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Bon Permintaan Material</h5>
            </div>
        </div>
        <div class="card-body">
            <table id="bpm-table" class="table table-bordered table-hover" style="width: 100%;">
                <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

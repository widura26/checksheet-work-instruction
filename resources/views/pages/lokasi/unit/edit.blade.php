@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Edit Unit</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lokasi.unit.update', $unit->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('pages.lokasi.unit._form', ['unit' => $unit])
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('lokasi.unit.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Tambah Unit</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lokasi.unit.store') }}" method="POST">
                    @csrf
                    @include('pages.lokasi.unit._form')
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('lokasi.unit.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

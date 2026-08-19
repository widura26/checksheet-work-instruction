@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Tambah Departemen</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lokasi.departemen.store') }}" method="POST">
                    @csrf
                    @include('pages.lokasi.departemen._form')
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('lokasi.departemen.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Edit Bagian</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lokasi.bagian.update', $bagian->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('pages.lokasi.bagian._form', ['bagian' => $bagian])
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('lokasi.bagian.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Form Edit Staff
        </div>
        <div class="card-body">
            <form action="{{ route('staff.update', $staff) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pages.staff._form')
                <div class="d-flex justify-content-end">
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

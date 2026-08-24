@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Form Edit Role
        </div>
        <div class="card-body">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                @include('pages.roles._form', ['role' => $role])
                <div class="d-flex justify-content-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Tambah Role')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Form Tambah Role
        </div>
        <div class="card-body">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                @include('pages.roles._form')
                <div class="d-flex justify-content-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

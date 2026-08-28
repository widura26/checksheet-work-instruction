@extends('layouts.app')

@section('title', 'Tambah Barcode')

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Tambah Barcode</h5>
            <a href="{{ route('barcode.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('barcode.store') }}">
                @csrf
                @include('barcode._form')
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Kode Material')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('kode-material.update', $kodeMaterial->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="kode_material" class="form-label">Kode Material</label>
                    <input type="text" class="form-control @error('kode_material') is-invalid @enderror" id="kode_material"
                        name="kode_material" value="{{ old('kode_material', $kodeMaterial->kode_material) }}" required>
                    @error('kode_material')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <input type="text" class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi"
                        name="deskripsi" value="{{ old('deskripsi', $kodeMaterial->deskripsi) }}">
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="spesifikasi" class="form-label">Spesifikasi</label>
                    <input type="text" class="form-control @error('spesifikasi') is-invalid @enderror" id="spesifikasi"
                        name="spesifikasi" value="{{ old('spesifikasi', $kodeMaterial->spesifikasi) }}">
                    @error('spesifikasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="satuan" class="form-label">Satuan</label>
                    <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan"
                        name="satuan" value="{{ old('satuan', $kodeMaterial->satuan) }}">
                    @error('satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('kode-material.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection

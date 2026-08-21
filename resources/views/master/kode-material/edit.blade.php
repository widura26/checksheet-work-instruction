@extends('layouts.app')

@section('title', 'Edit Kode Material')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Kode Material</h5>
            <small class="text-body-secondary">Form untuk mengubah data kode material</small>
        </div>
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
                    <label for="id_wi_trn_bpm" class="form-label">Kode Material</label>
                    <input type="text" class="form-control @error('id_wi_trn_bpm') is-invalid @enderror" id="id_wi_trn_bpm"
                        name="id_wi_trn_bpm" value="{{ old('id_wi_trn_bpm', $kodeMaterial->id_wi_trn_bpm) }}" required>
                    @error('id_wi_trn_bpm')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="spec" class="form-label">Spesifikasi</label>
                    <input type="text" class="form-control @error('spec') is-invalid @enderror" id="spec"
                        name="spec" value="{{ old('spec', $kodeMaterial->spec) }}">
                    @error('spec')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ket" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('ket') is-invalid @enderror" id="ket" name="ket"
                        rows="3">{{ old('ket', $kodeMaterial->ket) }}</textarea>
                    @error('ket')
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

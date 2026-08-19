@extends('layouts.app')

@section('title', 'Edit Lokasi Bagian')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Lokasi Bagian</h5>
            <small class="text-body-secondary">Catatan: Lokasi harus diawali dengan "lokasi-" (misalnya
                fabrikasi-tempat).</small>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Terdapat beberapa masalah dengan inputan Anda.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('bagian.update', $bagian->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="nama_bagian" class="form-label">Nama Bagian</label>
                    <input type="text" name="nama_bagian" class="form-control" id="nama_bagian"
                        value="{{ old('nama_bagian', $bagian->nama_bagian) }}" required>
                </div>
                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi</label>
                    <select name="lokasi" class="form-select" id="lokasi" required>
                        <option value="" disabled>-- Pilih Lokasi --</option>
                        <option value="fabrikasi" {{ old('lokasi', $bagian->lokasi) == 'fabrikasi' ? 'selected' : '' }}>
                            Fabrikasi</option>
                        <option value="finishing" {{ old('lokasi', $bagian->lokasi) == 'finishing' ? 'selected' : '' }}>
                            Finishing</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('bagian.index') }}" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

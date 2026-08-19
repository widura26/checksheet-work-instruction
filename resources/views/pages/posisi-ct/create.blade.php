@extends('layouts.app')

@section('title', 'Tambah Posisi CT')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Tambah Posisi CT</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <form action="{{ route('posisi-ct.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="nama_posisi_ct" class="form-label">Nama Posisi CT
                        <small class="text-muted d-block">
                            1 posisi CT untuk setiap departemen.
                        </small>
                    </label>
                    <input type="text" class="form-control @error('nama_posisi_ct') is-invalid @enderror"
                        id="nama_posisi_ct" name="nama_posisi_ct" value="{{ old('nama_posisi_ct') }}" required>
                    @error('nama_posisi_ct')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="departemen_id" class="form-label">Departemen</label>
                    <select class="form-select @error('departemen_id') is-invalid @enderror" id="departemen_id"
                        name="departemen_id">
                        <option value="">--- Pilih Departemen ---</option>
                        <option value="uu" {{ old('departemen_id') == 'uu' ? 'selected' : '' }}>(UU) Stok Bebas</option>
                        @foreach ($departemens as $departemen)
                            <option value="{{ $departemen->id }}"
                                {{ old('departemen_id') == $departemen->id ? 'selected' : '' }}>
                                {{ $departemen->nama_departemen }}
                            </option>
                        @endforeach
                    </select>
                    @error('departemen_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('posisi-ct.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection

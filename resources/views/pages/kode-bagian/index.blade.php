@extends('layouts.app')

@section('title', 'Kode Bagian SPM')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Formulir Setting</h5>
            <small class="text-body-secondary">Ubah informasi di bawah</small>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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

            <form action="{{ route('kode-bagian.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode Bagian<span class="text-danger">*</span></label>
                            <input type="text" name="kode" id="kode"
                                class="form-control @error('kode') is-invalid @enderror"
                                value="{{ old('kode', $kodeBagian->kode ?? '') }}" placeholder="Masukkan kode" required>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" id="jabatan"
                                class="form-control @error('jabatan') is-invalid @enderror"
                                value="{{ old('jabatan', $kodeBagian->jabatan ?? '') }}" placeholder="Masukkan jabatan"
                                required>
                            @error('jabatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_manager" class="form-label">Nama Manager <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_manager" id="nama_manager"
                                class="form-control @error('nama_manager') is-invalid @enderror"
                                value="{{ old('nama_manager', $kodeBagian->nama_manager ?? '') }}"
                                placeholder="Masukkan nama manager" required>
                            @error('nama_manager')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="stok_rendah" class="form-label">Stok Rendah <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="stok_rendah" id="stok_rendah"
                                class="form-control @error('stok_rendah') is-invalid @enderror"
                                value="{{ old('stok_rendah', $kodeBagian->stok_rendah ?? '') }}"
                                placeholder="Masukkan stok rendah" min="0" required>
                            @error('stok_rendah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Nilai minimum stok yang dianggap rendah</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="departemen_id" class="form-label">Departemen <span
                                    class="text-danger">*</span></label>
                            <select name="departemen_id" id="departemen_id"
                                class="form-select @error('departemen_id') is-invalid @enderror" required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemen as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ old('departemen_id', $kodeBagian->departemen_id ?? '') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nama_departemen }}</option>
                                @endforeach
                            </select>
                            @error('departemen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bagian_id" class="form-label">Bagian <span class="text-danger">*</span></label>
                            <select name="bagian_id" id="bagian_id"
                                class="form-select @error('bagian_id') is-invalid @enderror" required>
                                <option value="">Pilih Bagian</option>
                                @foreach ($bagian as $bg)
                                    <option value="{{ $bg->id }}"
                                        {{ old('bagian_id', $kodeBagian->bagian_id ?? '') == $bg->id ? 'selected' : '' }}>
                                        {{ $bg->nama_bagian }}</option>
                                @endforeach
                            </select>
                            @error('bagian_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                            <select name="unit_id" id="unit_id"
                                class="form-select @error('unit_id') is-invalid @enderror" required>
                                <option value="">Pilih unit</option>
                                @foreach ($unit as $bg)
                                    <option value="{{ $bg->id }}"
                                        {{ old('unit_id', $kodeBagian->unit_id ?? '') == $bg->id ? 'selected' : '' }}>
                                        {{ $bg->nama_unit }}</option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        @if (session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success'
            });
        @endif
        @if (session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: '{{ session('error') }}',
                icon: 'error'
            });
        @endif
    </script>
@endpush

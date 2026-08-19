@extends('layouts.app')

@section('title', 'Update Stok Tool')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="card-title mb-0">
                <i class="fas fa-plus-square me-2"></i>Update Stok Tool
            </h5>
            <small class="text-muted">
                Update stok tool berdasarkan kode material yang sudah terdaftar
            </small>
        </div>

        <div class="card-body">
            <form action="{{ route('update-stok-tool.store') }}" method="POST" id="myForm" class="enhanced-tool-form">
                @csrf
                <input type="hidden" name="form_mode" id="form_mode">

                {{-- ERROR SERVER --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- LOADING --}}
                <div class="loading-indicator alert alert-info mb-3">
                    <i class="fas fa-spinner fa-spin me-1"></i>
                    Memvalidasi data tool...
                </div>

                <div class="row">
                    {{-- KODE MATERIAL --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Kode Material <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="kode_material" name="kode_material" class="form-control" required>

                        <div class="valid-feedback"></div>
                        <div class="invalid-feedback"></div>
                    </div>

                    {{-- POSISI CT --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Posisi CT <span class="text-danger">*</span>
                        </label>
                        <select id="posisi_ct_id" name="posisi_ct_id" class="form-select" required>
                            <option value="">-- Pilih Posisi --</option>
                            @foreach ($posisiCt as $posisi)
                                <option value="{{ $posisi->id }}">
                                    {{ $posisi->nama }}
                                </option>
                            @endforeach
                        </select>

                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row">
                    {{-- DESKRIPSI --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" id="deskripsi" class="form-control" readonly>
                    </div>

                    {{-- SATUAN --}}
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" id="satuan" class="form-control" readonly>
                    </div>

                    {{-- STOK SAAT INI --}}
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Stok Saat Ini</label>
                        <input type="number" id="qty_current" class="form-control" readonly>
                    </div>

                    {{-- QTY TAMBAH --}}
                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            Qty Tambah <span class="text-danger">*</span>
                        </label>
                        <input type="number" id="qty_ditambah" name="qty_ditambah" class="form-control" min="1"
                            required>
                    </div>
                </div>

                <hr>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('update-stok-tool.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('css')
    <style>
        .loading-indicator {
            display: none;
        }

        .readonly-field {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
    </style>
@endpush

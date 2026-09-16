@extends('layouts.app')

@section('title', 'Tambah Bill Of Material')

@push('css')
    <style>
        .icon {
            vertical-align: middle;
        }
        .document-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .document-item:last-child {
            border-bottom: none;
        }
        .document-item .file-name {
            flex: 1;
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Tambah Bill Of Material</h5>
            <a href="{{ route('bill-of-material.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card-body">
            <form id="bomForm" action="{{ route('bill-of-material.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="project_id" class="form-label">Proyek <span class="text-danger">*</span></label>
                        <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                            <option value="">-- Pilih Proyek --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_id }} - {{ $project->name_project }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="number" class="form-label">Nomor BOM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('number') is-invalid @enderror" id="number" name="number" value="{{ old('number') }}" required>
                        @error('number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="published_date" class="form-label">Tanggal Terbit <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('published_date') is-invalid @enderror" id="published_date" name="published_date" value="{{ old('published_date', date('Y-m-d')) }}" required>
                        @error('published_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Dokumen BOM <span class="text-danger">*</span></label>
                        <div id="documents-container">
                            <div class="document-item">
                                <input type="file" class="form-control" name="documents[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" required>
                                <input type="text" class="form-control" name="revision_versions[]" placeholder="Rev" style="width: 100px;">
                                <button type="button" class="btn btn-outline-danger btn-remove-document" title="Hapus">
                                    <svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="btn-add-document">
                            <svg class="icon me-1" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"/></svg>
                            Tambah Dokumen
                        </button>
                        <div class="form-text">Format: PDF, XLS, XLSX (maks 5MB per file).</div>
                        @error('documents.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.getElementById('btn-add-document').addEventListener('click', function () {
            var container = document.getElementById('documents-container');
            var row = document.createElement('div');
            row.className = 'document-item';
            row.innerHTML =
                '<input type="file" class="form-control" name="documents[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" required>' +
                '<input type="text" class="form-control" name="revision_versions[]" placeholder="Rev" style="width: 100px;">' +
                '<button type="button" class="btn btn-outline-danger btn-remove-document" title="Hapus">' +
                '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>' +
                '</button>';
            container.appendChild(row);
        });

        document.getElementById('documents-container').addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-remove-document');
            if (!btn) return;
            var container = document.getElementById('documents-container');
            if (container.children.length > 0) {
                btn.closest('.document-item').remove();
            }
        });
    </script>
@endpush

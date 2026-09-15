@extends('layouts.app')

@section('title', 'Tambah Bill Of Material')

@push('css')
    <style>
        .icon {
            vertical-align: middle;
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
            <form id="bomForm" action="{{ route('bill-of-material.store') }}" method="POST">
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
                        <label for="number" class="form-label">Nomor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('number') is-invalid @enderror" id="number" name="number" value="{{ old('number') }}" required>
                        @error('number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="published_date" class="form-label">Published Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('published_date') is-invalid @enderror" id="published_date" name="published_date" value="{{ old('published_date', date('Y-m-d')) }}" required>
                        @error('published_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="revise" class="form-label">Revise</label>
                        <input type="text" class="form-control @error('revise') is-invalid @enderror" id="revise" name="revise" value="{{ old('revise', '0') }}">
                        @error('revise')
                            <div class="invalid-feedback">{{ $message }}</div>
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

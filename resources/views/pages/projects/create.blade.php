@extends('layouts.app')

@section('title', 'Buat Project Baru')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            Formulir Pembuatan Project
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

            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project ID <span class="text-danger">*</span></label>
                            <input type="text" name="project_id" id="project_id"
                                class="form-control @error('project_id') is-invalid @enderror"
                                value="{{ old('project_id') }}" required>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_project" class="form-label">Nama Project <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_project" id="nama_project"
                                class="form-control @error('nama_project') is-invalid @enderror"
                                value="{{ old('nama_project') }}" required>
                            @error('nama_project')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

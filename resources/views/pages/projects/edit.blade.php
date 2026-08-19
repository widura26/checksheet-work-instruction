@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')

    <div class="card mb-4">
        <div class="card-header">
            Formulir Edit Project
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> {{ session('error') }}<br><br>
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

            <form action="{{ route('projects.update', $project) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project ID</label>
                            <input type="text" name="project_id" id="project_id" class="form-control"
                                value="{{ $project->project_id }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_project" class="form-label">Nama Project <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_project" id="nama_project"
                                class="form-control @error('nama_project') is-invalid @enderror"
                                value="{{ old('nama_project', $project->nama_project) }}" required>
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
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

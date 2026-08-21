@extends('layouts.app')

@section('title', 'Edit Kode Proyek')

@section('content')
    @php
        $oka = $project->okas->first();
    @endphp
    <div class="folder-card mb-4">
        <div class="folder-tab">Edit Kode Proyek</div>
        <div class="folder-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('project-code.update', $project->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="project_code" class="form-label">Kode Proyek</label>
                        <input type="text" class="form-control @error('project_code') is-invalid @enderror"
                            id="project_code" name="project_code" value="{{ old('project_code', $project->project_code) }}" required>
                        @error('project_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $project->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="oka" class="form-label">Oka</label>
                        <input type="text" class="form-control @error('oka') is-invalid @enderror"
                            id="oka" name="oka" value="{{ old('oka', optional($oka)->oka) }}">
                        @error('oka')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="wbs" class="form-label">Wbs</label>
                        <input type="text" class="form-control @error('wbs') is-invalid @enderror"
                            id="wbs" name="wbs" value="{{ old('wbs', optional($oka)->wbs) }}">
                        @error('wbs')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="label_proyek" class="form-label">Label Proyek</label>
                        <input type="text" class="form-control @error('label_proyek') is-invalid @enderror"
                            id="label_proyek" name="label_proyek" value="{{ old('label_proyek', optional($oka)->label_proyek) }}">
                        @error('label_proyek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="total_car" class="form-label">Total Lot Car</label>
                        <input type="number" class="form-control @error('total_car') is-invalid @enderror"
                            id="total_car" name="total_car" value="{{ old('total_car', optional($oka)->total_car) }}">
                        @error('total_car')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="jig" class="form-label">Total Lot Jig</label>
                        <input type="text" class="form-control @error('jig') is-invalid @enderror"
                            id="jig" name="jig" value="{{ old('jig', optional($oka)->jig) }}">
                        @error('jig')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="prototype" class="form-label">Total Lot Prototype</label>
                        <input type="text" class="form-control @error('prototype') is-invalid @enderror"
                            id="prototype" name="prototype" value="{{ old('prototype', optional($oka)->prototype) }}">
                        @error('prototype')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="tgl_oka" class="form-label">Tgl</label>
                        <input type="date" class="form-control @error('tgl_oka') is-invalid @enderror"
                            id="tgl_oka" name="tgl_oka"
                            value="{{ old('tgl_oka', optional($oka)->tgl_oka ? \Carbon\Carbon::parse(optional($oka)->tgl_oka)->format('Y-m-d') : '') }}">
                        @error('tgl_oka')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('project-code.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection

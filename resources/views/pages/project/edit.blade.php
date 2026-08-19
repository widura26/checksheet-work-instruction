@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Edit Project: {{ $project->nama_project }}</h5>
            <small class="text-body-secondary">Ubah informasi project di bawah</small>
        </div>
        <div class="card-body">
            <form action="{{ route('project.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('project_id') is-invalid @enderror"
                                id="project_id" name="project_id" value="{{ old('project_id', $project->project_id) }}"
                                placeholder="Masukkan ID project" required>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_project" class="form-label">Nama Project <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_project') is-invalid @enderror"
                                id="nama_project" name="nama_project"
                                value="{{ old('nama_project', $project->nama_project) }}"
                                placeholder="Masukkan nama project" required>
                            @error('nama_project')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi"
                                name="lokasi" value="{{ old('lokasi', $project->lokasi) }}"
                                placeholder="Masukkan lokasi project" required>
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="">Pilih Status</option>
                                <option value="planning"
                                    {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Perencanaan
                                </option>
                                <option value="ongoing"
                                    {{ old('status', $project->status) == 'ongoing' ? 'selected' : '' }}>Berlangsung
                                </option>
                                <option value="completed"
                                    {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled"
                                    {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', $project->tanggal_mulai ? $project->tanggal_mulai->format('Y-m-d') : '') }}">
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                id="tanggal_selesai" name="tanggal_selesai"
                                value="{{ old('tanggal_selesai', $project->tanggal_selesai ? $project->tanggal_selesai->format('Y-m-d') : '') }}">
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4"
                        placeholder="Masukkan deskripsi project (opsional)">{{ old('deskripsi', $project->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('project.index') }}" class="btn btn-secondary">
                        <svg class="icon me-2">
                            <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-arrow-left') }}">
                            </use>
                        </svg>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="icon me-2">
                            <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-save') }}"></use>
                        </svg>
                        Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        // Validasi tanggal selesai tidak boleh lebih kecil dari tanggal mulai
        document.getElementById('tanggal_mulai').addEventListener('change', function() {
            const tanggalMulai = this.value;
            const tanggalSelesaiInput = document.getElementById('tanggal_selesai');

            if (tanggalMulai) {
                tanggalSelesaiInput.min = tanggalMulai;

                // Reset tanggal selesai jika lebih kecil dari tanggal mulai
                if (tanggalSelesaiInput.value && tanggalSelesaiInput.value < tanggalMulai) {
                    tanggalSelesaiInput.value = '';
                }
            }
        });

        // Set initial min date for tanggal_selesai
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalMulai = document.getElementById('tanggal_mulai').value;
            if (tanggalMulai) {
                document.getElementById('tanggal_selesai').min = tanggalMulai;
            }
        });
    </script>
@endpush

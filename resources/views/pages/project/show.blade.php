@extends('layouts.app')

@section('title', 'Detail Project')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Detail Project</h5>
                        <small class="text-body-secondary">Informasi lengkap project {{ $project->nama_project }}</small>
                    </div>
                    @role('admin')
                        <div class="btn-group">
                            <a href="{{ route('project.edit', $project->id) }}" class="btn btn-warning btn-sm">
                                <svg class="icon me-1">
                                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-pencil') }}">
                                    </use>
                                </svg>
                                Edit
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="deleteProject({{ $project->id }})">
                                <svg class="icon me-1">
                                    <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-trash') }}">
                                    </use>
                                </svg>
                                Hapus
                            </button>
                        </div>
                    @endrole
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Project ID:</td>
                                    <td>{{ $project->project_id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Nama Project:</td>
                                    <td>{{ $project->nama_project }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Lokasi:</td>
                                    <td>{{ $project->lokasi }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Status:</td>
                                    <td>
                                        <span class="badge {{ $project->status_badge }}">
                                            {{ $project->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="40%">Tanggal Mulai:</td>
                                    <td>{{ $project->tanggal_mulai ? $project->tanggal_mulai->format('d/m/Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tanggal Selesai:</td>
                                    <td>{{ $project->tanggal_selesai ? $project->tanggal_selesai->format('d/m/Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Dibuat:</td>
                                    <td>{{ $project->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Diperbarui:</td>
                                    <td>{{ $project->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if ($project->deskripsi)
                        <div class="mt-3">
                            <h6 class="fw-bold">Deskripsi:</h6>
                            <p class="text-muted">{{ $project->deskripsi }}</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('project.index') }}" class="btn btn-secondary">
                            <svg class="icon me-2">
                                <use
                                    xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-arrow-left') }}">
                                </use>
                            </svg>
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Distribusi Tools</h6>
                    <small class="text-body-secondary">Tools yang terdistribusi untuk project ini</small>
                </div>
                <div class="card-body">
                    @if ($project->distribusiTools->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($project->distribusiTools->take(5) as $distribusi)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $distribusi->kode_material ?? 'N/A' }}</h6>
                                            <p class="mb-1 small text-muted">{{ $distribusi->nama_material ?? 'N/A' }}</p>
                                            <small class="text-muted">Qty: {{ $distribusi->qty ?? 0 }}</small>
                                        </div>
                                        <small class="text-muted">{{ $distribusi->created_at->format('d/m') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($project->distribusiTools->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Dan {{ $project->distribusiTools->count() - 5 }} distribusi lainnya
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <svg class="icon icon-xl text-muted mb-3">
                                <use xlink:href="{{ asset('template/vendors/@coreui/icons/svg/free.svg#cil-inbox') }}">
                                </use>
                            </svg>
                            <p class="text-muted mb-0">Belum ada distribusi tools</p>
                            <small class="text-muted">Tools yang didistribusikan akan muncul di sini</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')


    @role('admin')
        <script type="module">
            function deleteProject(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Project ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create form and submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/project/${id}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endrole
@endpush

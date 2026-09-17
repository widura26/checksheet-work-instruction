@extends('layouts.app')

@section('title', 'Bill Of Material')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }

        #projects-table {
            width: 100% !important;
        }

        .icon {
            vertical-align: middle;
        }
        .avatar-lg .icon {
            width: 24px;
            height: 24px;
        }
    </style>
@endpush

@section('content')

    <div class="card mb-4">
        <div class="p-3">
            {{-- Analytics Container --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="card" style="border-top: 3px solid var(--cui-primary);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Total BOM</h6>
                                    <h3 class="mb-0" id="total-bom">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <div class="avatar avatar-lg bg-primary bg-opacity-10 text-primary">
                                        <svg class="icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card" style="border-top: 3px solid var(--cui-success);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Total Proyek</h6>
                                    <h3 class="mb-0" id="total-proyek">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <div class="avatar avatar-lg bg-success bg-opacity-10 text-success">
                                        <svg class="icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card" style="border-top: 3px solid var(--cui-warning);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Jenis Material</h6>
                                    <h3 class="mb-0" id="total-jenis">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <div class="avatar avatar-lg bg-warning bg-opacity-10 text-warning">
                                        <svg class="icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M20 2H4c-1 0-2 .9-2 2v3.01c0 .72.43 1.34 1 1.69V20c0 1.1 1.1 2 2 2h14c.9 0 2-.9 2-2V8.7c.57-.35 1-.97 1-1.69V4c0-1.1-1-2-2-2zm-5 12H9v-2h6v2zm5-7H4V4h16v3z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card" style="border-top: 3px solid var(--cui-info);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Total Distribusi</h6>
                                    <h3 class="mb-0" id="total-distribusi">-</h3>
                                </div>
                                <div class="align-self-center">
                                    <div class="avatar avatar-lg bg-info bg-opacity-10 text-info">
                                        <svg class="icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Distribusi BOM per Proyek</div>
                <div class="card-body">
                    <div id="bom-distribusi-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Bill Of Material</h5>
            </div>
            @if (Auth::user()->hasRole('admin'))
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('bill-of-material.create') }}" class="btn btn-primary text-white">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg>
                        Tambah BOM
                    </a>
                    <button type="button" class="btn btn-success text-white" data-coreui-toggle="modal"
                        data-coreui-target="#importExcelModal">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg>
                        Import Excel
                    </button>
                </div>
            @endif
        </div>
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="table-responsive">
                <table id="projects-table" class="table table-bordered table-hover" style="width: 100%;">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Proyek</th>
                            <th>Nama Proyek</th>
                            <th>Nama BOM</th>
                            <th>Nomor</th>
                            <th>Tanggal Terbit</th>
                            <th>Revisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module" src="{{ asset('js/bill-of-material/index.js') }}"></script>
@endpush

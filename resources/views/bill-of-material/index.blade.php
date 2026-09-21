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

        #charts {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .pie-charts {
            padding: 20px;
            display: flex;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #fff;
        }

        .bar-charts {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #fff;
        }

        .chart-section {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: #fff;
        }

        .chart-section h5 {
            margin-bottom: 16px;
            font-weight: 600;
        }

        .chartjs-tooltip {
            display: none;
        }

        .buttons {
            display: flex;
            align-items: center;
            justify-content: end;
            gap: 10px;
            margin-bottom: 10px;
        }
    </style>
@endpush

@section('content')
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

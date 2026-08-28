@extends('layouts.app')

@section('title', 'Cutting Plan')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }
        .dataTables_scrollBody {
            overflow-x: auto !important;
        }
        #cutting-plan-table {
            width: 100% !important;
        }
        .icon {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Cutting Plan</h5>
            </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-danger d-none" type="button" id="btnBulkDelete">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M15.375 4.125v-2.25c0-0.631-0.445-1.125-1.013-1.125h-4.725c-0.568 0-1.013 0.494-1.013 1.125v2.25h-5.625v1.5h18v-1.5zM10.125 2.25h3.75v1.875h-3.75z"></path></svg> Hapus Terpilih (<span id="selectedCount">0</span>)
                    </button>
                    <button class="btn btn-success" type="button" id="btnExportExcel">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M13 2v9h7l-8 11v-9h-7z"></path></svg> Export Excel
                    </button>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="columnToggleBtn" data-coreui-toggle="dropdown" aria-expanded="false">
                        <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 5h18v2H3zM3 11h18v2H3zM3 17h18v2H3z"></path></svg> Kolom
                    </button>
                    <ul class="dropdown-menu p-2" id="columnToggleMenu" style="min-width: 220px; max-height: 320px; overflow-y: auto;">
                        <li class="text-muted small px-2 pb-1">Tampilkan / sembunyikan kolom</li>
                    </ul>
                </div>
                <a href="{{ route('cutting-plan.create') }}" class="btn btn-primary">
                    <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg> Tambah
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="cutting-plan-table" class="table table-bordered table-hover" style="width: 100%;">
                    <thead class="text-center">
                        <tr>
                            <th class="text-center" style="width: 40px;"><input type="checkbox" id="selectAll" title="Pilih semua"></th>
                            <th>No</th>
                            <th>No CP</th>
                            <th>Material</th>
                            <th>Kode Proyek</th>
                            <th>WBS</th>
                            <th>Material Size</th>
                            <th>Quantity</th>
                            <th>Cutting Method</th>
                            <th>Sket</th>
                            <th>Parts</th>
                            <th>Special Instruction</th>
                            <th>Date Issued</th>
                            <th>Date Revised</th>
                            <th>BPM Number</th>
                            <th>Date of BPM</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="crudForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="_method" value="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Tambah Cutting Plan</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('cutting-plan._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        window.CP = {
            routes: {
                materials: "{{ route('cutting-plan.materials') }}",
                projects: "{{ route('cutting-plan.projects') }}",
                processes: "{{ route('cutting-plan.processes') }}",
                users: "{{ route('cutting-plan.users') }}",
                data: "{{ route('cutting-plan.data') }}",
                export: "{{ route('cutting-plan.export') }}",
                bulkDestroy: "{{ route('cutting-plan.bulk-destroy') }}",
                update: "{{ route('cutting-plan.update', '__ID__') }}",
                destroy: "{{ route('cutting-plan.destroy', '__ID__') }}",
                barcodeCreate: "{{ route('barcode.create') }}"
            }
        };
    </script>
    <script type="module" src="{{ asset('js/cutting-plan/index.js') }}"></script>
@endpush

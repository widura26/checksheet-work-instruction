@extends('layouts.app')

@section('title', 'Dashboard Monitoring Purchase Requisition')

@push('css')
    <style>
        .st-dashboard {
            font-family: "Source Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #31333F;
            padding-bottom: 3rem;
        }

        .st-dashboard h1, .st-dashboard h2, .st-dashboard h3, .st-dashboard h4, .st-dashboard h5 {
            color: #31333F;
            font-family: inherit;
        }

        .st-page-title {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
            color: #262730;
        }

        /* Streamlit Expander */
        .st-expander {
            border: 1px solid rgba(49, 51, 63, 0.2);
            border-radius: 8px;
            background: #ffffff;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .st-expander-header {
            padding: 12px 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            user-select: none;
            transition: background-color 0.15s;
        }

        .st-expander-header:hover {
            background-color: #f8f9fa;
        }

        .st-expander-chevron {
            transition: transform 0.2s ease;
            color: #495057;
        }

        .st-expander-body {
            border-top: 1px solid rgba(49, 51, 63, 0.12);
            padding: 18px;
            background: #fafbfc;
        }

        /* Dropzone */
        .st-dropzone {
            border: 1px dashed rgba(49, 51, 63, 0.3);
            border-radius: 8px;
            background: #ffffff;
            transition: border-color 0.2s, background-color 0.2s;
        }

        .st-dropzone.dragover {
            border-color: #0068c9;
            background-color: #f0f7ff;
        }

        /* Streamlit Select */
        .st-select-label {
            font-size: 14px;
            font-weight: 500;
            color: #31333F;
            margin-bottom: 6px;
        }

        .st-select {
            background-color: #f0f2f6;
            border: 1px solid transparent;
            border-radius: 8px;
            color: #31333F;
            font-size: 14px;
            font-weight: 400;
            height: 42px;
            padding: 8px 14px;
            transition: all 0.2s;
        }

        .st-select:focus {
            background-color: #ffffff;
            border-color: #ff4b4b;
            box-shadow: 0 0 0 1px #ff4b4b;
            outline: none;
        }

        /* Streamlit Metric Card */
        .st-metric-card {
            background: #ffffff;
            border: 1px solid rgba(49, 51, 63, 0.2);
            border-radius: 8px;
            padding: 18px 24px;
            height: 100%;
        }

        .st-metric-label {
            font-size: 14px;
            color: #31333F;
            margin-bottom: 4px;
            font-weight: 400;
        }

        .st-metric-value {
            font-size: 2.75rem;
            font-weight: 700;
            color: #31333F;
            line-height: 1.1;
        }

        /* Streamlit Card Container */
        .st-card {
            background: #ffffff;
            border: 1px solid rgba(49, 51, 63, 0.2);
            border-radius: 8px;
            padding: 20px;
        }

        .st-card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #31333F;
            margin-bottom: 1rem;
        }

        /* Streamlit Switch Toggle */
        .form-check-input.st-switch {
            width: 38px;
            height: 20px;
            cursor: pointer;
            border-color: rgba(49, 51, 63, 0.3);
        }

        .form-check-input.st-switch:checked {
            background-color: #ff4b4b;
            border-color: #ff4b4b;
        }

        .form-check-input.st-switch:focus {
            box-shadow: 0 0 0 1px #ff4b4b;
            border-color: #ff4b4b;
        }

        .st-switch-label {
            font-size: 14px;
            font-weight: 500;
            color: #31333F;
            cursor: pointer;
            user-select: none;
        }

        /* Streamlit Table */
        .st-table {
            font-size: 13px;
            border-collapse: collapse;
        }

        .st-table thead th {
            background-color: #f8f9fb;
            color: #31333F;
            font-weight: 600;
            border-bottom: 1px solid rgba(49, 51, 63, 0.15);
            padding: 10px 14px;
            white-space: nowrap;
        }

        .st-table tbody td {
            color: #31333F;
            border-color: rgba(49, 51, 63, 0.1);
            padding: 9px 14px;
            vertical-align: middle;
        }

        .st-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badges */
        .st-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .st-badge-success {
            background-color: rgba(0, 204, 150, 0.16);
            color: #008761;
            border: 1px solid rgba(0, 204, 150, 0.3);
        }

        .st-badge-danger {
            background-color: rgba(239, 85, 59, 0.16);
            color: #b91c1c;
            border: 1px solid rgba(239, 85, 59, 0.3);
        }

        .st-badge-purple {
            background-color: rgba(171, 99, 250, 0.16);
            color: #6d28d9;
            border: 1px solid rgba(171, 99, 250, 0.3);
        }

        .st-badge-warning {
            background-color: rgba(245, 158, 11, 0.16);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .st-badge-secondary {
            background-color: rgba(127, 127, 127, 0.16);
            color: #4b5563;
            border: 1px solid rgba(127, 127, 127, 0.3);
        }

        /* DataTables adjustments */
        .dataTables_wrapper {
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 10px;
            border-radius: 6px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 6px;
            border: 1px solid #d1d5db;
            padding: 4px 8px;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div class="st-dashboard">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="st-page-title mb-0">Dashboard Monitoring</h1>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-coreui-toggle="modal" data-coreui-target="#importPrModal">
                    <svg width="15" height="15" class="me-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Import PR (.xls)
                </button>
                <form method="POST" action="{{ route('sap-dataset.delete') }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh dataset SAP?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <svg width="15" height="15" class="me-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Reset Dataset
                    </button>
                </form>
            </div>
        </div>

        <div class="st-expander">
            <div class="st-expander-header" id="expanderToggle">
                <div class="d-flex align-items-center gap-2">
                    <svg class="st-expander-chevron" id="expanderChevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transform: rotate(180deg);">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                    <span class="fw-semibold" style="font-size: 15px; color: #31333F;">Upload Daftar Kebutuhan Material</span>
                </div>
                <span class="text-muted small">Excel (.xlsx / .xls)</span>
            </div>
            <div class="st-expander-body" id="expanderBody">
                <!-- Kriteria File -->
                <div class="mb-3 p-3 rounded-2" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <h6 class="fw-bold mb-2 text-dark" style="font-size: 13px;">Kriteria File:</h6>
                    <ul class="mb-0 text-muted small ps-3">
                        <li class="mb-1"><strong>Format:</strong> Excel (<code>.xlsx</code> / <code>.xls</code>) maksimal <strong>25 MB</strong>.</li>
                        <li>Gunakan <a href="https://docs.google.com/spreadsheets/d/1b3kH9CyGzWNEiYSRL7enafyEO8jhaGVbz7DuTQrOZ1k/edit?usp=sharing" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none fw-semibold">Template</a> agar kolom terdeteksi dengan baik.</li>
                    </ul>
                </div>

                <!-- Upload Area -->
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold small d-flex align-items-center gap-1 mb-2">
                        Pilih File
                        <span class="text-muted" title="Upload file Excel BOM"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></span>
                    </label>

                    <!-- Dropzone -->
                    <div class="st-dropzone p-4 text-center rounded-3" id="bomDropzone">
                        <input type="file" id="bomFileInput" class="d-none" accept=".xlsx,.xls,.csv">
                        <div class="d-flex flex-column align-items-center justify-content-center py-2">
                            <div class="mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-semibold" onclick="document.getElementById('bomFileInput').click()">
                                    <svg width="16" height="16" class="me-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    Upload
                                </button>
                            </div>
                            <div class="text-secondary small" id="bomFileLabel">25MB per file • XLS, XLSX</div>
                        </div>
                    </div>
                </div>

                <!-- Action button and info -->
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <button type="button" class="btn btn-primary text-white fw-semibold px-4 py-2 rounded-2" id="btnStreamlitGenerate" style="background-color: #0068c9; border-color: #0068c9;">
                        ⚙️ Generate & Rekap Data
                    </button>
                    <span class="text-muted small" id="bomStatusText">Belum ada file diunggah.</span>
                </div>

                <!-- Progress / Result container -->
                <div id="bomStreamlitProgress" class="d-none mt-3">
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 100%"></div>
                    </div>
                </div>
                <div id="bomStreamlitResult" class="d-none mt-3"></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="st-select-label d-block">Pilih Proyek</label>
                <select id="select-proyek" class="form-select st-select">
                    <option value="Semua" selected>Semua</option>
                    <option value="S-22002.1">S-22002.1</option>
                    <option value="S-22002.2">S-22002.2</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="st-select-label d-block">Pilih Group</label>
                <select id="select-group" class="form-select st-select">
                    <option value="Semua" selected>Semua</option>
                    <option value="KOMPONEN">KOMPONEN</option>
                    <option value="PIECE PART">PIECE PART</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="st-select-label d-block">Pilih Trainset</label>
                <select id="select-trainset" class="form-select st-select">
                    <option value="Semua" selected>Semua</option>
                    <option value="TANPA TRAINSET">TANPA TRAINSET</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="st-metric-card">
                    <div class="st-metric-label">Total Item</div>
                    <div class="st-metric-value" id="kpi-total-item">215</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="st-metric-card">
                    <div class="st-metric-label">Jumlah Material</div>
                    <div class="st-metric-value" id="kpi-jumlah-material">215</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="st-card h-100">
                    <h5 class="st-card-title">Group</h5>
                    <div style="position: relative; height: 320px;">
                        <canvas id="chart-group"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="st-card h-100">
                    <h5 class="st-card-title">Status PR</h5>
                    <div style="position: relative; height: 320px;">
                        <canvas id="chart-status-pr"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="st-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="st-card-title mb-0">Jumlah per Status PR</h5>
                <div class="text-muted small">
                    <span class="badge bg-light text-secondary border">Rekap Matriks</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle st-table mb-0" id="table-status-pr">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Status PR</th>
                            <th class="text-end" style="width: 25%;">KOMPONEN</th>
                            <th class="text-end" style="width: 25%;">PIECE PART</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">BELUM PR</td>
                            <td class="text-end" id="sum-belum-komp">0</td>
                            <td class="text-end" id="sum-belum-piece">3</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">PR PARSIAL</td>
                            <td class="text-end" id="sum-parsial-komp">0</td>
                            <td class="text-end" id="sum-parsial-piece">0</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">SUDAH PR</td>
                            <td class="text-end" id="sum-sudah-komp">1</td>
                            <td class="text-end" id="sum-sudah-piece">209</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">STOK DAN PR</td>
                            <td class="text-end" id="sum-stokpr-komp">0</td>
                            <td class="text-end" id="sum-stokpr-piece">0</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">STOK</td>
                            <td class="text-end" id="sum-stok-komp">0</td>
                            <td class="text-end" id="sum-stok-piece">1</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">STATUS LAIN / TIDAK DIKENAL</td>
                            <td class="text-end" id="sum-lain-komp">0</td>
                            <td class="text-end" id="sum-lain-piece">1</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="st-card p-3 mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-belum-pr" data-status="BELUM PR">
                        <label class="form-check-label st-switch-label" for="switch-belum-pr">BELUM PR</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-pr-parsial" data-status="PR PARSIAL">
                        <label class="form-check-label st-switch-label" for="switch-pr-parsial">PR PARSIAL</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-sudah-pr" data-status="SUDAH PR">
                        <label class="form-check-label st-switch-label" for="switch-sudah-pr">SUDAH PR</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-stok-dan-pr" data-status="STOK DAN PR">
                        <label class="form-check-label st-switch-label" for="switch-stok-dan-pr">STOK DAN PR</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-stok" data-status="STOK">
                        <label class="form-check-label st-switch-label" for="switch-stok">STOK</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check form-switch d-flex align-items-center gap-2">
                        <input class="form-check-input st-switch status-filter-switch" type="checkbox" role="switch" id="switch-status-lain" data-status="STATUS LAIN / TIDAK DIKENAL">
                        <label class="form-check-label st-switch-label" for="switch-status-lain">STATUS LAIN / TIDAK DIKENAL</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="st-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="st-card-title mb-0">Dataset Bill Of Materials (<span id="bomCountBadge">215</span>)</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light border py-1 px-3 d-flex align-items-center gap-1 fw-semibold text-secondary" id="btnExportBomCsv" title="Download CSV">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Download CSV
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table id="bom-table" class="table table-bordered table-hover align-middle st-table w-100">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th class="text-center">NO</th>
                            <th>Kode Material Konversi</th>
                            <th>Group</th>
                            <th>Tindak Lanjut</th>
                            <th>Deskripsi Material</th>
                            <th class="text-end">Kebutuhan</th>
                            <th class="text-end">Qty PR (BOM)</th>
                            <th>Kode Material</th>
                            <th>Trainset</th>
                            <th class="text-center">Status PR</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="importPrModal" tabindex="-1" aria-labelledby="importPrModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3 border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="importPrModalLabel" style="font-size: 16px;">Import Purchase Requisition</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="importForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="importFile" class="form-label small fw-semibold">Pilih File Excel SAP</label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text small">Format: .xlsx, .xls, atau .csv (Maksimal 25MB)</div>
                        </div>
                    </form>
                    <div id="importProgress" class="d-none">
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                    <div id="importResult" class="d-none"></div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-sm btn-success text-white px-3" id="btnImport">Import</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module" src="{{ asset('js/sap-dataset/index.js') }}"></script>
    <script src="{{ asset('js/bom-import.js') }}"></script>
@endpush

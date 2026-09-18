@extends('layouts.app')

@section('title', 'Material Planning')

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
        <div class="p-3">
            <div class="buttons">
                <form method="POST" action="{{ route("material-planning.delete") }}">
                    @csrf
                    <button>Delete</button>
                </form>
                <button type="button" class="btn btn-primary text-white" data-coreui-toggle="modal"
                    data-coreui-target="#importExcelModal">
                    <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg>
                    Import Bill Of Material (.xls)
                </button>
                <button type="button" class="btn btn-success text-white" data-coreui-toggle="modal"
                    data-coreui-target="#importExcelModal">
                    <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg>
                    Import Purchase Requisition (.xls)
                </button>
            </div>

            <!-- Import Excel Modal -->
            <div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importExcelModalLabel">Import Purchase Requisition</h5>
                            <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="importForm" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="importFile" class="form-label">Pilih File Excel</label>
                                    <input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                                    <div class="form-text">Format: .xlsx, .xls, atau .csv</div>
                                </div>
                            </form>
                            <div id="importProgress" class="d-none">
                                <div class="progress mb-2">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%">Loading...</div>
                                </div>
                            </div>
                            <div id="importResult" class="d-none"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-success" id="btnImport">Import</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-3 col-sm-6">
                    <div class="card" style="border-top: 3px solid var(--cui-primary);">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Total Purchase Requisition</h6>
                                    <h3 class="mb-0" id="total-bom">{{ count($prtotals) }}</h3>
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

            <div id="charts">
                <div class="">
                    <h5>Purchase Requisition</h5>
                    <div class="pie-charts">
                        <div class="pie-chart-pr-po">
                            <canvas id="canvas-5"></canvas>
                        </div>
                    </div>
                </div>
                <div class="">
                    <h5>Purchase Order</h5>
                    <div class="pie-charts">
                        <div class="pie-chart-pr-po">
                            <canvas id="canvas-6"></canvas>
                        </div>
                    </div>
                </div>
                <div class="bar-charts">
                    <div class="bar-chart-pr-po">
                        <canvas id="canvas-7"></canvas>
                    </div>
                </div>
                <div class="chart-section">
                    <h5>Bill Of Material & Purchase Requisition Difference</h5>
                    <div class="pie-chart-pr-po">
                        <canvas id="canvas-8" style="width: 100%"></canvas>
                    </div>
                </div>
                <div class="chart-section">
                    <h5>Purchase Requisition & Purchase Order Difference</h5>
                    <div class="pie-chart-pr-po">
                        <canvas id="canvas-9" style="width: 100%"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('js')
    <script type="module" src="{{ asset('js/material-planning/index.js') }}"></script>
@endpush

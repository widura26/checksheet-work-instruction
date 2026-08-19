@extends('layouts.app')

@section('title', 'Detail Purchase Requisition #' . $purchaseRequisition->no_pr)

@section('content')
    <div class="row">
        {{-- Header Information --}}
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Purchase Requisition</h5>
                    <div>
                        <a href="{{ route('pr.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>No. PR</strong></td>
                                    <td>: {{ $purchaseRequisition->no_pr }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal PR</strong></td>
                                    <td>: {{ \Carbon\Carbon::parse($purchaseRequisition->tanggal_pr)->format('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>:
                                        <span
                                            class="badge {{ $purchaseRequisition->status_pr == 'sudah_datang' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $purchaseRequisition->status_pr == 'sudah_datang' ? 'Sudah Datang' : 'Belum Datang' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Dibuat</strong></td>
                                    <td>: {{ $purchaseRequisition->created_at->format('d F Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate</strong></td>
                                    <td>: {{ $purchaseRequisition->updated_at->format('d F Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Items</strong></td>
                                    <td>: <span class="badge bg-info">{{ $purchaseRequisition->details->count() }}
                                            Items</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Items --}}
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title mb-0">Detail Tools yang Diminta</h6>
                </div>
                <div class="card-body">
                    @if ($purchaseRequisition->details->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>No. SPR</th>
                                        <th>Tanggal SPR</th>
                                        <th>Kode Material</th>
                                        <th>Deskripsi</th>
                                        <th>Deskripsi Kerusakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchaseRequisition->details as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detail->detailKerusakanTool->no_spr ?? '-' }}</td>
                                            <td>
                                                {{ $detail->detailKerusakanTool->tanggal_spr ? \Carbon\Carbon::parse($detail->detailKerusakanTool->tanggal_spr)->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <strong>{{ $detail->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->tool->kode_material ?? '-' }}</strong>
                                            </td>
                                            <td>{{ $detail->detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->tool->deskripsi ?? '-' }}
                                            </td>
                                            <td>
                                                <div class="text-wrap" style="max-width: 250px;">
                                                    {{ $detail->detailKerusakanTool->deskripsi_kerusakan }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Tidak ada detail items dalam PR ini.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title mb-0">Ringkasan</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary">{{ $purchaseRequisition->details->count() }}</h4>
                                <small class="text-muted">Total Items</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-danger">
                                    {{ $purchaseRequisition->details->whereHas('detailKerusakanTool', function ($q) {
                                            $q->where('status_perbaikan', 'tidak_bisa_diperbaiki');
                                        })->count() }}
                                </h4>
                                <small class="text-muted">Tidak Bisa Diperbaiki</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success">
                                    {{ $purchaseRequisition->details->whereHas('detailKerusakanTool', function ($q) {
                                            $q->where('status_perbaikan', 'selesai');
                                        })->count() }}
                                </h4>
                                <small class="text-muted">Selesai Diperbaiki</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h4 class="text-warning">
                                    {{ $purchaseRequisition->details->whereHas('detailKerusakanTool', function ($q) {
                                            $q->where('status_perbaikan', 'sedang_diperbaiki');
                                        })->count() }}
                                </h4>
                                <small class="text-muted">Sedang Diperbaiki</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection

@push('css')
    <style>
        .table td {
            vertical-align: middle;
        }

        .border.rounded.p-3 {
            transition: all 0.3s ease;
        }

        .border.rounded.p-3:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
    </style>
@endpush

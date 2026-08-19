@extends('layouts.app')

@section('title', 'Detail SPM')

@section('content')

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            Detail Surat Permintaan Material (SPM)
            <a href="{{ route('spm.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 30%;">No. SPM</th>
                            <td>: {{ $spm->no_spm }}</td>
                        </tr>
                        <tr>
                            <th>Project</th>
                            <td>: {{ $spm->project->nama_project }}</td>
                        </tr>
                        <tr>
                            <th>Posisi</th>
                            <td>: {{ $spm->posisiCt->nama_posisi_ct }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 30%;">Kepada</th>
                            <td>: {{ $spm->kepada }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>: {{ \Carbon\Carbon::parse($spm->tanggal_spm)->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $statusClass = [
                                        'WAITING' => 'badge bg-warning text-white',
                                        'PARTIAL' => 'badge bg-info text-white',
                                        'COMPLETED' => 'badge bg-success',
                                    ];
                                @endphp
                                <span
                                    class="{{ $statusClass[$spm->status] ?? 'badge bg-secondary' }}">{{ $spm->status }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Destinasi</th>
                            <td>
                                @php
                                    $statusClass = [
                                        'workshop' => 'badge bg-primary text-white',
                                        'ct' => 'badge bg-success text-white',
                                    ];
                                @endphp
                                <span
                                    class="{{ $statusClass[$spm->destinasi] ?? 'badge bg-secondary' }}">{{ strtoupper($spm->destinasi) }}</span>
                            </td>
                        </tr>
                        @if ($spm->destinasi == 'workshop')
                            <tr>
                                <th>Departemen</th>
                                <td>: {{ $spm->departemen->nama_departemen }}</td>
                            </tr>
                            <tr>
                                <th>Bagian</th>
                                <td>: {{ $spm->bagian->nama_bagian }}</td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td>: {{ $spm->unit->nama_unit }}</td>
                            </tr>
                        @endif
                        <tr></tr>
                    </table>
                </div>
            </div>

            <h5>Item Permintaan</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            <th>Satuan</th>
                            <th class="text-center">Qty Diminta</th>
                            <th class="text-center">Qty Diterima</th>
                            <th class="text-center">Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spm->items as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->kode_material }}</td>
                                <td>{{ $item->deskripsi }}</td>
                                <td>{{ $item->spesifikasi }}</td>
                                <td>{{ $item->satuan ?? '-' }}</td>
                                <td class="text-center">{{ $item->requested_qty }}</td>
                                <td class="text-center">
                                    {{ $item->itemHistory->sum('qty') }}</td>
                                <td class="text-center">
                                    {{ $item->kategori }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada item pada SPM ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

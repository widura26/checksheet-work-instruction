@extends('layouts.app')

@section('title', 'Detail Riwayat Material: ' . $tool->deskripsi)

@section('content')
    <!-- Tool Details Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Informasi Material</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode Material:</strong> {{ $tool->kode_material }}</p>
                    <p><strong>Deskripsi:</strong> {{ $tool->deskripsi }}</p>
                    <p><strong>Spesifikasi:</strong> {{ $tool->spesifikasi ?: '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Project :</strong> {{ $tool->stockProject[0]->nama_project }}</p>
                    <p><strong>Stok Saat Ini:</strong>
                        {{ rtrim(rtrim(number_format($tool->stockProject[0]->pivot->qty, 4, '.', ''), '0'), '.') }}
                        {{ $tool->satuan }}</p>
                    <p><strong>Posisi:</strong> {{ $tool->posisiCT->nama_posisi_ct ?? 'N/A' }}</p>
                    <p><strong>Vendor:</strong> {{ $tool->vendor ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- History Sections -->
    <div class="row">

        <!-- penambahan stok History -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Penambahan Stok</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Ditambah</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($updateHistory as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $item->qty_update }} {{ $tool->satuan }}</td>
                                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada riwayat penambahan stok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Pengurangan Stok</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Dikurangi</th>
                                    <th>Alasan</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penguranganHistory as $item)
                                    <tr>
                                        <td>{{ $item->tanggal_pengurangan->format('d M Y H:i') }}</td>
                                        <td>{{ $item->qty_dikurangi }} {{ $tool->satuan }}</td>
                                        <td>{{ $item->alasan }}</td>
                                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada riwayat pengurangan stok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- transfer posting history --}}
        <div class="col-lg-12 my-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Transfer Posting</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No. Transfer</th>
                                    <th>Ke Project</th>
                                    <th>Total</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transferHistory as $history)
                                    <tr>
                                        <td>{{ $history->id }}</td>
                                        <td>{{ $history->posting->toProject->nama_project }}</td>
                                        <td>{{ $history->qty }} {{ $tool->satuan }}</td>
                                        <td>{{ $history->posting->transfer_date
                                            ? \Carbon\Carbon::parse($history->posting->transfer_date)->format('d M Y')
                                            : '-' }}
                                        </td>
                                        <td>
                                            @switch($history->posting->status)
                                                @case('ON_LOAN')
                                                    <span>DIPINJAM</span>
                                                @break

                                                @case('PARTIAL_RETURN')
                                                    <span>DIKEMBALIKAN SEBAGIAN</span>
                                                @break

                                                @case('RETURNED')
                                                    <span>SUDAH DIKEMBALIKAN</span>
                                                @break

                                                @case('CANCELED')
                                                    <span>DIBATALKAN</span>
                                                @break

                                                @case('WRITTEN_OFF')
                                                    <span>TIDAK DIKEMBALIKAN</span>
                                                @break

                                                @default
                                                    <span>-</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada riwayat Transfer Posting.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Distribution History -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Distribusi</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Peminjam/Penerima</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah</th>
                                        <th>Satuan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($distribusiHistory->daftarDistribusiTools as $item)
                                        <tr>
                                            <td>
                                                @php
                                                    $type = $item->distribusiTool->type;
                                                @endphp

                                                <span
                                                    class="badge 
        {{ $type === 'peminjaman' ? 'bg-success' : ($type === 'penyerahan' ? 'bg-primary' : 'bg-secondary') }}">
                                                    {{ Str::title(str_replace('_', ' ', $type)) }}
                                                </span>
                                            </td>

                                            <td>{{ $item->distribusiTool->nama_peminjam ?? 'N/A' }}</td>
                                            <td>{{ $item->distribusiTool->tanggal_pinjam_diserahkan->format('d M Y') }}
                                            </td>
                                            <td>{{ $item->jumlah_distribusi_formatted }}</td>
                                            <td>{{ $tool->satuan }}</td>
                                            <td>
                                                <a href="{{ route('distribusi.show', $item->distribusiTool->public_id) }}"
                                                    class="btn btn-sm btn-info text-white">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada riwayat distribusi.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Damage History -->
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Riwayat Kerusakan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal Pengembalian</th>
                                        <th>Deskripsi Kerusakan</th>
                                        <th>Status Pengajuan SPR</th>
                                        <th>Status Perbaikan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($damageHistory as $item)
                                        <tr>
                                            <td>{{ $item->created_at->format('d M Y') }}</td>
                                            <td>{{ $item->deskripsi_kerusakan }}</td>
                                            <td>{{ Str::title(str_replace('_', ' ', $item->status_pengajuan_spr)) }}</td>
                                            <td>{{ Str::title(str_replace('_', ' ', $item->status_perbaikan)) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada riwayat kerusakan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

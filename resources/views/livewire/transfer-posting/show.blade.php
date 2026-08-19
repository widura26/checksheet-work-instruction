<div>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Detail Transfer Posting</h1>
            <a href="{{ route('transfer-posting.index') }}" class="btn btn-secondary shadow-sm" wire:navigate>
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Transfer</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nomor Transfer:</strong> {{ $materialTransferPosting->id }}</p>
                                <p><strong>Dari Project:</strong>
                                    {{ $materialTransferPosting->fromProject->nama_project ?? 'N/A' }}</p>
                                <p><strong>Ke Project:</strong>
                                    {{ $materialTransferPosting->toProject->nama_project ?? 'N/A' }}</p>
                                <p><strong>Dibuat Oleh:</strong>
                                    {{ $materialTransferPosting->createdBy->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tanggal Transfer:</strong>
                                    {{ \Carbon\Carbon::parse($materialTransferPosting->transfer_date)->format('d F Y') }}
                                </p>
                                <p><strong>Dari CT:</strong>
                                    {{ $materialTransferPosting->fromCt->nama_posisi_ct ?? 'N/A' }}</p>
                                <p><strong>Ke CT:</strong>
                                    {{ $materialTransferPosting->toCt->nama_posisi_ct ?? 'N/A' }}</p>
                                <p><strong>Status:</strong>
                                    @if ($materialTransferPosting->status == 'ON_LOAN')
                                        <span class="badge bg-warning text-dark">BELUM DIKEMBALIKAN</span>
                                    @elseif($materialTransferPosting->status == 'PARTIAL_RETURN')
                                        <span class="badge bg-info text-white">DI KEMBALIKAN SEBAGIAN</span>
                                    @elseif($materialTransferPosting->status == 'RETURNED')
                                        <span class="badge bg-success text-white">DI KEMBALIKAN</span>
                                    @elseif($materialTransferPosting->status == 'CANCELED')
                                        <span class="badge bg-danger text-white">DI BATALKAN</span>
                                    @else
                                        <span class="badge bg-secondary">{{ 'TIDAK DI KEMBALIKAN' }}</span>
                                    @endif
                                </p>
                                @if ($materialTransferPosting->returned_at)
                                    <p><strong>Tanggal Selesai Pengembalian:</strong>
                                        {{ \Carbon\Carbon::parse($materialTransferPosting->returned_at)->format('d F Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rincian Item</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Kode Material</th>
                                <th>Deskripsi Material</th>
                                <th>Jenis</th>
                                <th>Posisi</th>
                                <th class="text-center">Total Pinjam</th>
                                <th class="text-center">Sudah Kembali</th>
                                <th class="text-center">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($materialTransferPosting->items as $item)
                                <tr>
                                    <td>{{ $item->fromTool->kode_material ?? 'N/A' }}</td>
                                    <td>{{ $item->fromTool->deskripsi ?? 'N/A' }}</td>
                                    <td>{{ $item->fromTool->jenis_material ?? 'N/A' }}</td>
                                    <td>{{ $materialTransferPosting->fromCt->nama_posisi_ct ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-center">{{ $item->returned_qty }}</td>
                                    <td class="text-center">{{ $item->qty - $item->returned_qty }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada item.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat Pengembalian</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tanggal Pengembalian</th>
                                <th>Status</th>
                                <th>Detail Pengembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($materialTransferPosting->histories->where('action', 'RETURNED') as $history)
                                <tr>
                                    <td>{{ $history->returned_at ? \Carbon\Carbon::parse($history->returned_at)->format('d F Y H:i') : $history->created_at->format('d F Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ 'DI KEMBALIKAN' }}</span>
                                    </td>
                                    <td>
                                        @if ($history->items->isNotEmpty())
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th class="text-center">Jumlah</th>
                                                        <th>Di Kembalikan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($history->items as $item)
                                                        <tr>
                                                            <td>{{ $item->tool->kode_material ?? 'N/A' }}</td>
                                                            <td class="text-center">{{ $item->qty_returned }}</td>
                                                            <td>{{ $item->createdBy->name ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            {{ $history->notes ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada riwayat pengembalian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

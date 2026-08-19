@extends('layouts.app')

@section('title', 'Detail Distribusi #' . $distribusi->id)

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Detail Distribusi #{{ $distribusi->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Tipe Distribusi:</strong> {{ ucfirst($distribusi->type) }}</p>
                    <p><strong>Staff:</strong> {{ $distribusi->user->name }}</p>
                    <p><strong>Penerima:</strong> {{ $distribusi->nama_peminjam }}</p>
                    <p><strong>NIP:</strong> {{ $distribusi->nip }}</p>
                    <p><strong>Tanggal Distribusi:</strong>
                        {{ $distribusi->tanggal_pinjam_diserahkan->format('d M Y H:i') }}
                    </p>
                    <p>
                        <strong>Status:</strong>
                        @if ($distribusi->type === 'peminjaman' && $distribusi->status === 'kembali')
                            <span class="badge bg-success ms-2">Selesai</span>
                            <br>
                            <p class="ms-2">Telah Dikembalikan Semua Pada Tanggal :
                                <span class="badge bg-success ms-2">
                                    {{ $distribusi->tanggal_kembali->format('d M Y H:i') }}
                                </span>
                            </p>
                        @elseif ($distribusi->type === 'peminjaman' && $distribusi->status === 'sebagian_dikembalikan')
                            <span class="badge bg-warning text-dark ms-2">Sebagian Dikembalikan</span>
                        @endif

                        @if ($distribusi->type == 'penyerahan')
                            <span class="badge bg-secondary ms-2">Diserahkan</span>
                        @endif
                    </p>
                    @if ($distribusi->type == 'peminjaman' && $distribusi->status == 'selesai')
                        <p><strong>Tanggal Kembali:</strong>
                            {{ \Carbon\Carbon::parse($distribusi->tanggal_kembali)->format('d M Y H:i') }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p><strong>Departemen:</strong> {{ $distribusi->departemen->nama_departemen }}</p>
                    <p><strong>Bagian:</strong> {{ $distribusi->bagian->nama_bagian }}</p>
                    @if ($distribusi->unit)
                        <p><strong>Unit:</strong> {{ $distribusi->unit->nama_unit }}</p>
                    @endif
                    @if ($distribusi->project)
                        <p><strong>Asal Project:</strong> {{ $distribusi->project->nama_project }}</p>
                    @endif
                    @if ($distribusi->projectDest)
                        <p><strong>Tujuan Project:</strong> {{ $distribusi->projectDest->nama_project }}</p>
                    @endif
                    <p><strong>Keterangan:</strong> {{ $distribusi->keterangan ?? '-' }}</p>
                </div>
            </div>

            <hr>

            <h5>Daftar Item Distribusi</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Material</th>
                            <th>Deskripsi</th>
                            <th>Spesifikasi</th>
                            <th>Lokasi CT</th>
                            <th>Qty {{ $distribusi->type == 'peminjaman' ? 'Pinjam' : 'Diserahkan' }}</th>
                            <th>Satuan</th>
                            @if ($distribusi->type == 'peminjaman')
                                <th>Qty Kembali</th>
                                <th>Detail Pengembalian</th>
                                <th>Detail Kerusakan</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($distribusi->daftarDistribusiTool as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->tool->kode_material }}</td>
                                <td>{{ $item->tool->deskripsi ?? 'N/A' }}</td>
                                <td>{{ $item->tool->spesifikasi ?? 'N/A' }}</td>
                                <td>{{ $item->tool->posisiCT->nama_posisi_ct ?? 'N/A' }}</td>
                                <td class="text-center">{{ $item->jumlah_distribusi_formatted }}</td>
                                <td>{{ $item->tool->satuan }}</td>
                                @if ($distribusi->type == 'peminjaman')
                                    <td class="text-center">{{ $item->qty_kembali ?? 0 }}</td>
                                    <td>
                                        @forelse ($item->details->groupBy(function ($detail) {
                                                                                                                                                                        return \Carbon\Carbon::parse($detail->tanggal_kembali)->format('Y-m-d H:i');
                                                                                                                                                                    }) as $tanggal => $detailsGroup)
                                            <div class="mb-2 p-2 border rounded bg-light">
                                                <strong class="d-block mb-1 border-bottom pb-1">
                                                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y H:i') }}
                                                </strong>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($detailsGroup as $detail)
                                                        <li>
                                                            <span
                                                                class="badge bg-{{ $detail->kondisi == 'baik' ? 'success' : 'danger' }} me-1">
                                                                {{ ucfirst($detail->kondisi) }}: {{ $detail->jumlah }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @empty
                                            <span class="text-muted">Belum ada pengembalian</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @php
                                            $damagedItemsDetails = $item->details->where('kondisi', 'rusak');
                                        @endphp

                                        @forelse ($damagedItemsDetails as $detail)
                                            @if ($detail->detailKerusakanTools->isNotEmpty())
                                                <div class="mb-2 p-2 border rounded bg-light">
                                                    <strong class="d-block mb-1 border-bottom pb-1">
                                                        Pengembalian
                                                        {{ \Carbon\Carbon::parse($detail->tanggal_kembali)->translatedFormat('d M Y') }}
                                                    </strong>
                                                    <ul class="list-unstyled mb-0 small">
                                                        @foreach ($detail->detailKerusakanTools as $kerusakan)
                                                            <li>- {{ $kerusakan->deskripsi_kerusakan }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('distribusi.index') }}" class="btn btn-secondary">Kembali</a>
                @if ($distribusi->type == 'peminjaman' && $distribusi->status != 'kembali')
                    <a href="{{ route('distribusi.peminjaman.kembali', $distribusi->public_id) }}"
                        class="btn btn-warning ms-2">Proses Pengembalian</a>
                @endif
            </div>
        </div>
    </div>
@endsection

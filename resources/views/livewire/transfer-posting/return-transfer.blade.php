<div>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Pengembalian Transfer Posting</h1>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Transfer Posting</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Transfer</th>
                                <th>Proyek Asal</th>
                                <th>Proyek Peminjam</th>
                                <th>Tanggal</th>
                                <th>Sisa Pengembalian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($postings as $index => $posting)
                                <tr>
                                    <td>{{ $postings->firstItem() + $index }}</td>
                                    <td>{{ $posting->id }}</td>
                                    <td>{{ $posting->fromProject->nama_project ?? 'N/A' }}</td>
                                    <td>{{ $posting->toProject->nama_project ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($posting->transfer_date)->format('d F Y') }}</td>
                                    <td>{{ $posting->items->sum('qty') - $posting->items->sum('returned_qty') }}
                                    </td>
                                    <td>
                                        @if ($posting->status == 'ON_LOAN')
                                            <span class="badge bg-warning text-dark">BELUM DIKEMBALIKAN</span>
                                        @elseif ($posting->status == 'PARTIAL_RETURN')
                                            <span class="badge bg-info text-dark">DI KEMBALIKAN SEBAGIAN</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (Auth::user()->hasRole(['admin', 'staff']))
                                            <button class="btn btn-primary btn-sm"
                                                wire:click="startReturn({{ $posting->id }})">
                                                <i class="fas fa-undo fa-sm text-white-50"></i> Proses Pengembalian
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $postings->links() }}
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->hasRole(['admin', 'staff']))
        @if ($showModal)
            <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1"
                role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-primary font-weight-bold">Proses Pengembalian -
                                {{ $selectedPosting->id ?? '' }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModal"
                                aria-label="Close"></button>
                        </div>
                        <form wire:submit.prevent="processReturn">
                            <div class="modal-body">
                                @if ($errors->has('general'))
                                    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
                                @endif

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Proyek Asal:</strong>
                                        {{ $selectedPosting->fromProject->nama_project ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Proyek Peminjam:</strong>
                                        {{ $selectedPosting->toProject->nama_project ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>CT Asal:</strong>
                                        {{ $selectedPosting->fromCt->nama_posisi_ct ?? 'N/A' }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>CT Peminjam:</strong>
                                        {{ $selectedPosting->toCt->nama_posisi_ct ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Kode Material</th>
                                                <th>Deskripsi</th>
                                                <th class="text-center">Total Pinjam</th>
                                                <th class="text-center">Sudah Kembali</th>
                                                <th class="text-center">Sisa</th>
                                                <th style="width: 150px;">Jml Dikembalikan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($returnItems as $itemId => $item)
                                                <tr>
                                                    <td>{{ $item['kode_material'] }}</td>
                                                    <td>{{ $item['nama_tool'] }}</td>
                                                    <td class="text-center">{{ $item['qty_dipinjam'] }}</td>
                                                    <td class="text-center">{{ $item['qty_sudah_kembali'] }}</td>
                                                    <td class="text-center">{{ $item['qty_sisa'] }}</td>
                                                    <td>
                                                        <input type="number"
                                                            class="form-control @error('returnItems.' . $itemId . '.qty_akan_dikembalikan') is-invalid @enderror"
                                                            wire:model.live="returnItems.{{ $itemId }}.qty_akan_dikembalikan"
                                                            max="{{ $item['qty_sisa'] }}" min="0">
                                                        @error('returnItems.' . $itemId . '.qty_akan_dikembalikan')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="processReturn">
                                        <i class="fas fa-save"></i> Submit Pengembalian
                                    </span>
                                    <span wire:loading wire:target="processReturn">
                                        <i class="fas fa-spinner fa-spin"></i> Memproses...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

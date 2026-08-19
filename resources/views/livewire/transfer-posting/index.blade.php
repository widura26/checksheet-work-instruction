<div>
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Transfer Posting</h1>
            @if (Auth::user()->hasRole(['admin', 'staff']))
                <a href="{{ route('transfer-posting.create') }}" class="btn btn-primary shadow-sm" wire:navigate>
                    <i class="fas fa-plus fa-sm text-white-50"></i> Buat Transfer Baru
                </a>
            @endif
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filter Transfer</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="startDate">Tanggal Mulai</label>
                            <input type="date" wire:model.live="startDate" class="form-control" id="startDate">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="endDate">Tanggal Akhir</label>
                            <input type="date" wire:model.live="endDate" class="form-control" id="endDate">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fromProjectId">Dari Project</label>
                            <select wire:model.live="fromProjectId" class="form-control" id="fromProjectId">
                                <option value="">Semua Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="toProjectId">Ke Project</label>
                            <select wire:model.live="toProjectId" class="form-control" id="toProjectId">
                                <option value="">Semua Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mt-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select wire:model.live="status" class="form-control" id="status">
                                <option value="">Semua Status</option>
                                <option value="ON_LOAN">BELUM DIKEMBALIKAN</option>
                                <option value="PARTIAL_RETURN">DI KEMBALIKAN SEBAGIAN</option>
                                <option value="RETURNED">DI KEMBALIKAN</option>
                                <option value="CANCELED">DI BATALKAN</option>
                                <option value="WRITTEN_OFF">TIDAK DI KEMBALIKAN</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mt-2">
                        <div class="form-group">
                            <label for="status">Kode Material</label>
                            <input type="text" class="form-control" wire:model.live="kodeMaterial">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-danger text-white" wire:click="resetFilter">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Transfer</h6>
                <button class="btn btn-success text-white" wire:click="exportToExcel">Export Excel</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Dari Project</th>
                                <th>Ke Project</th>
                                <th>Tanggal Transfer</th>
                                <th>Kode Material</th>
                                <th>Total Peminjaman</th>
                                <th>Total Pengembalian </th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Dibuat Oleh</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transferPostings as $posting)
                                <tr>
                                    <td>{{ $posting->id }}</td>
                                    <td>{{ $posting->fromProject->nama_project ?? 'N/A' }}</td>
                                    <td>{{ $posting->toProject->nama_project ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($posting->transfer_date)->format('d-m-Y') }}</td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($posting->items as $item)
                                                <li>
                                                    {{ $item->fromTool->kode_material }} - Qty:
                                                    {{ $item->qty }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ $posting->items->sum('qty') }}</td>
                                    <td>{{ $posting->items->sum('returned_qty') }}</td>
                                    <td>
                                        @if ($posting->status == 'ON_LOAN')
                                            <span class="badge bg-warning text-dark">BELUM DIKEMBALIKAN</span>
                                        @elseif($posting->status == 'PARTIAL_RETURN')
                                            <span class="badge bg-info text-dark">DI KEMBALIKAN SEBAGIAN</span>
                                        @elseif($posting->status == 'RETURNED')
                                            <span class="badge bg-success text-white">DI KEMBALIKAN</span>
                                        @elseif($posting->status == 'CANCELED')
                                            <span class="badge bg-danger text-white">DI BATALKAN</span>
                                            <br>
                                            Alasan : {{ $posting->cancel_reason }}
                                        @elseif($posting->status == 'WRITTEN_OFF')
                                            <span class="badge bg-secondary">TIDAK DIKEMBALIKAN</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $posting->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $posting->createdBy->name ?? 'N/A' }}</td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('transfer-posting.show', $posting->id) }}"
                                            class="btn btn-info btn-sm text-white" wire:navigate>
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                        @if (Auth::user()->hasRole(['admin', 'staff']))
                                            @if ($posting->status !== 'CANCELED' && $posting->status !== 'RETURNED' && $posting->status !== 'WRITTEN_OFF')
                                                <button class="btn btn-danger btn-sm text-white"
                                                    data-coreui-toggle="modal" data-coreui-target="#cancelModal"
                                                    wire:click="$set('selectedTransferId', {{ $posting->id }})">
                                                    <i class="fas fa-times"></i> Batal
                                                </button>
                                            @endif
                                        @endif
                                        @if (Auth::user()->hasRole(['admin']))
                                            @if (!in_array($posting->status, ['RETURNED', 'CANCELED', 'WRITTEN_OFF']))
                                                <button type="button"
                                                    class="btn btn-warning btn-sm text-white btn-written-off"
                                                    data-id="{{ $posting->id }}">
                                                    <i class="fas fa-file-invoice"></i> Tidak Dikembalikan
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data transfer.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    {{ $transferPostings->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Batalkan Transfer Posting</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancelReason">Alasan Pembatalan:</label>
                        <textarea class="form-control" id="cancelReason" rows="3" wire:model.defer="cancelReason"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger"
                        wire:click="cancelTransfer({{ $selectedTransferId }})">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('writtenOff', ({
            id
        }) => {
            @this.call('writtenOff', id);
        });
        Livewire.on('closeCancelModal', () => {
            const modalEl = document.getElementById('cancelModal')
            const modal = coreui.Modal.getInstance(modalEl)
            modal.hide()
        })
        Livewire.on('writtenOffSuccess', () => {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Transfer Posting tidak perlu di kembalikan'
            });
        });
    });
    document.querySelectorAll('.btn-written-off').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            confirmWrittenOff(id);
        });
    });

    function confirmWrittenOff(transferId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                Livewire.dispatch('writtenOff', {
                    id: transferId
                });
            }
        });
    }
</script>

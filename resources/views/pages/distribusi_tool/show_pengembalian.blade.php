@extends('layouts.app')

@section('title', 'Form Pengembalian Tool')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Pengembalian Tool</h5>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('distribusi.peminjaman.storeKembali', $distribusi->public_id) }}" method="POST"
                id="pengembalianForm">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Tanggal Pinjam:</strong>
                            {{ $distribusi->tanggal_pinjam_diserahkan->format('d M Y') }}</p>
                        <p><strong>Peminjam:</strong> {{ $distribusi->nama_peminjam }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Departemen:</strong> {{ $distribusi->departemen->nama_departemen }}</p>
                        <p><strong>Bagian:</strong> {{ $distribusi->bagian->nama_bagian }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Project:</strong> {{ $distribusi->project->nama_project }}</p>
                        <p><strong>Keterangan:</strong> {{ $distribusi->keterangan }}</p>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Daftar Tool yang Dipinjam</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Kode Material</th>
                                <th>Deskripsi</th>
                                <th>Spesifikasi</th>
                                <th>Posisi</th>
                                <th>Qty Pinjam</th>
                                <th>Qty Telah Kembali</th>
                                <th>Qty Kembali</th>
                                <th>Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($distribusi->daftarDistribusiTool as $index => $item)
                                @php
                                    $qtyBisaKembali = $item->jumlah_distribusi - $item->qty_kembali;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][id]"
                                            value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][posisi_ct_id]"
                                            value="{{ $item->posisi_ct_id }}">
                                        {{ $item->tool->kode_material ?? '-' }}
                                    </td>
                                    <td>{{ $item->tool->deskripsi ?? '-' }}</td>
                                    <td>{{ $item->tool->spesifikasi ?? '-' }}</td>
                                    <td>{{ $item->posisiCt->nama_posisi_ct ?? '-' }}</td>
                                    <td>{{ $item->jumlah_distribusi_formatted }}</td>
                                    <td>{{ $item->qty_kembali }}</td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][qty_kembali]"
                                            id="qty_kembali_{{ $item->id }}"
                                            class="form-control qty-kembali @error('items.' . $index . '.qty_kembali') is-invalid @enderror"
                                            value="{{ old('items.' . $index . '.qty_kembali', 0) }}"
                                            max="{{ $qtyBisaKembali }}" min="0" data-itemid="{{ $item->id }}">
                                        @error('items.' . $index . '.qty_kembali')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-coreui-toggle="modal"
                                            data-coreui-target="#kondisiModal-{{ $item->id }}">
                                            Input Kondisi
                                        </button>
                                        <div id="kondisi-display-{{ $item->id }}" class="mt-2"></div>
                                    </td>
                                </tr>

                                <!-- Modal Kondisi -->
                                <div class="modal fade" id="kondisiModal-{{ $item->id }}" tabindex="-1"
                                    aria-labelledby="kondisiModalLabel-{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="kondisiModalLabel-{{ $item->id }}">
                                                    Input Kondisi: {{ $item->tool->deskripsi }}</h5>
                                                <button type="button" class="btn-close" data-coreui-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-danger" id="modal-error-{{ $item->id }}"
                                                    style="display: none;"></div>
                                                <input type="hidden"
                                                    name="details[{{ $item->id }}][daftar_distribusi_tool_id]"
                                                    value="{{ $item->id }}">
                                                <div class="mb-3">
                                                    <label for="jumlah_baik_{{ $item->id }}" class="form-label">Jumlah
                                                        Baik</label>
                                                    <input type="number" class="form-control"
                                                        id="jumlah_baik_{{ $item->id }}"
                                                        name="details[{{ $item->id }}][jumlah_baik]" value="0"
                                                        min="0">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="jumlah_rusak_{{ $item->id }}" class="form-label">Jumlah
                                                        Rusak</label>
                                                    <input type="number" class="form-control"
                                                        id="jumlah_rusak_{{ $item->id }}"
                                                        name="details[{{ $item->id }}][jumlah_rusak]" value="0"
                                                        min="0">
                                                </div>
                                                <div id="kerusakan-details-{{ $item->id }}" class="mb-3">
                                                    {{-- Dynamic Kerusakan Details will be appended here --}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-coreui-dismiss="modal">Tutup</button>
                                                <button type="button" class="btn btn-primary save-kondisi"
                                                    data-itemid="{{ $item->id }}">Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('items')
                    <div class="text-danger mb-3">{{ $message }}</div>
                @enderror
                <hr>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Proses Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('keydown', function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    return false;
                }
            });
            const saveButtons = document.querySelectorAll('.save-kondisi');

            saveButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.dataset.itemid;
                    const modal = document.getElementById('kondisiModal-' + itemId);
                    const qtyKembaliEl = document.getElementById('qty_kembali_' + itemId);
                    const qtyKembali = parseInt(qtyKembaliEl.value) || 0;

                    const jumlahBaikEl = document.getElementById('jumlah_baik_' + itemId);
                    const jumlahRusakEl = document.getElementById('jumlah_rusak_' + itemId);
                    const jumlahBaik = parseInt(jumlahBaikEl.value) || 0;
                    const jumlahRusak = parseInt(jumlahRusakEl.value) || 0;

                    const totalKondisi = jumlahBaik + jumlahRusak;

                    const errorDiv = document.getElementById('modal-error-' + itemId);

                    if (totalKondisi !== qtyKembali) {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent =
                            'Jumlah total (Baik + Rusak) harus sama dengan Qty Kembali (' +
                            qtyKembali + ').';
                        return;
                    } else {
                        errorDiv.style.display = 'none';
                    }

                    const kerusakanDetailsContainer = document.getElementById('kerusakan-details-' +
                        itemId);
                    const kerusakanInputs = kerusakanDetailsContainer.querySelectorAll('textarea');
                    let allKerusakanFilled = true;
                    if (jumlahRusak > 0) {
                        if (kerusakanInputs.length !== jumlahRusak) {
                            allKerusakanFilled = false;
                        }
                        kerusakanInputs.forEach(input => {
                            if (input.value.trim() === '') {
                                allKerusakanFilled = false;
                                input.classList.add('is-invalid');
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });
                        if (!allKerusakanFilled) {
                            errorDiv.style.display = 'block';
                            errorDiv.textContent = 'Semua detail kerusakan harus diisi.';
                            return;
                        }
                    }

                    const displayDiv = document.getElementById('kondisi-display-' + itemId);
                    let displayContent =
                        `<span class="badge bg-success">Baik: ${jumlahBaik}</span> <span class="badge bg-danger">Rusak: ${jumlahRusak}</span>`;

                    if (jumlahRusak > 0 && allKerusakanFilled) {
                        displayContent += '<ul class="list-unstyled mt-2">';
                        kerusakanInputs.forEach((input, index) => {
                            displayContent +=
                                `<li>- Kerusakan ${index + 1}: ${input.value}</li>`;
                        });
                        displayContent += '</ul>';
                    }

                    displayDiv.innerHTML = displayContent;

                    $(modal).modal('hide');
                });
            });

            const jumlahRusakInputs = document.querySelectorAll('[id^="jumlah_rusak_"]');
            jumlahRusakInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const itemId = this.id.replace('jumlah_rusak_', '');
                    const jumlahRusak = parseInt(this.value) || 0;
                    const kerusakanDetailsContainer = document.getElementById('kerusakan-details-' +
                        itemId);
                    kerusakanDetailsContainer.innerHTML = '';

                    if (jumlahRusak > 0) {
                        const title = document.createElement('label');
                        title.classList.add('form-label', 'mt-2');
                        title.textContent = 'Detail Kerusakan per Item:';
                        kerusakanDetailsContainer.appendChild(title);

                        for (let i = 0; i < jumlahRusak; i++) {
                            const div = document.createElement('div');
                            div.classList.add('mb-2');

                            const label = document.createElement('label');
                            label.setAttribute('for', `kerusakan_detail_${itemId}_${i}`);
                            label.classList.add('form-label');
                            label.textContent = `Kerusakan Item ${i + 1}`;

                            const textarea = document.createElement('textarea');
                            textarea.classList.add('form-control');
                            textarea.setAttribute('id', `kerusakan_detail_${itemId}_${i}`);
                            textarea.setAttribute('name', `kerusakan_details[${itemId}][]`);
                            textarea.setAttribute('rows', '2');
                            textarea.setAttribute('placeholder',
                                `Deskripsi kerusakan item ${i + 1}`);

                            div.appendChild(label);
                            div.appendChild(textarea);
                            kerusakanDetailsContainer.appendChild(div);
                        }
                    }
                });

                // Trigger input event on load to correctly render dynamic fields based on old input
                input.dispatchEvent(new Event('input'));
            });
        });
    </script>
@endpush

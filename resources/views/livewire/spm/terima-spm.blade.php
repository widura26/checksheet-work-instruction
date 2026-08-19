<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->has('terimaItems'))
        <div class="alert alert-danger">{{ $errors->first('terimaItems') }}</div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row mb-3">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width: 30%">No. SPM</th>
                        <td>: {{ $spm->no_spm }}</td>
                    </tr>
                    <tr>
                        <th>Project</th>
                        <td>: {{ $spm->project?->nama_project }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th style="width: 30%">Kepada</th>
                        <td>: {{ $spm->kepada }}</td>
                    </tr>
                    <tr>
                        <th>Posisi</th>
                        <td>: {{ $spm->posisiCt->nama_posisi_ct }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Kode Material</th>
                        <th>Deskripsi</th>
                        <th>Destinasi</th>
                        <th>Satuan</th>
                        <th class="text-center">Qty Diminta</th>
                        <th style="width: 15%;">Qty Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spm->items as $index => $item)
                        @php
                            $sisa = $item->requested_qty - $item->qty_diterima;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_material }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td>{{ $spm->destinasi }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td class="text-center">{{ $item->requested_qty }}</td>
                            <td>
                                <input type="number"
                                    class="form-control @error('terimaItems.' . $item->id . '.qty_diterima') is-invalid @enderror"
                                    wire:model.defer="terimaItems.{{ $item->id }}.qty_diterima" min="0"
                                    max="{{ $item->requested_qty }}"
                                    {{ $item->requested_qty <= 0 ? 'disabled' : '' }}>
                                @error('terimaItems.' . $item->id . '.qty_diterima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mb-3">
            <label for="file_spm" class="form-label"><b>Upload File SPM Yang Telah Di Tanda Tangani :</b></label>
            <input type="file" class="form-control @error('file_spm') is-invalid @enderror" id="file_spm"
                wire:model.defer="file_spm">
            @error('file_spm')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('spm.index') }}" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan Penerimaan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

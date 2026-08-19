<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <div class="mb-3">
                <label for="kode_material" class="form-label">Kode Material :</label>
                <br>
                <strong>{{ $tool->kode_material }}</strong>
            </div>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi :</label>
            <br>
            <strong> {{ $tool->deskripsi }} </strong>
        </div>
        <div class="mb-3">
            <label for="spesifikasi" class="form-label">Spesifikasi :</label>
            <br>
            <strong>
                {{ $tool->spesifikasi ? $tool->spesifikasi : '-' }}
            </strong>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="qty" class="form-label">Jumlah :</label>
            <br>
            <strong>
                {{ $tool->qty }}
            </strong>
        </div>
        <div class="mb-3">
            <label for="satuan" class="form-label">Satuan</label>
            <br>
            <strong>
                {{ $tool->satuan }}
            </strong>
        </div>
        <div class="mb-3">
            <label for="posisi_ct_id" class="form-label">Posisi</label>
            <select class="form-select @error('posisi_ct_id') is-invalid @enderror" id="posisi_ct_id"
                name="posisi_ct_id">
                <option value="">Pilih Posisi</option>
                @foreach ($posisiCt as $posisi)
                    <option value="{{ $posisi->id }}"
                        {{ old('posisi_ct_id', $tool->posisi_ct_id ?? '') == $posisi->id ? 'selected' : '' }}>
                        {{ $posisi->nama_posisi_ct }}
                    </option>
                @endforeach
            </select>
            @error('posisi_ct_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="vendor" class="form-label">Asal Barang</label>
            <select class="form-select @error('vendor') is-invalid @enderror" id="vendor" name="vendor">
                <option value="">Pilih Vendor</option>
                <option value="vendor" {{ old('vendor', $tool->vendor ?? '') == 'vendor' ? 'selected' : '' }}>Vendor
                </option>
                <option value="INKA" {{ old('vendor', $tool->vendor ?? '') == 'INKA' ? 'selected' : '' }}>INKA
                </option>
            </select>
            @error('vendor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="keterangan_vendor" class="form-label">Keterangan Vendor</label>
            <textarea class="form-control @error('keterangan_vendor') is-invalid @enderror" id="keterangan_vendor"
                name="keterangan_vendor" rows="3">{{ old('keterangan_vendor', $tool->keterangan_vendor ?? '') }}</textarea>
            @error('keterangan_vendor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-6">
        <button type="submit" class="btn btn-primary w-100">{{ $submitButtonText ?? 'Submit' }}</button>
    </div>
    <div class="col-md-6">
        <a onclick="history.back()" class="btn btn-outline-secondary w-100">Kembali</a>
    </div>
</div>

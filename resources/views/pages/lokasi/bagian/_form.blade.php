<div class="mb-3">
    <label for="nama_bagian" class="form-label">Nama Bagian</label>
    <input type="text" name="nama_bagian" id="nama_bagian" class="form-control"
        value="{{ old('nama_bagian', $bagian->nama_bagian ?? '') }}">
</div>
<div class="mb-3">
    <label for="departemen_id" class="form-label">Departemen</label>
    <select name="departemen_id" id="departemen_id" class="form-control">
        <option value="">Pilih Departemen</option>
        @foreach ($departemens as $departemen)
            <option value="{{ $departemen->id }}"
                {{ (isset($bagian) && $bagian->departemen_id == $departemen->id) || old('departemen_id') == $departemen->id ? 'selected' : '' }}>
                {{ $departemen->nama_departemen }}</option>
        @endforeach
    </select>
</div>

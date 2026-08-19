<div class="mb-3">
    <label for="nama_unit" class="form-label">Nama Unit</label>
    <input type="text" name="nama_unit" id="nama_unit" class="form-control" value="{{ old('nama_unit', $unit->nama_unit ?? '') }}">
</div>
<div class="mb-3">
    <label for="bagian_id" class="form-label">Bagian</label>
    <select name="bagian_id" id="bagian_id" class="form-control">
        <option value="">Pilih Bagian</option>
        @foreach($bagians as $bagian)
            <option value="{{ $bagian->id }}" {{ (isset($unit) && $unit->bagian_id == $bagian->id) || old('bagian_id') == $bagian->id ? 'selected' : '' }}>{{ $bagian->nama_bagian }}</option>
        @endforeach
    </select>
</div>
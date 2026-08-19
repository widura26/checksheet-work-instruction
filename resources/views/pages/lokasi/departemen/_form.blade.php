<div class="mb-3">
    <label for="nama_departemen" class="form-label">Nama Departemen</label>
    <input type="text" name="nama_departemen" id="nama_departemen" class="form-control" value="{{ old('nama_departemen', $departemen->nama_departemen ?? '') }}">
</div>
<div class="mb-3">
    <label for="name" class="form-label">Nama Role</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
        value="{{ old('name', isset($role) ? $role->name : '') }}" placeholder="Contoh: staff, viewer, admin" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')

    <div class="card mb-4">
        <div class="card-body">
            @if (session('success'))
                <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-coreui-toggle="modal"
                    data-coreui-target="#createStaffModal">
                    <i class="fas fa-plus me-1"></i>
                    Tambah Pengguna
                </button>
            </div>

            <div class="table-responsive">
                <table id="table-staff" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NIP</th>
                            <th>Role</th>
                            <th>Aktif</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Staff Modal -->
    <div class="modal fade {{ $errors->any() ? 'show' : '' }}" id="createStaffModal" tabindex="-1"
        aria-labelledby="createStaffModalLabel" aria-hidden="true" @if ($errors->any()) style="display: block;" @endif>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createStaffModalLabel">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('staff.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip"
                                name="nip" value="{{ old('nip') }}" required>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                required>
                                <option value="" disabled selected>Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editStaffForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="edit_nip" name="nip" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password <small class="text-muted">(kosongkan
                                    jika tidak diubah)</small></label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <div id="edit_roles_list">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="role[]"
                                            value="{{ $role->name }}" id="edit_role_{{ $role->id }}">
                                        <label class="form-check-label"
                                            for="edit_role_{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(function() {
            let staffTable = $('#table-staff').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                ajax: function(data, callback, settings) {
                    $.get("{{ route('staff.data') }}", {
                        page: (data.start / data.length) + 1,
                        per_page: data.length,
                        search: data.search.value
                    }).done(function(json) {
                        callback({
                            draw: data.draw,
                            recordsTotal: json.total,
                            recordsFiltered: json.total,
                            data: json.data
                        });
                    });
                },
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'nip',
                        name: 'nip'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ]
            });

            function showAlert(message, type) {
                const body = document.querySelector('#table-staff').closest('.card-body');
                const div = document.createElement('div');
                div.className = 'alert alert-' + type + ' alert-dismissible fade show';
                div.innerHTML = message +
                    '<button type="button" class="btn-close" data-coreui-dismiss="alert"></button>';
                body.prepend(div);
            }

            function getModal() {
                return window.coreui ? coreui.Modal : (window.bootstrap ? bootstrap.Modal : null);
            }

            window.openEditModal = function(id) {
                const url = "{{ route('staff.edit', '__ID__') }}".replace('__ID__', id);
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(res => {
                    const s = res.staff;
                    document.getElementById('edit_name').value = s.name;
                    document.getElementById('edit_email').value = s.email;
                    document.getElementById('edit_nip').value = s.nip;
                    document.getElementById('edit_password').value = '';
                    const assigned = res.assigned || [];
                    document.querySelectorAll('#edit_roles_list input[type=checkbox]').forEach(cb => {
                        cb.checked = assigned.includes(cb.value);
                    });
                    document.getElementById('editStaffForm').action =
                        "{{ route('staff.update', '__ID__') }}".replace('__ID__', id);
                    const Modal = getModal();
                    if (Modal) {
                        new Modal(document.getElementById('editStaffModal')).show();
                    } else {
                        const el = document.getElementById('editStaffModal');
                        el.classList.add('show');
                        el.style.display = 'block';
                    }
                });
            };

            $('#editStaffForm').on('submit', function(e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(r => {
                    if (!r.ok) {
                        return r.json().then(err => { throw err; });
                    }
                    return r.json();
                })
                .then(res => {
                    if (res.success) {
                        const Modal = getModal();
                        const el = document.getElementById('editStaffModal');
                        if (Modal) {
                            const inst = Modal.getInstance(el);
                            if (inst) inst.hide();
                        } else {
                            el.classList.remove('show');
                            el.style.display = 'none';
                        }
                        staffTable.ajax.reload();
                        showAlert(res.success, 'success');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan.';
                    if (err && err.errors) {
                        msg = Object.values(err.errors).flat().join(' ');
                    }
                    showAlert(msg, 'danger');
                });
            });
        });
    </script>
@endpush

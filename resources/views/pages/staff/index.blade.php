@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('staff.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Tambah Pengguna
                </a>
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
                        @foreach ($staffs as $staff)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->nip ?? '-' }}</td>
                                <td>{{ ucfirst($staff->roles->first()->name ?? '-') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('staff.status', $staff->nip) }}" method="POST">
                                        @csrf
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                onchange="this.form.submit()" @checked($staff->is_active)>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(document).ready(function() {

            $(document).on('click', '.deleteButton', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/staff/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Terhapus!',
                                    'Data berhasil dihapus.',
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Gagal!',
                                    'Terjadi kesalahan saat menghapus data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

@extends('layouts.app')

@section('title', 'Work Instruction')

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Data Work Instruction</h5>
            </div>
            <button type="button" class="btn btn-primary" id="btnTambah" data-coreui-toggle="modal"
                data-coreui-target="#formModal">
                <i class="fas fa-plus me-1"></i> Tambah
            </button>
        </div>
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

            <div class="table-responsive">
                <table id="table-wi" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>No WI</th>
                            <th>No CP</th>
                            <th>Release Date</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="crudForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="_method" value="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Tambah Work Instruction</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="work_instruction_number" class="form-label">No WI</label>
                            <input type="text" class="form-control" id="work_instruction_number" name="work_instruction_number">
                        </div>
                        <div class="mb-3">
                            <label for="cutting_plan_id" class="form-label">Cutting Plan</label>
                            <select class="form-select" name="cutting_plan_id" id="cutting_plan_id">
                                <option value="">-- Pilih Cutting Plan --</option>
                                @foreach ($cuttingPlans as $cp)
                                    <option value="{{ $cp->id }}">{{ $cp->no_cp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rev" class="form-label">Revisi</label>
                                <input type="number" class="form-control" name="rev" id="rev" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estimasi" class="form-label">Estimasi</label>
                                <input type="date" class="form-control" name="estimasi" id="estimasi">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control" name="note" id="note" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        $(function() {
            function fmtDate(v) {
                if (!v) return '-';
                var p = String(v).split(' ')[0].split('-');
                if (p.length !== 3) return v;
                return p[2] + '-' + p[1] + '-' + p[0];
            }

            var table = $('#table-wi').DataTable({
                processing: false,
                responsive: true,
                info: false,
                ajax: {
                    url: "{{ route('wi.data') }}",
                    dataSrc: 'data'
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
                        data: 'work_instruction_number',
                        name: 'no_wi',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'no_cp',
                        name: 'no_cp',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'release_date',
                        name: 'release_date',
                        orderable: true,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return data ?? '-';
                        }
                    },
                    {
                        data: 'id',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            var iconArrow = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M11.293 13.293l-4 4c-0.391 0.391-1.024 0.391-1.414 0l-0.001-0.001c-0.391-0.391-0.391-1.024 0-1.414l4-4c0.391-0.391 1.024-0.391 1.414 0s0.391 1.024 0 1.414zM19.293 13.293l-4 4c-0.391 0.391-1.024 0.391-1.414 0l-0.001-0.001c-0.391-0.391-0.391-1.024 0-1.414l4-4c0.391-0.391 1.024-0.391 1.414 0s0.391 1.024 0 1.414zM12 2c0.553 0 1 0.447 1 1v16.172l3.293-3.293c0.391-0.391 1.024-0.391 1.414 0s0.391 1.024 0 1.414l-5 5c-0.391 0.391-1.024 0.391-1.414 0l-5-5c-0.391-0.391-0.391-1.024 0-1.414s1.024-0.391 1.414 0L11 19.172V3c0-0.553 0.447-1 1-1z"></path></svg>';
                            var iconEdit = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M22.029 1.971c-0.754-0.754-1.795-1.22-2.944-1.22s-2.191 0.466-2.944 1.22l-12.773 12.773-2.258 6.657c-0.040 0.113-0.063 0.243-0.063 0.378 0 0.323 0.132 0.616 0.344 0.827l0.004 0.004c0.211 0.212 0.503 0.344 0.826 0.344h0c0.136-0 0.266-0.023 0.388-0.066l-0.008 0.003 6.657-2.258 12.773-12.773c0.754-0.754 1.22-1.795 1.22-2.944s-0.466-2.191-1.22-2.944v0zM8.443 19.325l-5.702 1.934 1.934-5.702 9.785-9.785 3.767 3.767zM20.969 6.799l-1.68 1.68-3.767-3.767 1.68-1.68c0.482-0.483 1.149-0.783 1.886-0.783 1.471 0 2.664 1.193 2.664 2.664 0 0.737-0.299 1.404-0.783 1.886l-0 0z"></path></svg>';
                            var iconTrash = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M15.375 4.125v-2.25c0-0.631-0.445-1.125-1.013-1.125h-4.725c-0.568 0-1.013 0.494-1.013 1.125v2.25h-5.625v1.5h18v-1.5zM10.125 2.25h3.75v1.875h-3.75z"></path></svg>';

                            return '<div class="btn-group" style="display:flex; gap:10px; align-items:center;" role="group" aria-label="Aksi">' +
                                '<button class="w-fit btn btn-info btn-sm btn-download" title="Download">' + iconArrow + '</button>' +
                                '<button class="w-fit btn btn-warning btn-sm btn-edit" title="Edit">' + iconEdit + '</button>' +
                                '<button class="w-fit btn btn-danger btn-sm btn-delete" data-id="' + data + '" title="Hapus">' + iconTrash + '</button>' +
                                '</div>';
                        }
                    },
                ],
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
            });

            var modal = new coreui.Modal(document.getElementById('formModal'));

            $('#btnTambah').on('click', function() {
                $('#crudForm')[0].reset();
                $('#_method').val('POST');
                $('#crudForm').attr('action', "{{ route('wi.store') }}");
                $('#work_instruction_number').val('');
                $('#formModalLabel').text('Tambah Work Instruction');
                $('#formError').addClass('d-none').text('');
                $('#btnSubmit').prop('disabled', false).text('Simpan');
            });

            $('#table-wi').on('click', '.btn-edit', function() {
                var row = table.row($(this).closest('tr')).data();
                $('#crudForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('#_method').val('PUT');
                $('#crudForm').attr('action', "{{ route('wi.update', '__ID__') }}".replace('__ID__', row.id));
                $('#work_instruction_number').val(row.id);
                $('#cutting_plan_id').val(row.cutting_plan_id || '');
                $('#keterangan').val(row.keterangan);
                $('#rev').val(row.rev);
                $('#estimasi').val(row.estimasi ? String(row.estimasi).substring(0, 10) : '');
                $('#note').val(row.note);
                $('#formModalLabel').text('Edit Work Instruction');
                modal.show();
            });

            $('#table-wi').on('click', '.btn-download', function() {
                var row = table.row($(this).closest('tr')).data();
                var wiId = row.id;
                var noWi = row.work_instruction_number || '-';

                Swal.fire({
                    title: 'Release Work Instruction?',
                    html: 'Apakah Anda yakin ingin me-release WI <b>' + noWi + '</b>?<br>Tanggal release akan diisi hari ini.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Release!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('wi.release', '__ID__') }}".replace('__ID__', wiId),
                            type: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire('Berhasil!', res.message, 'success');
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat me-release WI.', 'error');
                            }
                        });
                    }
                });
            });

            $('#table-wi').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('wi.destroy', '__ID__') }}".replace('__ID__', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                                table.ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        });
                    }
                });
            });

            $('#crudForm').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var btn = $('#btnSubmit');
                btn.prop('disabled', true).text('Menyimpan...');
                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        btn.prop('disabled', false).text('Simpan');
                        modal.hide();
                        Swal.fire('Berhasil', res.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Simpan');
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var msg = '<ul class="mb-0">';
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                msg += '<li>' + v[0] + '</li>';
                            });
                            msg += '</ul>';
                            $('#formError').removeClass('d-none').html(msg);
                        } else {
                            $('#formError').removeClass('d-none').text(
                                'Terjadi kesalahan saat menyimpan data.');
                        }
                    }
                });
            });
        });
    </script>
@endpush

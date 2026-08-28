@extends('layouts.app')

@section('title', 'Manufacture Drawing')

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Data Manufacture Drawing</h5>
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
                <table id="md-table" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Manufacture Drawing Code</th>
                            <th>Project</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
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
                        <h5 class="modal-title" id="formModalLabel">Tambah Manufacture Drawing</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="id_mdr" class="form-label">ID MDR</label>
                            <input type="text" class="form-control" name="id_mdr" id="id_mdr" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label">Proyek</label>
                                <select class="form-select" name="project_id" id="project_id">
                                    <option value="">-- Pilih Proyek --</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->project_code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="oka_id" class="form-label">OKA</label>
                                <select class="form-select" name="oka_id" id="oka_id">
                                    <option value="">-- Pilih OKA --</option>
                                    @foreach ($okas as $o)
                                        <option value="{{ $o->id }}">{{ $o->oka }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_mdr" class="form-label">Tanggal MDR</label>
                            <input type="date" class="form-control" name="tgl_mdr" id="tgl_mdr">
                        </div>
                        <div class="mb-3">
                            <label for="masalah" class="form-label">Masalah</label>
                            <textarea class="form-control" name="masalah" id="masalah" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="lanjut" class="form-label">Lanjut</label>
                            <input type="text" class="form-control" name="lanjut" id="lanjut">
                        </div>
                        <div class="mb-3">
                            <label for="input" class="form-label">Input</label>
                            <input type="text" class="form-control" name="input" id="input">
                        </div>
                        <div class="mb-3">
                            <label for="dampak" class="form-label">Dampak</label>
                            <input type="text" class="form-control" name="dampak" id="dampak">
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

            var table = $('#md-table').DataTable({
                processing: false,
                serverSide: true,
                responsive: true,
                ajax: function(data, callback, settings) {
                    $.get("{{ route('md.data') }}", {
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
                        data: 'manufacture_drawing_code',
                        name: 'id_mdr'
                    },
                    {
                        data: 'name_project',
                        name: 'name_project'
                    },
                    {
                        data: 'manufacture_drawing_date',
                        name: 'tgl_mdr',
                        render: (data, type) => {
                            return data ? convertDate(data) : '';
                        }
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'id',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return '<button class="btn btn-warning btn-sm btn-edit" title="Edit">' +
                                '<i class="fas fa-edit"></i></button> ' +
                                '<button class="btn btn-danger btn-sm btn-delete" data-id="' + data +
                                '" title="Hapus"><i class="fas fa-trash"></i></button>';
                        }
                    }
                ]
            });

            var modal = new coreui.Modal(document.getElementById('formModal'));

            $('#btnTambah').on('click', function() {
                $('#crudForm')[0].reset();
                $('#_method').val('POST');
                $('#crudForm').attr('action', "{{ route('md.store') }}");
                $('#formModalLabel').text('Tambah Manufacture Drawing');
                $('#formError').addClass('d-none').text('');
            });

            $('#md-table').on('click', '.btn-edit', function() {
                var row = table.row($(this).closest('tr')).data();
                $('#crudForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('#_method').val('PUT');
                $('#crudForm').attr('action', "{{ route('md.update', '__ID__') }}".replace('__ID__', row.id));
                $('#id_mdr').val(row.id_mdr);
                $('#project_id').val(row.project_id || '');
                $('#oka_id').val(row.oka_id || '');
                $('#tgl_mdr').val(row.tgl_mdr ? row.tgl_mdr.substring(0, 10) : '');
                $('#masalah').val(row.masalah);
                $('#lanjut').val(row.lanjut);
                $('#input').val(row.input);
                $('#dampak').val(row.dampak);
                $('#formModalLabel').text('Edit Manufacture Drawing');
                modal.show();
            });

            $('#md-table').on('click', '.btn-delete', function() {
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
                            url: "{{ route('md.destroy', '__ID__') }}".replace('__ID__', id),
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

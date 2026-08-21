@extends('layouts.app')

@section('title', 'Work Instruction')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Data Work Instruction</h5>
                <small class="text-body-secondary">Kelola data Work Instruction (wi_trns)</small>
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
                            <th>No Part</th>
                            <th>Nama</th>
                            <th>Mat</th>
                            <th>T</th>
                            <th>H</th>
                            <th>B</th>
                            <th>L</th>
                            <th>Ws1</th>
                            <th>Ws2</th>
                            <th>Ws3</th>
                            <th>Ws4</th>
                            <th>Ws5</th>
                            <th>Shop 1</th>
                            <th>Shop 2</th>
                            <th>Des</th>
                            <th>No Cp</th>
                            <th>Tgl Cp</th>
                            <th>Qty/ lot</th>
                            <th>No Bpm</th>
                            <th>Tgl Bpm</th>
                            <th>Tgl Wi Trn</th>
                            <th>Keterangan</th>
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="part_no" class="form-label">No Part</label>
                                <input type="text" class="form-control" name="part_no" id="part_no">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mat_cp" class="form-label">Mat</label>
                                <input type="text" class="form-control" name="mat_cp" id="mat_cp">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="md_header_id" class="form-label">Manufacture Drawing</label>
                                <select class="form-select" name="md_header_id" id="md_header_id">
                                    <option value="">-- Pilih Drawing --</option>
                                    @foreach ($mdHeaders as $mh)
                                        <option value="{{ $mh->id }}">
                                            {{ $mh->part_no }}{{ $mh->title ? ' - ' . $mh->title : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_ctplan" class="form-label">No Cp</label>
                                <input type="text" class="form-control" name="no_ctplan" id="no_ctplan">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tgl_ctplan" class="form-label">Tgl Cp</label>
                                <input type="date" class="form-control" name="tgl_ctplan" id="tgl_ctplan">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="qty_lot" class="form-label">Qty/ lot</label>
                                <input type="number" class="form-control" name="qty_lot" id="qty_lot">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_bpm" class="form-label">No Bpm</label>
                                <input type="text" class="form-control" name="no_bpm" id="no_bpm">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tgl_bpm" class="form-label">Tgl Bpm</label>
                                <input type="date" class="form-control" name="tgl_bpm" id="tgl_bpm">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tgl_trn" class="form-label">Tgl Wi Trn</label>
                                <input type="date" class="form-control" name="tgl_trn" id="tgl_trn">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="2"></textarea>
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
                processing: true,
                serverSide: true,
                responsive: true,
                info: false,
                ajax: {
                    url: "{{ route('wi.data') }}",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'part_no',
                        name: 'part_no',
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false
                    },
                    {
                        data: 'mat',
                        name: 'mat',
                        orderable: false
                    },
                    {
                        data: 't1',
                        name: 't1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'h1',
                        name: 'h1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'b1',
                        name: 'b1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'l1',
                        name: 'l1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws1',
                        name: 'ws1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws2',
                        name: 'ws2',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws3',
                        name: 'ws3',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws4',
                        name: 'ws4',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'ws5',
                        name: 'ws5',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'shop1',
                        name: 'shop1',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'shop2',
                        name: 'shop2',
                        orderable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'des',
                        name: 'des',
                        orderable: false
                    },
                    {
                        data: 'no_ctplan',
                        name: 'no_ctplan',
                        orderable: false
                    },
                    {
                        data: 'tgl_ctplan',
                        name: 'tgl_ctplan',
                        orderable: false,
                        className: 'text-center',
                        render: function(data) {
                            return fmtDate(data);
                        }
                    },
                    {
                        data: 'qty_lot',
                        name: 'qty_lot',
                        orderable: false,
                        className: 'text-end'
                    },
                    {
                        data: 'no_bpm',
                        name: 'no_bpm',
                        orderable: false
                    },
                    {
                        data: 'tgl_bpm',
                        name: 'tgl_bpm',
                        orderable: false,
                        className: 'text-center',
                        render: function(data) {
                            return fmtDate(data);
                        }
                    },
                    {
                        data: 'tgl_trn',
                        name: 'tgl_trn',
                        orderable: false,
                        className: 'text-center',
                        render: function(data) {
                            return fmtDate(data);
                        }
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        orderable: false
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
                $('#formModalLabel').text('Tambah Work Instruction');
                $('#formError').addClass('d-none').text('');
            });

            $('#table-wi').on('click', '.btn-edit', function() {
                var row = table.row($(this).closest('tr')).data();
                $('#crudForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('#_method').val('PUT');
                $('#crudForm').attr('action', "{{ route('wi.update', '__ID__') }}".replace('__ID__', row.id));
                $('#part_no').val(row.part_no);
                $('#name').val(row.name);
                $('#mat_cp').val(row.mat);
                $('#md_header_id').val(row.md_header_id || '');
                $('#no_ctplan').val(row.no_ctplan);
                $('#tgl_ctplan').val(row.tgl_ctplan ? String(row.tgl_ctplan).substring(0, 10) : '');
                $('#qty_lot').val(row.qty_lot);
                $('#no_bpm').val(row.no_bpm);
                $('#tgl_bpm').val(row.tgl_bpm ? String(row.tgl_bpm).substring(0, 10) : '');
                $('#tgl_trn').val(row.tgl_trn ? String(row.tgl_trn).substring(0, 10) : '');
                $('#keterangan').val(row.keterangan);
                $('#formModalLabel').text('Edit Work Instruction');
                modal.show();
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

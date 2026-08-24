@extends('layouts.app')

@section('title', 'Cutting Plan')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Data Cutting Plan</h5>
                <small class="text-body-secondary">Kelola data Cutting Plan (CP)</small>
            </div>
            <button type="button" class="btn btn-primary" id="btnTambah" data-coreui-toggle="modal"
                data-coreui-target="#formModal">
                <i class="fas fa-plus me-1"></i> Tambah
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="cutting-plan-table" class="table table-bordered table-hover">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Nomor Cutting Plan</th>
                            <th>Material</th>
                            <th>Kode Proyek</th>
                            <th>WBS</th>
                            <th>Material Size</th>
                            <th>Quantity</th>
                            <th>Cutting Method</th>
                            <th>Sket</th>
                            <th>Part No</th>
                            <th>Cutting Size</th>
                            <th>Qty / Car</th>
                            <th>Qty / Lot</th>
                            <th>Part Name</th>
                            <th>Remark</th>
                            <th>Special Instruction</th>
                            <th>Date Issued</th>
                            <th>Date Revised</th>
                            <th>Prepared By</th>
                            <th>Approve By</th>
                            <th>BPM Number</th>
                            <th>Date of BPM</th>
                            <th>Date Operator</th>
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
                        <h5 class="modal-title" id="formModalLabel">Tambah Cutting Plan</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formError" class="alert alert-danger d-none"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_cp" class="form-label">ID CP</label>
                                <input type="text" class="form-control" name="id_cp" id="id_cp">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_cp" class="form-label">No CP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="no_cp" id="no_cp" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mat" class="form-label">Material</label>
                                <input type="text" class="form-control" name="mat" id="mat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="spec_mat" class="form-label">Spec Material</label>
                                <input type="text" class="form-control" name="spec_mat" id="spec_mat">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="qty" class="form-label">Qty</label>
                                <input type="number" class="form-control" name="qty" id="qty">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="shop" class="form-label">Shop</label>
                                <input type="text" class="form-control" name="shop" id="shop">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="grup" class="form-label">Grup</label>
                                <input type="text" class="form-control" name="grup" id="grup">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lot" class="form-label">Lot</label>
                                <input type="text" class="form-control" name="lot" id="lot">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="qty_lot" class="form-label">Qty Lot</label>
                                <input type="number" class="form-control" name="qty_lot" id="qty_lot">
                            </div>
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
            var table = $('#cutting-plan-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: function(data, callback, settings) {
                    $.get("{{ route('cutting-plan.data') }}", {
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
                        data: 'no_cp',
                        name: 'no_cp',
                        className: 'text-center'
                    },
                    {
                        data: 'mat',
                        name: 'mat'
                    },
                    {
                        data: 'kode_proyek',
                        name: 'kode_proyek',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'wbs',
                        name: 'wbs',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'material_size',
                        name: 'material_size',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        className: 'text-center'
                    },
                    {
                        data: 'cutting_method',
                        name: 'cutting_method',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'sket',
                        name: 'sket',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'part_no',
                        name: 'part_no',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'cutting_size',
                        name: 'cutting_size',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'qty_car',
                        name: 'qty_car',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'qty_lot',
                        name: 'qty_lot',
                        className: 'text-center'
                    },
                    {
                        data: 'part_name',
                        name: 'part_name',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'remark',
                        name: 'remark',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'special_instruction',
                        name: 'special_instruction',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'date_issued',
                        name: 'date_issued',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'date_revised',
                        name: 'date_revised',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'prepared_by',
                        name: 'prepared_by',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'approve_by',
                        name: 'approve_by',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'bpm_number',
                        name: 'bpm_number',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'date_of_bpm',
                        name: 'date_of_bpm',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'date_operator',
                        name: 'date_operator',
                        className: 'text-center',
                        defaultContent: ''
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
                $('#crudForm').attr('action', "{{ route('cutting-plan.store') }}");
                $('#formModalLabel').text('Tambah Cutting Plan');
                $('#formError').addClass('d-none').text('');
            });

            $('#cutting-plan-table').on('click', '.btn-edit', function() {
                var row = table.row($(this).closest('tr')).data();
                $('#crudForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('#_method').val('PUT');
                $('#crudForm').attr('action', "{{ route('cutting-plan.update', '__ID__') }}".replace('__ID__', row.id));
                $('#id_cp').val(row.id_cp);
                $('#no_cp').val(row.no_cp);
                $('#mat').val(row.mat);
                $('#spec_mat').val(row.spec_mat);
                $('#qty').val(row.qty);
                $('#shop').val(row.shop);
                $('#grup').val(row.grup);
                $('#lot').val(row.lot);
                $('#qty_lot').val(row.qty_lot);
                $('#formModalLabel').text('Edit Cutting Plan');
                modal.show();
            });

            $('#cutting-plan-table').on('click', '.btn-delete', function() {
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
                            url: "{{ route('cutting-plan.destroy', '__ID__') }}".replace('__ID__', id),
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

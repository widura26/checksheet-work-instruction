@extends('layouts.app')

@section('title', 'Cutting Plan')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }
        .dataTables_scrollBody {
            overflow-x: auto !important;
        }
        #cutting-plan-table {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">Data Cutting Plan</h5>
                <small class="text-body-secondary">Kelola data Cutting Plan (CP)</small>
            </div>
            <a href="{{ route('cutting-plan.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah
            </a>
        </div>
        <div class="card-body">
            <table id="cutting-plan-table" class="table table-bordered table-hover" style="width: 100%;">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>No CP</th>
                            <th>Material</th>
                            <th>Kode Proyek</th>
                            <th>WBS</th>
                            <th>Material Size</th>
                            <th>Quantity</th>
                            <th>Cutting Method</th>
                            <th>Sket</th>
                            <th>Parts</th>
                            <th>Special Instruction</th>
                            <th>Date Issued</th>
                            <th>Date Revised</th>
                            <th>BPM Number</th>
                            <th>Date of BPM</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="crudForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="_method" value="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formModalLabel">Tambah Cutting Plan</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @include('cutting-plan._form')
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
            $('#mat').select2({
                placeholder: 'Cari Material...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#formModal'),
                ajax: {
                    url: "{{ route('cutting-plan.materials') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || ''
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            var piecepartTemplate = function(index, data) {
                data = data || {};
                return '<tr>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][part_no]" value="' + (data.part_no ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][cutting_size]" value="' + (data.cutting_size ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][qty_car]" value="' + (data.qty_car ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][qty_lot]" value="' + (data.qty_lot ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][part_name]" value="' + (data.part_name ?? '') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="pieceparts[' + index + '][remark]" value="' + (data.remark ?? '') + '"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-piecepart"><i class="fas fa-trash"></i></button></td>' +
                    '</tr>';
            };

            function reindexPieceparts() {
                $('#piecepartsList tr').each(function(i) {
                    $(this).find('input').each(function() {
                        var name = $(this).attr('name');
                        var newName = name.replace(/pieceparts\[\d+\]/, 'pieceparts[' + i + ']');
                        $(this).attr('name', newName);
                    });
                });
            }

            function addPiecepart(data) {
                var index = $('#piecepartsList tr').length;
                $('#piecepartsList').append(piecepartTemplate(index, data));
            }

            $('#btnAddPiecepart').on('click', function() {
                addPiecepart();
            });

            $('#piecepartsList').on('click', '.btn-remove-piecepart', function() {
                $(this).closest('tr').remove();
                reindexPieceparts();
            });

            function loadUsers() {
                $.get("{{ route('cutting-plan.users') }}", function(users) {
                    var opts = '<option value="">-- Pilih --</option>';
                    $.each(users, function(i, u) {
                        opts += '<option value="' + u.id + '">' + u.name + '</option>';
                    });
                    $('#prepared_by, #approved_by, #date_operator').html(opts);
                });
            }
            loadUsers();

            var table = $('#cutting-plan-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                deferRender: true,
                responsive: false,
                columnDefs: [
                    { targets: 0,  width: '40px',  minWidth: '40px'  },
                    { targets: 1,  width: '100px', minWidth: '100px' },
                    { targets: 2,  width: '120px', minWidth: '120px' },
                    { targets: 3,  width: '130px', minWidth: '130px' },
                    { targets: 4,  width: '100px', minWidth: '100px' },
                    { targets: 5,  width: '110px', minWidth: '110px' },
                    { targets: 6,  width: '80px',  minWidth: '80px'  },
                    { targets: 7,  width: '120px', minWidth: '120px' },
                    { targets: 8,  width: '100px', minWidth: '100px' },
                    { targets: 9,  width: '80px',  minWidth: '80px'  },
                    { targets: 10, width: '160px', minWidth: '160px' },
                    { targets: 11, width: '110px', minWidth: '110px' },
                    { targets: 12, width: '110px', minWidth: '110px' },
                    { targets: 13, width: '110px', minWidth: '110px' },
                    { targets: 14, width: '110px', minWidth: '110px' },
                    { targets: 15, width: '80px',  minWidth: '80px'  },
                ],
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
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'mat',
                        name: 'mat',
                        defaultContent: ''
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
                        data: 'quantity',
                        name: 'quantity',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'cutting_method',
                        name: 'cutting_method',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'sketch',
                        name: 'sketch',
                        className: 'text-center',
                        defaultContent: ''
                    },
                    {
                        data: 'pieceparts',
                        name: 'pieceparts',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (Array.isArray(data) && data.length) {
                                return data.length + ' part';
                            }
                            return '-';
                        }
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

            $('#cutting-plan-table').on('click', '.btn-edit', function() {
                var row = table.row($(this).closest('tr')).data();
                $('#crudForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('#_method').val('PUT');
                $('#crudForm').attr('action', "{{ route('cutting-plan.update', '__ID__') }}".replace('__ID__', row.id));
                $('#no_cp').val(row.no_cp);
                $('#mat').empty();
                if (row.mat) {
                    var opt = new Option(row.mat, row.mat, true, true);
                    $('#mat').append(opt).trigger('change');
                } else {
                    $('#mat').val(null).trigger('change');
                }
                $('#kode_proyek').val(row.kode_proyek);
                $('#wbs').val(row.wbs);
                $('#material_size').val(row.material_size);
                $('#quantity').val(row.quantity);
                $('#cutting_method').val(row.cutting_method);
                $('#sketch').val(row.sketch);
                $('#special_instruction').val(row.special_instruction);
                $('#date_issued').val((row.date_issued || '').toString().substring(0, 10));
                $('#date_revised').val((row.date_revised || '').toString().substring(0, 10));
                $('#bpm_number').val(row.bpm_number);
                $('#date_of_bpm').val((row.date_of_bpm || '').toString().substring(0, 10));
                $('#prepared_by').val(row.prepared_by);
                $('#approved_by').val(row.approved_by);
                $('#date_operator').val(row.date_operator);

                $('#piecepartsList').empty();
                if (Array.isArray(row.pieceparts) && row.pieceparts.length) {
                    $.each(row.pieceparts, function(i, pp) {
                        addPiecepart(pp);
                    });
                } else {
                    addPiecepart();
                }

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

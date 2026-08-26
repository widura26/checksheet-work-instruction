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
        .icon {
            vertical-align: middle;
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
                <svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg> Tambah
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
            function initCombobox(opts) {
                var $input = $(opts.input);
                var $hidden = $(opts.hidden);
                var $dropdown = $(opts.dropdown);
                var $wrapper = $hidden.closest('.mat-combobox');
                var results = [];

                function render(rs) {
                    results = rs || [];
                    var html = '';
                    if (!results.length) {
                        html = '<div class="dropdown-item text-muted disabled">Tidak ada hasil</div>';
                    } else {
                        $.each(results, function(i, m) {
                            html += '<button type="button" class="dropdown-item" data-id="' + m.id +
                                '">' + m.text + '</button>';
                        });
                    }
                    $dropdown.html(html).addClass('show');
                }

                function search(q) {
                    $.get(opts.url, {
                        q: q || ''
                    }, function(data) {
                        render(data.results || []);
                    });
                }

                var timer;
                $input.on('input', function() {
                    $hidden.val('');
                    clearTimeout(timer);
                    var val = $(this).val();
                    timer = setTimeout(function() {
                        search(val);
                    }, 250);
                }).on('focus', function() {
                    if (!$dropdown.hasClass('show')) {
                        search($input.val());
                    }
                });

                $dropdown.on('click', '.dropdown-item', function() {
                    if ($(this).hasClass('disabled')) return;
                    var id = $(this).data('id');
                    var found = results.find(function(m) {
                        return String(m.id) === String(id);
                    });
                    $input.val(found ? found.text : '');
                    $hidden.val(id);
                    $dropdown.removeClass('show');
                });

                $(document).on('click', function(e) {
                    if (!$(e.target).closest($wrapper).length) {
                        $dropdown.removeClass('show');
                    }
                });
            }

            initCombobox({
                input: '#mat_search',
                hidden: '#mat',
                dropdown: '#mat_dropdown',
                url: "{{ route('cutting-plan.materials') }}"
            });
            initCombobox({
                input: '#kode_proyek_search',
                hidden: '#kode_proyek',
                dropdown: '#kode_proyek_dropdown',
                url: "{{ route('cutting-plan.projects') }}"
            });
            initCombobox({
                input: '#cutting_method_search',
                hidden: '#cutting_method',
                dropdown: '#cutting_method_dropdown',
                url: "{{ route('cutting-plan.processes') }}"
            });
            initCombobox({
                input: '#prepared_by_search',
                hidden: '#prepared_by',
                dropdown: '#prepared_by_dropdown',
                url: "{{ route('cutting-plan.users') }}"
            });
            initCombobox({
                input: '#approved_by_search',
                hidden: '#approved_by',
                dropdown: '#approved_by_dropdown',
                url: "{{ route('cutting-plan.users') }}"
            });
            initCombobox({
                input: '#date_operator_search',
                hidden: '#date_operator',
                dropdown: '#date_operator_dropdown',
                url: "{{ route('cutting-plan.users') }}"
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
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-piecepart"><svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M15.375 4.125v-2.25c0-0.631-0.445-1.125-1.013-1.125h-4.725c-0.568 0-1.013 0.494-1.013 1.125v2.25h-5.625v1.5h18v-1.5zM10.125 2.25h3.75v1.875h-3.75z"></path></svg></button></td>' +
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
                        render: function(data) {
                            return data ? data.substring(0, 10) : '';
                        },
                        defaultContent: '-'
                    },
                    {
                        data: 'date_revised',
                        name: 'date_revised',
                        className: 'text-center',
                        render: function(data) {
                            return data ? data.substring(0, 10) : '';
                        },
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
                            var iconEdit = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M22.029 1.971c-0.754-0.754-1.795-1.22-2.944-1.22s-2.191 0.466-2.944 1.22l-12.773 12.773-2.258 6.657c-0.040 0.113-0.063 0.243-0.063 0.378 0 0.323 0.132 0.616 0.344 0.827l0.004 0.004c0.211 0.212 0.503 0.344 0.826 0.344h0c0.136-0 0.266-0.023 0.388-0.066l-0.008 0.003 6.657-2.258 12.773-12.773c0.754-0.754 1.22-1.795 1.22-2.944s-0.466-2.191-1.22-2.944v0zM8.443 19.325l-5.702 1.934 1.934-5.702 9.785-9.785 3.767 3.767zM20.969 6.799l-1.68 1.68-3.767-3.767 1.68-1.68c0.482-0.483 1.149-0.783 1.886-0.783 1.471 0 2.664 1.193 2.664 2.664 0 0.737-0.299 1.404-0.783 1.886l-0 0z"></path></svg>';
                            var iconTrash = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M15.375 4.125v-2.25c0-0.631-0.445-1.125-1.013-1.125h-4.725c-0.568 0-1.013 0.494-1.013 1.125v2.25h-5.625v1.5h18v-1.5zM10.125 2.25h3.75v1.875h-3.75z"></path></svg>';

                            return `
                                <div class="btn-group" style="display:flex; gap:10px; align-items:center;" role="group" aria-label="Aksi">
                                    <button class="btn btn-warning btn-sm btn-edit" title="Edit">
                                        ${iconEdit}
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="${data}" title="Hapus">
                                        ${iconTrash}
                                    </button>
                                </div>
                            `
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
                $('#mat').val('');
                $('#mat_search').val('');
                if (row.material_id) {
                    $('#mat').val(row.material_id);
                    $('#mat_search').val(row.mat);
                }
                $('#kode_proyek').val('');
                $('#kode_proyek_search').val('');
                if (row.kode_proyek) {
                    $('#kode_proyek').val(row.kode_proyek);
                    $('#kode_proyek_search').val(row.project_name || row.kode_proyek);
                }
                $('#wbs').val(row.wbs);
                $('#material_size').val(row.material_size);
                $('#quantity').val(row.quantity);
                $('#cutting_method').val('');
                $('#cutting_method_search').val('');
                if (row.process_id) {
                    $('#cutting_method').val(row.process_id);
                    $('#cutting_method_search').val(row.cutting_method);
                }
                $('#sketch').val(row.sketch);
                $('#special_instruction').val(row.special_instruction);
                $('#date_issued').val((row.date_issued || '').toString().substring(0, 10));
                $('#date_revised').val((row.date_revised || '').toString().substring(0, 10));
                $('#bpm_number').val(row.bpm_number);
                $('#date_of_bpm').val((row.date_of_bpm || '').toString().substring(0, 10));
                $('#prepared_by').val('');
                $('#prepared_by_search').val('');
                if (row.prepared_by) {
                    $('#prepared_by').val(row.prepared_by);
                    $('#prepared_by_search').val(row.prepared_by_name || '');
                }
                $('#approved_by').val('');
                $('#approved_by_search').val('');
                if (row.approved_by) {
                    $('#approved_by').val(row.approved_by);
                    $('#approved_by_search').val(row.approved_by_name || '');
                }
                $('#date_operator').val('');
                $('#date_operator_search').val('');
                if (row.date_operator) {
                    $('#date_operator').val(row.date_operator);
                    $('#date_operator_search').val(row.date_operator_name || '');
                }

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
                                `Terjadi kesalahan saat menyimpan data. ${xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : ''}`);
                        }
                    }
                });
            });
        });
    </script>
@endpush

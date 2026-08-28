$(function() {
    const convertDate = (timestamp) => {
        const date = new Date(timestamp);
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    };

    var table = $('#barcode-table').DataTable({
        processing: false,
        serverSide: true,
        scrollX: true,
        deferRender: true,
        responsive: false,
        columnDefs: [
            { targets: 0, width: '40px', minWidth: '40px' },
            { targets: 1, width: '40px', minWidth: '40px' },
        ],
        ajax: function(data, callback, settings) {
            $.get(BC.routes.data, {
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
                name: 'checkbox',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return '<input type="checkbox" class="row-select" value="' + row.id + '">';
                }
            },
            {
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
                data: 'no_konv',
                name: 'no_konv',
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
                data: 'partno1',
                name: 'partno1',
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
                data: 'lot',
                name: 'lot',
                className: 'text-center',
                defaultContent: ''
            },
            {
                data: 'PR',
                name: 'PR',
                className: 'text-center',
                defaultContent: ''
            },
            {
                data: 'oka_no',
                name: 'oka_no',
                className: 'text-center',
                defaultContent: ''
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
                defaultContent: ''
            },
            {
                data: 'bil',
                name: 'bil',
                className: 'text-center',
                defaultContent: ''
            },
            {
                data: 'tgl',
                name: 'tgl',
                className: 'text-center',
                render: function(data) {
                    return data ? convertDate(data) : '';
                },
                defaultContent: '-'
            },
            {
                data: 'tgl_print',
                name: 'tgl_print',
                className: 'text-center',
                render: function(data) {
                    return data ? convertDate(data) : '';
                },
                defaultContent: '-'
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
                            <button class="w-fit btn btn-warning btn-sm btn-edit" title="Edit">
                                ${iconEdit}
                            </button>
                            <button class="w-fit btn btn-danger btn-sm btn-delete" data-id="${data}" title="Hapus">
                                ${iconTrash}
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    /* Kolom: tampilkan / sembunyikan */
    (function() {
        var $menu = $('#columnToggleMenu');
        var fixedTitles = ['No', 'Aksi'];
        var initCols = table.settings().init().columns;
        table.columns().every(function(idx) {
            var name = initCols[idx] ? initCols[idx].name : null;
            if (name === 'DT_RowIndex' || name === 'aksi' || name === 'checkbox') return;
            var title = $(this.header()).text().trim();
            if (fixedTitles.indexOf(title) !== -1) return;
            var $li = $(
                '<li class="form-check mb-1">' +
                '<input class="form-check-input col-toggle" type="checkbox" checked ' +
                'data-column="' + idx + '" id="coltoggle-' + idx + '">' +
                '<label class="form-check-label ps-1" for="coltoggle-' + idx + '">' + title + '</label>' +
                '</li>'
            );
            $menu.append($li);
        });
        $menu.on('change', '.col-toggle', function() {
            var idx = $(this).data('column');
            table.column(idx).visible(this.checked);
        });
    })();

    /* Export Excel: hanya kolom yang sedang tampil (kecuali No & Aksi) */
    $('#btnExportExcel').on('click', function() {
        var keys = [];
        var initCols = table.settings().init().columns;
        table.columns().every(function(idx) {
            if (!this.visible()) return;
            var name = initCols[idx] ? initCols[idx].name : null;
            if (name === 'DT_RowIndex' || name === 'aksi' || name === 'checkbox') return;
            if (name) keys.push(name);
        });

        if (!keys.length) {
            Swal.fire('Info', 'Tidak ada kolom yang dipilih untuk diekspor.', 'info');
            return;
        }

        var $form = $('<form method="GET" action="' + BC.routes.export + '"></form>');
        keys.forEach(function(k) {
            $form.append($('<input type="hidden" name="columns[]">').val(k));
        });
        var searchVal = table.search();
        if (searchVal) {
            $form.append($('<input type="hidden" name="search">').val(searchVal));
        }
        $form.appendTo('body').trigger('submit').remove();
    });

    /* Checklist: pilih semua / individual (kolom untuk hapus massal) */
    function updateBulkDeleteButton() {
        var count = $('.row-select:checked').length;
        $('#selectedCount').text(count);
        if (count >= 2) {
            $('#btnBulkDelete').removeClass('d-none');
        } else {
            $('#btnBulkDelete').addClass('d-none');
        }
    }

    $(document).on('change', '#selectAll', function() {
        $('.row-select').prop('checked', this.checked);
        updateBulkDeleteButton();
    });
    $('#barcode-table').on('change', '.row-select', function() {
        var total = $('.row-select').length;
        var checked = $('.row-select:checked').length;
        $('#selectAll').prop('checked', total > 0 && total === checked);
        updateBulkDeleteButton();
    });

    /* Hapus massal */
    $('#btnBulkDelete').on('click', function() {
        var ids = $('.row-select:checked').map(function() {
            return this.value;
        }).get();

        if (ids.length < 2) return;

        Swal.fire({
            title: 'Hapus ' + ids.length + ' data?',
            text: 'Data terpilih akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: BC.routes.bulkDestroy,
                type: 'DELETE',
                data: {
                    ids: ids
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    Swal.fire('Terhapus!', 'Data terpilih berhasil dihapus.', 'success');
                    $('#selectAll').prop('checked', false);
                    $('#btnBulkDelete').addClass('d-none');
                    table.ajax.reload();
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        });
    });

    var modal = new coreui.Modal(document.getElementById('formModal'));

    $('#barcode-table').on('click', '.btn-edit', function() {
        var row = table.row($(this).closest('tr')).data();
        $('#btnSubmit').prop('disabled', false).text('Simpan');
        $('#crudForm')[0].reset();
        $('#formError').addClass('d-none').text('');
        $('#_method').val('PUT');
        $('#crudForm').attr('action', BC.routes.update.replace('__ID__', row.id));
        $('#no_konv').val(row.no_konv);
        $('#part_no').val(row.part_no);
        $('#partno1').val(row.partno1);
        $('#wbs').val(row.wbs);
        $('#lot').val(row.lot);
        $('#PR').val(row.PR);
        $('#oka_id').val(row.oka_id || '');
        $('#wi_trn_id').val(row.wi_trn_id || '');
        $('#status').val(row.status);
        $('#bil').val(row.bil);
        $('#tgl').val((row.tgl || '').toString().substring(0, 10));
        $('#tgl_print').val((row.tgl_print || '').toString().substring(0, 10));
        $('#formModalLabel').text('Edit Barcode');
        modal.show();
    });

    $('#barcode-table').on('click', '.btn-delete', function() {
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
                    url: BC.routes.destroy.replace('__ID__', id),
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
                btn.prop('disabled', false).text('Simpan');
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

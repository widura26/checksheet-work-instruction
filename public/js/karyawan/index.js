$(function() {
    const canManage = window.Karyawan.canManage;

    const iconEdit = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M22.029 1.971c-0.754-0.754-1.795-1.22-2.944-1.22s-2.191 0.466-2.944 1.22l-12.773 12.773-2.258 6.657c-0.040 0.113-0.063 0.243-0.063 0.378 0 0.323 0.132 0.616 0.344 0.827l0.004 0.004c0.211 0.212 0.503 0.344 0.826 0.344h0c0.136-0 0.266-0.023 0.388-0.066l-0.008 0.003 6.657-2.258 12.773-12.773c0.754-0.754 1.22-1.795 1.22-2.944s-0.466-2.191-1.22-2.944v0zM8.443 19.325l-5.702 1.934 1.934-5.702 9.785-9.785 3.767 3.767zM20.969 6.799l-1.68 1.68-3.767-3.767 1.68-1.68c0.482-0.483 1.149-0.783 1.886-0.783 1.471 0 2.664 1.193 2.664 2.664 0 0.737-0.299 1.404-0.783 1.886l-0 0z"></path></svg>';
    const iconTrash = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M15.375 4.125v-2.25c0-0.631-0.445-1.125-1.013-1.125h-4.725c-0.568 0-1.013 0.494-1.013 1.125v2.25h-5.625v1.5h18v-1.5zM10.125 2.25h3.75v1.875h-3.75z"></path></svg>';

    function loadOptions(url, target, selected, labelField, cb) {
        $.getJSON(url, function(data) {
            const $sel = $(target);
            $sel.empty().append('<option value="">Pilih</option>');
            $.each(data, function(i, v) {
                $sel.append('<option value="' + v.id + '">' + v[labelField] + '</option>');
            });
            if (selected) {
                $sel.val(selected);
            }
            if (cb) {
                cb();
            }
        }).fail(function() {
            const $sel = $(target);
            $sel.empty().append('<option value="">Pilih</option>');
            if (cb) {
                cb();
            }
        });
    }

    function clearDependent($bagian, $unit) {
        $bagian.empty().append('<option value="">Pilih Bagian</option>');
        $unit.empty().append('<option value="">Pilih Unit</option>');
    }

    var table = $('#karyawan-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        deferRender: true,
        responsive: false,
        ajax: function(data, callback, settings) {
            $.get(Karyawan.routes.data, {
                page: (data.start / data.length) + 1,
                per_page: data.length,
                search: data.search.value,
                status: $('#status_filter').val()
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
                data: 'nip',
                name: 'nip',
                className: 'text-center',
                defaultContent: ''
            },
            {
                data: 'nama',
                name: 'nama',
                defaultContent: ''
            },
            {
                data: 'departemen',
                name: 'departemen',
                className: 'text-center',
                defaultContent: '-'
            },
            {
                data: 'bagian',
                name: 'bagian',
                className: 'text-center',
                defaultContent: '-'
            },
            {
                data: 'unit',
                name: 'unit',
                className: 'text-center',
                defaultContent: '-'
            },
            {
                data: 'jabatan',
                name: 'jabatan',
                defaultContent: ''
            },
            {
                data: 'nip',
                name: 'aksi',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(nip) {
                    if (!canManage) {
                        return '-';
                    }
                    return `
                        <div class="btn-group" style="display:flex; gap:10px; align-items:center;" role="group" aria-label="Aksi">
                            <button class="w-fit btn btn-warning btn-sm btn-edit" data-nip="${nip}" title="Edit">
                                ${iconEdit}
                            </button>
                            <button class="w-fit btn btn-danger btn-sm btn-delete" data-nip="${nip}" title="Hapus">
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
        var initCols = table.settings().init().columns;
        table.columns().every(function(idx) {
            var name = initCols[idx] ? initCols[idx].name : null;
            if (name === 'DT_RowIndex' || name === 'aksi') return;
            var title = $(this.header()).text().trim();
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

    /* Pencarian & filter status */
    $('#btnSearch').on('click', function() {
        table.search($('#search_input').val()).draw();
    });
    $('#search_input').on('keypress', function(e) {
        if (e.which === 13) {
            table.search(this.value).draw();
        }
    });
    $('#status_filter').on('change', function() {
        table.ajax.reload();
    });
    $('#btnReset').on('click', function() {
        $('#search_input').val('');
        $('#status_filter').val('');
        table.search('').ajax.reload();
    });

    /* Dependent dropdown (Departemen -> Bagian -> Unit) */
    $(document).on('change', '.departemen-select', function() {
        const departemenId = $(this).val();
        const $form = $(this).closest('form');
        const $bagian = $form.find('.bagian-select');
        const $unit = $form.find('.unit-select');

        clearDependent($bagian, $unit);

        if (departemenId) {
            loadOptions(Karyawan.routes.bagian.replace('__ID__', departemenId), $bagian, null, 'nama_bagian');
        }
    });

    $(document).on('change', '.bagian-select', function() {
        const bagianId = $(this).val();
        const $form = $(this).closest('form');
        const $unit = $form.find('.unit-select');

        $unit.empty().append('<option value="">Pilih Unit</option>');

        if (bagianId) {
            loadOptions(Karyawan.routes.unit.replace('__ID__', bagianId), $unit, null, 'nama_unit');
        }
    });

    function showErrors(form, xhr) {
        $(form).find('.invalid-feedback').remove();
        $(form).find('.is-invalid').removeClass('is-invalid');
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
            const errors = xhr.responseJSON.errors;
            $.each(errors, function(k, v) {
                const $input = $(form).find('[name="' + k + '"]');
                if ($input.length) {
                    $input.addClass('is-invalid');
                    $input.after('<div class="invalid-feedback">' + v[0] + '</div>');
                }
            });
            Swal.fire('Validasi gagal', 'Periksa kembali isian form.', 'error');
        } else {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan.';
            Swal.fire('Gagal', msg, 'error');
        }
    }

    function clearFormErrors(form) {
        $(form).find('.invalid-feedback').remove();
        $(form).find('.is-invalid').removeClass('is-invalid');
    }

    /* Modal Create */
    const createModal = new coreui.Modal(document.getElementById('createModal'));
    $('#btnCreate').on('click', function() {
        const form = document.getElementById('createForm');
        form.reset();
        clearFormErrors(form);
        clearDependent($('#create_bagian_id'), $('#create_unit_id'));
        createModal.show();
    });

    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = $('#btnCreateSubmit');
        btn.prop('disabled', true).text('Menyimpan...');
        $.ajax({
            url: Karyawan.routes.store,
            type: 'POST',
            data: new FormData(form),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                createModal.hide();
                btn.prop('disabled', false).text('Simpan');
                Swal.fire('Berhasil', res.message, 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan');
                showErrors(form, xhr);
            }
        });
    });

    /* Modal Edit */
    const editModal = new coreui.Modal(document.getElementById('editModal'));
    $('#karyawan-table').on('click', '.btn-edit', function() {
        const nip = $(this).data('nip');
        $.get(Karyawan.routes.show.replace('__NIP__', nip), function(k) {
            const form = document.getElementById('editForm');
            form.reset();
            clearFormErrors(form);
            clearDependent($('#edit_bagian_id'), $('#edit_unit_id'));

            $('#edit_nip').val(k.nip);
            $('#edit_nip_display').val(k.nip);
            $('#edit_nama').val(k.nama);
            $('#edit_jabatan').val(k.jabatan);
            $('#edit_departemen_id').val(k.departemen_id ?? '');
            $('#editForm').attr('action', Karyawan.routes.update.replace('__NIP__', k.nip));

            if (k.departemen_id) {
                loadOptions(Karyawan.routes.bagian.replace('__ID__', k.departemen_id), '#edit_bagian_id', k.bagian_id, 'nama_bagian', function() {
                    if (k.bagian_id) {
                        loadOptions(Karyawan.routes.unit.replace('__ID__', k.bagian_id), '#edit_unit_id', k.unit_id, 'nama_unit');
                    }
                });
            }

            editModal.show();
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = $('#btnEditSubmit');
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
                editModal.hide();
                btn.prop('disabled', false).text('Simpan Perubahan');
                Swal.fire('Berhasil', res.message, 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan Perubahan');
                showErrors(form, xhr);
            }
        });
    });

    /* Hapus */
    $('#karyawan-table').on('click', '.btn-delete', function() {
        const nip = $(this).data('nip');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data karyawan ini akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: Karyawan.routes.destroy.replace('__NIP__', nip),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    Swal.fire('Terhapus!', 'Data karyawan berhasil dihapus.', 'success');
                    table.ajax.reload();
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        });
    });

    /* Import Excel */
    const importModal = new coreui.Modal(document.getElementById('importModal'));
    $('#btnImportExcel').on('click', function() {
        const form = document.getElementById('importForm');
        form.reset();
        clearFormErrors(form);
        importModal.show();
    });

    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = $('#btnImportSubmit');
        btn.prop('disabled', true).text('Mengimpor...');
        $.ajax({
            url: Karyawan.routes.import,
            type: 'POST',
            data: new FormData(form),
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                importModal.hide();
                btn.prop('disabled', false).text('Import Data');
                Swal.fire('Berhasil', res.message, 'success');
                table.ajax.reload();
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Import Data');
                showErrors(form, xhr);
            }
        });
    });
});

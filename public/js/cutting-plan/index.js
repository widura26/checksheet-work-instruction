$(function() {
    // --- Combobox helper ---
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
            $.get(opts.url, { q: q || '' }, function(data) {
                render(data.results || []);
            });
        }

        var timer;
        $input.on('input', function() {
            $hidden.val('');
            clearTimeout(timer);
            var val = $(this).val();
            timer = setTimeout(function() { search(val); }, 250);
        }).on('focus', function() {
            if (!$dropdown.hasClass('show')) {
                search($input.val());
            }
        });

        $dropdown.on('click', '.dropdown-item', function() {
            if ($(this).hasClass('disabled')) return;
            var id = $(this).data('id');
            var found = results.find(function(m) { return String(m.id) === String(id); });
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

    // --- Global comboboxes ---
    initCombobox({
        input: '#cutting_method_search',
        hidden: '#cutting_method',
        dropdown: '#cutting_method_dropdown',
        url: CP.routes.processes
    });
    initCombobox({
        input: '#prepared_by_search',
        hidden: '#prepared_by',
        dropdown: '#prepared_by_dropdown',
        url: CP.routes.users
    });
    initCombobox({
        input: '#approved_by_search',
        hidden: '#approved_by',
        dropdown: '#approved_by_dropdown',
        url: CP.routes.users
    });
    initCombobox({
        input: '#date_operator_search',
        hidden: '#date_operator',
        dropdown: '#date_operator_dropdown',
        url: CP.routes.users
    });

    // --- Part template ---
    var partTemplate = function(mi, pi, data) {
        data = data || {};
        return '<tr>' +
            '<td><input type="text" class="form-control form-control-sm" name="materials[' + mi + '][parts][' + pi + '][part_no]" value="' + (data.part_no ?? '') + '"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="materials[' + mi + '][parts][' + pi + '][cutting_size]" value="' + (data.cutting_size ?? '') + '"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="materials[' + mi + '][parts][' + pi + '][qty_car]" value="' + (data.qty_car ?? '') + '"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="materials[' + mi + '][parts][' + pi + '][qty_lot]" value="' + (data.qty_lot ?? '') + '"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="materials[' + mi + '][parts][' + pi + '][part_name]" value="' + (data.part_name ?? '') + '"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="materials[' + mi + '][parts][' + pi + '][remark]" value="' + (data.remark ?? '') + '"></td>' +
            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm btn-remove-part"><svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M15.375 4.125v-2.25c0-0.631-0.445-1.125-1.013-1.125h-4.725c-0.568 0-1.013 0.494-1.013 1.125v2.25h-5.625v1.5h18v-1.5zM10.125 2.25h3.75v1.875h-3.75z"></path></svg></button></td>' +
            '</tr>';
    };

    // --- Material section template ---
    var materialIndex = 0;

    function materialSectionTemplate(mi, data) {
        data = data || {};
        var uid = 'mat_combo_' + mi;
        var html = '<div class="material-section" data-material-index="' + mi + '">' +
            '<div class="material-header">' +
                '<h6>Material ' + (mi + 1) + '</h6>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-material">' +
                    '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4.5 22.125c-0 0.003-0 0.006-0 0.008 0 0.613 0.494 1.11 1.105 1.116h12.79c0.612-0.006 1.105-0.504 1.105-1.117 0-0.003-0-0.006-0-0.009v0-15h-15zM6 8.625h12v13.125h-12z"></path><path d="M7.875 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M11.25 10.125h1.5v9.375h-1.5v-9.375z"></path><path d="M14.625 10.125h1.5v9.375h-1.5v-9.375z"></path></svg> Hapus' +
                '</button>' +
            '</div>' +
            '<div class="cp-form-grid">' +
                '<div>' +
                    '<label class="form-label">Material <span class="text-danger">*</span></label>' +
                    '<div class="position-relative mat-combobox">' +
                        '<input type="text" class="form-control" id="' + uid + '_search" placeholder="Cari Material..." autocomplete="off">' +
                        '<input type="hidden" name="materials[' + mi + '][material_id]" id="' + uid + '_hidden" value="' + (data.material_id ?? '') + '">' +
                        '<div class="dropdown-menu w-100" id="' + uid + '_dropdown" style="max-height: 240px; overflow-y: auto;"></div>' +
                    '</div>' +
                '</div>' +
                '<div>' +
                    '<label class="form-label">Material Size</label>' +
                    '<input type="text" class="form-control" name="materials[' + mi + '][material_size]" value="' + (data.material_size ?? '') + '">' +
                '</div>' +
                '<div>' +
                    '<label class="form-label">Quantity</label>' +
                    '<input type="number" class="form-control" name="materials[' + mi + '][quantity]" value="' + (data.quantity ?? '') + '">' +
                '</div>' +
                '<div>' +
                    '<label class="form-label">WBS</label>' +
                    '<input type="text" class="form-control" name="materials[' + mi + '][wbs]" value="' + (data.wbs ?? '') + '">' +
                '</div>' +
            '</div>' +
            '<div class="parts-section">' +
                '<div class="parts-header">' +
                    '<label class="form-label fw-bold mb-0">Parts</label>' +
                    '<button type="button" class="btn btn-sm btn-outline-primary btn-add-part">' +
                        '<svg class="icon me-1" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21 11.25h-8.25v-8.25h-1.5v8.25h-8.25v1.5h8.25v8.25h1.5v-8.25h8.25z"></path></svg> Tambah Part' +
                    '</button>' +
                '</div>' +
                '<div class="table-responsive">' +
                    '<table class="table table-bordered table-sm align-middle mb-0">' +
                        '<thead class="text-center">' +
                            '<tr>' +
                                '<th style="width: 14%">Part No</th>' +
                                '<th style="width: 14%">Cutting Size</th>' +
                                '<th style="width: 12%">Qty / Car</th>' +
                                '<th style="width: 12%">Qty / Lot</th>' +
                                '<th style="width: 18%">Part Name</th>' +
                                '<th style="width: 22%">Remark</th>' +
                                '<th style="width: 8%">Aksi</th>' +
                            '</tr>' +
                        '</thead>' +
                        '<tbody class="parts-body"></tbody>' +
                    '</table>' +
                '</div>' +
            '</div>' +
        '</div>';
        return html;
    }

    function addMaterial(data) {
        var mi = materialIndex++;
        $('#materialsContainer').append(materialSectionTemplate(mi, data));
        initCombobox({
            input: '#' + 'mat_combo_' + mi + '_search',
            hidden: '#' + 'mat_combo_' + mi + '_hidden',
            dropdown: '#' + 'mat_combo_' + mi + '_dropdown',
            url: CP.routes.materials
        });
        addPart(mi, null);
        if (data && data.material_name) {
            $('#mat_combo_' + mi + '_search').val(data.material_name);
        }
        if (data && Array.isArray(data.parts) && data.parts.length) {
            var $tbody = $('[data-material-index="' + mi + '"] .parts-body');
            $tbody.empty();
            $.each(data.parts, function(i, pp) {
                addPart(mi, pp);
            });
        }
    }

    function addPart(mi, data) {
        var $tbody = $('[data-material-index="' + mi + '"] .parts-body');
        var pi = $tbody.find('tr').length;
        $tbody.append(partTemplate(mi, pi, data));
    }

    function reindexParts(mi) {
        var $section = $('[data-material-index="' + mi + '"]');
        $section.find('.parts-body tr').each(function(i) {
            $(this).find('input').each(function() {
                var name = $(this).attr('name');
                var newName = name.replace(/parts\[\d+\]/, 'parts[' + i + ']');
                $(this).attr('name', newName);
            });
        });
    }

    // --- Material/Part events ---
    $(document).on('click', '.btn-remove-material', function() {
        $(this).closest('.material-section').remove();
        $('#materialsContainer .material-section').each(function(newMi) {
            $(this).attr('data-material-index', newMi);
            $(this).find('.material-header h6').text('Material ' + (newMi + 1));
            $(this).find('input, .dropdown-menu').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    var newName = name.replace(/materials\[\d+\]/, 'materials[' + newMi + ']');
                    $(this).attr('name', newName);
                }
                var id = $(this).attr('id');
                if (id) {
                    var newId = id.replace(/mat_combo_\d+/, 'mat_combo_' + newMi);
                    $(this).attr('id', newId);
                }
            });
        });
        materialIndex = $('#materialsContainer .material-section').length;
    });

    $(document).on('click', '.btn-add-part', function() {
        var mi = $(this).closest('.material-section').data('material-index');
        addPart(mi);
    });

    $(document).on('click', '.btn-remove-part', function() {
        var $section = $(this).closest('.material-section');
        var mi = $section.data('material-index');
        $(this).closest('tr').remove();
        reindexParts(mi);
    });

    // --- Date helper ---
    const convertDate = (timestamp) => {
        const date = new Date(timestamp);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    };

    // --- DataTable ---
    var table = $('#cutting-plan-table').DataTable({
        processing: false,
        serverSide: true,
        scrollX: true,
        deferRender: true,
        responsive: false,
        columnDefs: [
            { targets: 0,  width: '40px',  minWidth: '40px'  },
            { targets: 1,  width: '40px',  minWidth: '40px'  },
            { targets: 2,  width: '100px', minWidth: '100px' },
            { targets: 3,  width: '120px', minWidth: '120px' },
            { targets: 4,  width: '130px', minWidth: '130px' },
            { targets: 5,  width: '100px', minWidth: '100px' },
            { targets: 6,  width: '120px', minWidth: '120px' },
            { targets: 7,  width: '80px',  minWidth: '80px'  },
            { targets: 8,  width: '160px', minWidth: '160px' },
            { targets: 9,  width: '110px', minWidth: '110px' },
            { targets: 10, width: '110px', minWidth: '110px' },
            { targets: 11, width: '110px', minWidth: '110px' },
            { targets: 12, width: '80px',  minWidth: '80px'  },
        ],
        ajax: function(data, callback, settings) {
            $.get(CP.routes.data, {
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
        columns: [
            {
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
                render: function(data, type, row, meta) { return meta.row + 1; }
            },
            {
                data: 'no_cp',
                name: 'no_cp',
                className: 'text-center',
                defaultContent: ''
            },
            {
                data: 'materials_data',
                name: 'materials',
                orderable: false,
                searchable: false,
                className: '',
                render: function(data) {
                    if (!Array.isArray(data) || !data.length) return '-';
                    return data.map(function(m) {
                        return '<span class="badge bg-light text-dark">' + (m.material_name || '-') + '</span>';
                    }).join(' ');
                }
            },
            {
                data: 'kode_proyek',
                name: 'kode_proyek',
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
                data: 'materials_data',
                name: 'materials_parts',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data) {
                    if (!Array.isArray(data) || !data.length) return '-';
                    var total = data.reduce(function(sum, m) { return sum + (m.parts ? m.parts.length : 0); }, 0);
                    return total + ' part(s)';
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
                render: function(data) { return data ? convertDate(data) : '-'; },
                defaultContent: '-'
            },
            {
                data: 'date_revised',
                name: 'date_revised',
                className: 'text-center',
                render: function(data) { return data ? convertDate(data) : ''; },
                defaultContent: ''
            },
            {
                data: 'bpm_number',
                name: 'bpm_number',
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
                    var iconBarcode = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M3 5h2v14H3zM7 5h2v14H7zM11 5h2v14h-2zM15 5h1v14h-1zM18 5h1v14h-1zM20.5 5h1.5v14H20.5z"></path></svg>';
                    var iconBpm = '<svg class="icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M6 2h9l5 5v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zM14 3.5V8h4.5L14 3.5zM8 12h8v1.5H8zM8 15h8v1.5H8zM8 18h5v1.5H8z"></path></svg>';
                    return '<div class="btn-group" style="display:flex; gap:10px; align-items:center;">' +
                        '<a class="w-fit btn btn-info btn-sm" href="' + CP.routes.barcodeCreate + '?cutting_plan_id=' + data + '" title="Barcode">' + iconBarcode + '</a>' +
                        '<a class="w-fit btn btn-primary btn-sm" href="' + CP.routes.bpmCreate + '?cutting_plan_id=' + data + '" title="Buat BPM">' + iconBpm + '</a>' +
                        '<button class="w-fit btn btn-warning btn-sm btn-edit" title="Edit">' + iconEdit + '</button>' +
                        '<button class="w-fit btn btn-danger btn-sm btn-delete" data-id="' + data + '" title="Hapus">' + iconTrash + '</button>' +
                    '</div>';
                }
            }
        ]
    });

    // --- Column toggle ---
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

    // --- Export Excel ---
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
        var $form = $('<form method="GET" action="' + CP.routes.export + '"></form>');
        keys.forEach(function(k) {
            $form.append($('<input type="hidden" name="columns[]">').val(k));
        });
        var searchVal = table.search();
        if (searchVal) {
            $form.append($('<input type="hidden" name="search">').val(searchVal));
        }
        $form.appendTo('body').trigger('submit').remove();
    });

    // --- Bulk delete ---
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
    $('#cutting-plan-table').on('change', '.row-select', function() {
        var total = $('.row-select').length;
        var checked = $('.row-select:checked').length;
        $('#selectAll').prop('checked', total > 0 && total === checked);
        updateBulkDeleteButton();
    });

    $('#btnBulkDelete').on('click', function() {
        var ids = $('.row-select:checked').map(function() { return this.value; }).get();
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
                url: CP.routes.bulkDestroy,
                type: 'DELETE',
                data: { ids: ids },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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

    // --- Edit modal ---
    var modal = new coreui.Modal(document.getElementById('formModal'));

    $('#cutting-plan-table').on('click', '.btn-edit', function() {
        var row = table.row($(this).closest('tr')).data();
        $('#btnSubmit').prop('disabled', false).text('Simpan');
        $('#crudForm')[0].reset();
        $('#formError').addClass('d-none').text('');
        $('#_method').val('PUT');
        $('#crudForm').attr('action', CP.routes.update.replace('__ID__', row.id));

        // Global fields
        $('#no_cp').val(row.no_cp);
        $('#sketch').val(row.sketch);
        $('#special_instruction').val(row.special_instruction);
        $('#date_issued').val((row.date_issued || '').toString().substring(0, 10));
        $('#date_revised').val((row.date_revised || '').toString().substring(0, 10));
        $('#bpm_number').val(row.bpm_number);
        $('#date_of_bpm').val((row.date_of_bpm || '').toString().substring(0, 10));

        // Cutting method
        $('#cutting_method').val('');
        $('#cutting_method_search').val('');
        if (row.process_id) {
            $('#cutting_method').val(row.process_id);
            $('#cutting_method_search').val(row.cutting_method);
        }

        // Prepared by
        $('#prepared_by').val('');
        $('#prepared_by_search').val('');
        if (row.prepared_by) {
            $('#prepared_by').val(row.prepared_by);
            $('#prepared_by_search').val(row.prepared_by_name || '');
        }

        // Approved by
        $('#approved_by').val('');
        $('#approved_by_search').val('');
        if (row.approved_by) {
            $('#approved_by').val(row.approved_by);
            $('#approved_by_search').val(row.approved_by_name || '');
        }

        // Date operator
        $('#date_operator').val('');
        $('#date_operator_search').val('');
        if (row.date_operator) {
            $('#date_operator').val(row.date_operator);
            $('#date_operator_search').val(row.date_operator_name || '');
        }

        // Materials + parts
        $('#materialsContainer').empty();
        materialIndex = 0;
        if (Array.isArray(row.materials_data) && row.materials_data.length) {
            $.each(row.materials_data, function(i, m) {
                addMaterial(m);
            });
        } else {
            addMaterial();
        }

        $('#formModalLabel').text('Edit Cutting Plan');
        modal.show();
    });

    // --- Delete ---
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
                    url: CP.routes.destroy.replace('__ID__', id),
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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

    // --- Form submission (modal) ---
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
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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
                        'Terjadi kesalahan saat menyimpan data. ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : ''));
                }
            }
        });
    });
});

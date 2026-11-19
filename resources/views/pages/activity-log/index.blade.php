@extends('layouts.app')

@section('title', 'Activity Log')

@push('css')
    <style>
        .dataTables_wrapper {
            overflow-x: auto;
        }

        #table-activity {
            width: 100% !important;
        }

        .activity-detail dt {
            font-size: .75rem;
            text-transform: uppercase;
            color: var(--cui-secondary-color);
            margin-bottom: .15rem;
        }

        .activity-detail dd {
            margin-bottom: .85rem;
            word-break: break-word;
        }

        .activity-changes td {
            vertical-align: top;
            word-break: break-word;
        }

        .activity-changes .value-old {
            color: var(--cui-danger);
        }

        .activity-changes .value-new {
            color: var(--cui-success);
        }
    </style>
@endpush

@section('content')
    <div class="card mb-4">
        <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="card-title mb-0">Activity Log</h5>
                <small class="text-medium-emphasis">Riwayat aktivitas pengguna untuk kebutuhan tracking dan
                    transparansi.</small>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="filter_search">Pencarian</label>
                    <input type="text" class="form-control" id="filter_search"
                        placeholder="Cari deskripsi, modul, user, subjek...">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="filter_user">Pengguna</label>
                    <select class="form-select" id="filter_user">
                        <option value="">Semua Pengguna</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->nip }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="filter_log_name">Modul</label>
                    <select class="form-select" id="filter_log_name">
                        <option value="">Semua Modul</option>
                        @foreach ($logNames as $logName)
                            <option value="{{ $logName }}">{{ $logName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="filter_event">Aksi</label>
                    <select class="form-select" id="filter_event">
                        <option value="">Semua Aksi</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}">{{ $event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="filter_date_from">Dari Tanggal</label>
                    <input type="date" class="form-control" id="filter_date_from">
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="filter_date_to">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="filter_date_to">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-primary" id="btnFilter">Terapkan</button>
                    <button type="button" class="btn btn-secondary" id="btnReset">Reset</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="table-activity" class="table table-bordered table-hover" style="width: 100%;">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Modul</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>Subjek</th>
                            <th>IP</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Aktivitas</h5>
                    <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="module">
        const activityDataUrl = @json(route('activity-log.data'));

        const eventColors = {
            created: 'bg-success',
            updated: 'bg-info',
            deleted: 'bg-danger',
            login: 'bg-primary',
            logout: 'bg-secondary',
            login_failed: 'bg-warning text-dark',
            import: 'bg-success',
            import_queued: 'bg-info text-dark',
            export: 'bg-primary',
            download: 'bg-primary',
            view: 'bg-secondary',
            role_updated: 'bg-warning text-dark',
            delete_all: 'bg-danger',
        };

        function escapeHtml(value) {
            if (value === null || value === undefined) {
                return '';
            }

            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatValue(value) {
            if (value === null || value === undefined || value === '') {
                return '-';
            }

            if (typeof value === 'object') {
                try {
                    return JSON.stringify(value);
                } catch (e) {
                    return String(value);
                }
            }

            return String(value);
        }

        function getModal() {
            return window.coreui ? coreui.Modal : (window.bootstrap ? bootstrap.Modal : null);
        }

        function collectFilters() {
            return {
                search: $('#filter_search').val(),
                user_id: $('#filter_user').val(),
                log_name: $('#filter_log_name').val(),
                event: $('#filter_event').val(),
                date_from: $('#filter_date_from').val(),
                date_to: $('#filter_date_to').val(),
            };
        }

        $(function() {
            const table = $('#table-activity').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                ajax: function(data, callback) {
                    $.get(activityDataUrl, Object.assign({
                        page: (data.start / data.length) + 1,
                        per_page: data.length,
                    }, collectFilters())).done(function(json) {
                        callback({
                            draw: data.draw,
                            recordsTotal: json.total,
                            recordsFiltered: json.total,
                            data: json.data,
                        });
                    }).fail(function() {
                        callback({
                            draw: data.draw,
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: [],
                        });
                    });
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'created_at',
                        className: 'text-nowrap'
                    },
                    {
                        data: 'user',
                        render: function(data, type, row) {
                            let html = escapeHtml(data);

                            if (row.user_email) {
                                html += '<br><small class="text-medium-emphasis">' + escapeHtml(row
                                    .user_email) + '</small>';
                            }

                            if (row.role) {
                                html += '<br><small class="text-medium-emphasis">' + escapeHtml(row.role) +
                                    '</small>';
                            }

                            return html;
                        }
                    },
                    {
                        data: 'log_name',
                        render: function(data) {
                            return escapeHtml(data || '-');
                        }
                    },
                    {
                        data: 'event',
                        className: 'text-center',
                        render: function(data) {
                            if (!data) {
                                return '-';
                            }

                            return '<span class="badge ' + (eventColors[data] || 'bg-secondary') + '">' +
                                escapeHtml(data) + '</span>';
                        }
                    },
                    {
                        data: 'description',
                        render: function(data) {
                            return escapeHtml(data);
                        }
                    },
                    {
                        data: 'subject',
                        render: function(data, type, row) {
                            if (!data) {
                                return '-';
                            }

                            return escapeHtml(data) + (row.subject_id
                                ? '<br><small class="text-medium-emphasis">' + escapeHtml(row.subject_id) +
                                '</small>'
                                : '');
                        }
                    },
                    {
                        data: 'ip',
                        render: function(data) {
                            return escapeHtml(data || '-');
                        }
                    },
                    {
                        data: 'detail_url',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return '<button type="button" class="btn btn-sm btn-info btn-detail" data-url="' +
                                escapeHtml(data) + '">Lihat</button>';
                        }
                    },
                ]
            });

            function reloadTable() {
                table.ajax.reload();
            }

            $('#btnFilter').on('click', reloadTable);

            $('#filter_search').on('keypress', function(e) {
                if (e.which === 13) {
                    reloadTable();
                }
            });

            $('#filter_user, #filter_log_name, #filter_event, #filter_date_from, #filter_date_to')
                .on('change', reloadTable);

            $('#btnReset').on('click', function() {
                $('#filter_search').val('');
                $('#filter_user').val('');
                $('#filter_log_name').val('');
                $('#filter_event').val('');
                $('#filter_date_from').val('');
                $('#filter_date_to').val('');
                reloadTable();
            });

            $('#table-activity').on('click', '.btn-detail', function() {
                const url = $(this).data('url');
                const modal = document.getElementById('detailModal');
                const Modal = getModal();

                $('#detailBody').html(
                    '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat...</span></div></div>'
                );

                if (Modal) {
                    new Modal(modal).show();
                } else {
                    modal.classList.add('show');
                    modal.style.display = 'block';
                }

                fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Gagal memuat detail aktivitas.');
                        }

                        return response.json();
                    })
                    .then(renderDetail)
                    .catch(function(error) {
                        $('#detailBody').html('<div class="alert alert-danger mb-0">' + escapeHtml(error
                            .message) + '</div>');
                    });
            });

            function renderDetail(detail) {
                const meta = [
                    ['Waktu', detail.created_at],
                    ['Pengguna', detail.user],
                    ['Email', detail.user_email],
                    ['Role', detail.role],
                    ['Modul', detail.log_name],
                    ['Aksi', detail.event],
                    ['Subjek', detail.subject_type ? detail.subject_type + (detail.subject_id ? ' #' +
                        detail.subject_id : '') : null],
                    ['IP', detail.ip],
                    ['URL', detail.url],
                    ['User Agent', detail.user_agent],
                ];

                let metaHtml = '<dl class="row activity-detail mb-3">';
                meta.forEach(function(item) {
                    metaHtml += '<dt class="col-sm-3">' + escapeHtml(item[0]) + '</dt>';
                    metaHtml += '<dd class="col-sm-9">' + (item[1] ? escapeHtml(item[1]) : '-') + '</dd>';
                });
                metaHtml += '</dl>';

                let changesHtml = renderChanges(detail.old, detail.new);
                let otherHtml = renderOtherProperties(detail.properties);

                $('#detailBody').html(
                    '<h5 class="mb-3">' + escapeHtml(detail.description) + '</h5>' +
                    metaHtml +
                    '<h6 class="mb-2">Perubahan Data</h6>' +
                    changesHtml +
                    otherHtml
                );
            }

            function renderChanges(oldValues, newValues) {
                const oldData = oldValues && typeof oldValues === 'object' ? oldValues : {};
                const newData = newValues && typeof newValues === 'object' ? newValues : {};
                const keys = Array.from(new Set(Object.keys(oldData).concat(Object.keys(newData))));

                if (keys.length === 0) {
                    return '<p class="text-medium-emphasis">Tidak ada detail perubahan data.</p>';
                }

                let rows = '';
                keys.forEach(function(key) {
                    const oldValue = formatValue(oldData[key]);
                    const newValue = formatValue(newData[key]);

                    rows += '<tr>' +
                        '<td><code>' + escapeHtml(key) + '</code></td>' +
                        '<td class="value-old">' + escapeHtml(oldValue) + '</td>' +
                        '<td class="value-new">' + escapeHtml(newValue) + '</td>' +
                        '</tr>';
                });

                return '<div class="table-responsive mb-4"><table class="table table-sm table-bordered activity-changes mb-0">' +
                    '<thead><tr><th style="width: 25%">Field</th><th style="width: 37.5%">Sebelum</th><th style="width: 37.5%">Sesudah</th></tr></thead>' +
                    '<tbody>' + rows + '</tbody></table></div>';
            }

            function renderOtherProperties(properties) {
                if (!properties || typeof properties !== 'object') {
                    return '';
                }

                const excluded = ['ip', 'url', 'user_agent', 'causer_name'];
                const keys = Object.keys(properties).filter(function(key) {
                    return excluded.indexOf(key) === -1;
                });

                if (keys.length === 0) {
                    return '';
                }

                let rows = '';
                keys.forEach(function(key) {
                    rows += '<tr><td><code>' + escapeHtml(key) + '</code></td><td>' + escapeHtml(
                        formatValue(properties[key])) + '</td></tr>';
                });

                return '<h6 class="mb-2">Informasi Tambahan</h6>' +
                    '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">' +
                    '<thead><tr><th style="width: 30%">Properti</th><th>Nilai</th></tr></thead>' +
                    '<tbody>' + rows + '</tbody></table></div>';
            }
        });
    </script>
@endpush

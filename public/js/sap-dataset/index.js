let chartGroupInstance = null;
let chartStatusPrInstance = null;
let bomDataTableInstance = null;

const filters = {
    proyek: 'Semua',
    group: 'Semua',
    trainset: 'Semua',
    selectedStatuses: new Set()
};

// --- Penghemat request -----------------------------------------------------
// Respons /data untuk kombinasi filter yang sama dipakai ulang selama 60 detik.
// Request yang sedang berjalan juga digabung (dedupe) agar perubahan filter
// bersamaan tidak mengirim dua request identik.
const DATA_CACHE_TTL_MS = 60_000;
const dataCache = new Map();
const pendingData = new Map();

function clearDataCache() {
    dataCache.clear();

    // Bersihkan juga salinan di service worker agar data pasca-import tidak basi.
    if ('caches' in window && navigator.serviceWorker && navigator.serviceWorker.controller) {
        caches.keys()
            .then(keys => Promise.all(keys
                .filter(key => key.endsWith('-runtime'))
                .map(async key => {
                    const cache = await caches.open(key);
                    const requests = await cache.keys();
                    await Promise.all(requests
                        .filter(req => new URL(req.url).pathname === '/material-planning/data')
                        .map(req => cache.delete(req)));
                })))
            .catch(() => {});
    }
}

function fetchDataOnce(url) {
    const cached = dataCache.get(url);
    if (cached && Date.now() - cached.at < DATA_CACHE_TTL_MS) {
        return Promise.resolve(JSON.parse(cached.body));
    }

    if (pendingData.has(url)) {
        return pendingData.get(url);
    }

    const promise = fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(json => {
            dataCache.set(url, { at: Date.now(), body: JSON.stringify(json) });
            return json;
        })
        .finally(() => pendingData.delete(url));

    pendingData.set(url, promise);
    return promise;
}

let loadTimer = null;
function scheduleLoad(delay = 250) {
    clearTimeout(loadTimer);
    loadTimer = setTimeout(() => loadData(), delay);
}
// ---------------------------------------------------------------------------

const createTooltipConfig = (unitName = 'item') => ({
    enabled: true,
    external: null,
    backgroundColor: 'rgba(15, 23, 42, 0.95)',
    titleColor: '#ffffff',
    titleFont: {
        size: 13,
        weight: '700',
        family: "'Source Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    },
    titleMarginBottom: 6,
    bodyColor: '#e2e8f0',
    bodyFont: {
        size: 12,
        weight: '500',
        family: "'Source Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    },
    bodySpacing: 5,
    footerColor: '#94a3b8',
    footerFont: {
        size: 11,
        weight: '400',
        family: "'Source Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    },
    footerMarginTop: 6,
    borderColor: 'rgba(255, 255, 255, 0.12)',
    borderWidth: 1,
    padding: {
        top: 10,
        bottom: 10,
        left: 14,
        right: 14
    },
    cornerRadius: 8,
    displayColors: true,
    boxWidth: 8,
    boxHeight: 8,
    boxPadding: 6,
    usePointStyle: true,
    callbacks: {
        title: function(items) {
            return items[0].label || '';
        },
        label: function(ctx) {
            const val = ctx.raw || 0;
            return ` Jumlah: ${val.toLocaleString('id-ID')} ${unitName}`;
        },
        afterLabel: function(ctx) {
            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
            const val = ctx.raw || 0;
            const pct = total > 0 ? ((val / total) * 100).toFixed(2) : '0.00';
            return ` Proporsi: ${pct}%`;
        },
        footer: function(items) {
            const total = items[0].dataset.data.reduce((a, b) => a + b, 0);
            return `Total: ${total.toLocaleString('id-ID')} ${unitName}`;
        }
    }
});

const sharedLegendConfig = {
    display: true,
    position: 'bottom',
    labels: {
        boxWidth: 10,
        boxHeight: 10,
        usePointStyle: true,
        pointStyle: 'circle',
        color: '#31333F',
        font: {
            size: 12,
            family: "'Source Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
        },
        padding: 15
    }
};

// Plugin lokal: tampilkan persentase langsung di setiap potongan doughnut,
// tanpa menghilangkan hover + tooltip bawaan Chart.js.
const doughnutPercentageLabels = {
    id: 'doughnutPercentageLabels',
    afterDatasetsDraw(chart) {
        const dataset = chart.data.datasets[0];
        const meta = chart.getDatasetMeta(0);
        if (!dataset || !meta || !meta.data.length) return;

        const data = dataset.data.map(value => Number(value) || 0);
        const isVisible = data.map((_, index) => chart.getDataVisibility(index));
        const total = data.reduce((sum, value, index) => sum + (isVisible[index] ? value : 0), 0);
        if (total <= 0) return;

        const { ctx } = chart;
        ctx.save();
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        meta.data.forEach((element, index) => {
            const value = data[index] || 0;
            if (value <= 0 || !isVisible[index]) return;

            // Perkecil font untuk potongan kecil agar tidak saling bertumpuk.
            const pct = (value / total) * 100;
            const fontSize = pct >= 10 ? 13 : pct >= 4 ? 11 : 9;
            const { x, y } = element.tooltipPosition();

            ctx.font = `400 ${fontSize}px 'Source Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`;
            // Outline gelap supaya angka tetap terbaca di warna terang maupun gelap.
            ctx.lineWidth = 3;
            ctx.strokeStyle = 'rgba(15, 23, 42, 0.55)';
            ctx.strokeText(`${pct.toFixed(1)}%`, x, y);
            ctx.fillStyle = '#ffffff';
            ctx.fillText(`${pct.toFixed(1)}%`, x, y);
        });

        ctx.restore();
    }
};

const STATUS_PR_LABELS = ['BELUM PR', 'PR PARSIAL', 'SUDAH PR', 'STOK DAN PR', 'STOK'];
const STATUS_PR_COLORS = ['#EF553B', '#FFA15A', '#00CC96', '#636EFA', '#AB63FA'];
const GROUP_PALETTE    = ['#0068c9', '#83c9ff', '#ff2b2b', '#ffabab', '#29b09d', '#7defa1', '#ff8700', '#ffd16a'];

const STATUS_TABLE_KEYS = {
    'BELUM PR': 'belum',
    'PR PARSIAL': 'parsial',
    'SUDAH PR': 'sudah',
    'STOK DAN PR': 'stokpr',
    'STOK': 'stok',
    'STATUS LAIN / TIDAK DIKENAL': 'lain'
};

function buildParams(extra = {}) {
    const params = new URLSearchParams({
        proyek: filters.proyek,
        group: filters.group,
        trainset: filters.trainset
    });

    if (filters.selectedStatuses.size > 0) {
        params.set('statuses', Array.from(filters.selectedStatuses).join(','));
    }

    Object.entries(extra).forEach(([key, value]) => {
        if (value !== '' && value !== null && value !== undefined) {
            params.set(key, value);
        }
    });

    return params;
}

function renderSummary(summary) {
    if (!summary) return;

    const total = Number(summary.totalItem || 0);
    const grouped = Number(summary.groupedItem || 0);
    const jumlahMaterial = Number(summary.jumlahMaterial || 0);

    $('#kpi-total-item').text(total.toLocaleString('id-ID'));
    $('#kpi-jumlah-material').text(jumlahMaterial.toLocaleString('id-ID'));
    $('#bomCountBadge').text(grouped.toLocaleString('id-ID'));

    updateStatusPrTable(summary);
    updateCharts(summary);
}

function updateStatusPrTable(summary) {
    const matrix = summary.statusMatrix || {};
    const groups = Array.isArray(summary.matrixGroups) ? summary.matrixGroups : [];

    const headerCells = ['<th class="st-sticky-col" style="min-width: 220px;">Status PR</th>']
        .concat(groups.map(group => `<th class="text-end" style="min-width: 140px; white-space: nowrap;">${$('<div>').text(group).html()}</th>`))
        .join('');
    $('#table-status-pr-head').html(`<tr>${headerCells}</tr>`);

    const bodyRows = Object.keys(STATUS_TABLE_KEYS).map(status => {
        const counts = matrix[status] || {};
        const cells = groups.map(group => {
            const value = Number(counts[group] || 0).toLocaleString('id-ID');
            return `<td class="text-end" style="white-space: nowrap;">${value}</td>`;
        }).join('');

        return `<tr><td class="fw-semibold st-sticky-col" style="min-width: 220px;">${$('<div>').text(status).html()}</td>${cells}</tr>`;
    }).join('');

    $('#table-status-pr-body').html(bodyRows);
}

function renderDoughnut(instance, canvasId, labels, values, colors, unitName) {
    if (instance) {
        instance.data.labels = labels;
        instance.data.datasets[0].data = values;
        instance.data.datasets[0].backgroundColor = colors;
        instance.update();
        return instance;
    }

    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    const chart = new Chart(canvas, {
        type: 'doughnut',
        plugins: [doughnutPercentageLabels],
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 10,
                hoverBorderWidth: 2,
                hoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '30%',
            animation: { duration: 600 },
            plugins: {
                legend: sharedLegendConfig,
                tooltip: createTooltipConfig(unitName)
            }
        }
    });
    // Hapus tooltip CoreUI global dari main.js
    canvas.parentNode.querySelectorAll('div.chartjs-tooltip').forEach(el => el.remove());
    return chart;
}

function updateCharts(summary) {
    // Chart Group: mengikuti ringkasan group dari server.
    const groupCounts = summary.groupCounts || {};
    const groupLabels = Object.keys(groupCounts).sort();
    const groupValues = groupLabels.map(group => Number(groupCounts[group] || 0));
    const groupColors = groupLabels.map((_, i) => GROUP_PALETTE[i % GROUP_PALETTE.length]);

    chartGroupInstance = renderDoughnut(
        chartGroupInstance, 'chart-group', groupLabels, groupValues, groupColors, 'item'
    );

    // Chart Status PR: 6 status, hanya yang > 0.
    const statusCounts = summary.statusTotals || {};
    const visible = STATUS_PR_LABELS.filter(status => Number(statusCounts[status] || 0) > 0);

    chartStatusPrInstance = renderDoughnut(
        chartStatusPrInstance,
        'chart-status-pr',
        visible,
        visible.map(status => Number(statusCounts[status] || 0)),
        visible.map(status => STATUS_PR_COLORS[STATUS_PR_LABELS.indexOf(status)]),
        'material'
    );
}

function formatStatusBadge(status) {
    if (status === 'SUDAH PR') {
        return '<span class="st-badge st-badge-success">SUDAH PR</span>';
    } else if (status === 'BELUM PR') {
        return '<span class="st-badge st-badge-danger">BELUM PR</span>';
    } else if (status === 'STOK') {
        return '<span class="st-badge st-badge-purple">STOK</span>';
    } else if (status === 'PR PARSIAL') {
        return '<span class="st-badge st-badge-warning">PR PARSIAL</span>';
    } else {
        return '<span class="st-badge st-badge-secondary">' + status + '</span>';
    }
}

function initBomTable() {
    bomDataTableInstance = $('#bom-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthChange: false,
        ordering: true,
        searching: true,
        info: true,
        autoWidth: false,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        order: [[0, 'asc']],
        ajax: function (data, callback) {
            const page = Math.floor(data.start / data.length) + 1;
            const params = buildParams({
                page: page,
                per_page: data.length,
                search: data.search.value || ''
            });

            if (data.order && data.order.length > 0) {
                const column = data.columns[data.order[0].column];

                if (column && column.data && column.orderable !== false) {
                    params.set('sort', column.data);
                    params.set('dir', data.order[0].dir);
                }
            }

            fetchDataOnce('/material-planning/data?' + params.toString())
                .then(json => {
                    renderSummary(json.summary);

                    callback({
                        draw: data.draw,
                        recordsTotal: json.summary ? json.summary.groupedItem : json.meta.total,
                        recordsFiltered: json.meta.total,
                        data: json.data
                    });
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal memuat data: ' + err.message);

                    callback({
                        draw: data.draw,
                        recordsTotal: 0,
                        recordsFiltered: 0,
                        data: []
                    });
                });
        },
        columns: [
            { data: 'no', className: 'fw-semibold text-center' },
            { data: 'group' },
            { data: 'tindak_lanjut', render: $.fn.dataTable.render.text() },
            { data: 'deskripsi', render: $.fn.dataTable.render.text() },
            { data: 'kebutuhan', className: 'text-end' },
            { data: 'qty_pr', className: 'text-end' },
            { data: 'kode_material' },
            {
                data: 'trainset',
                render: (data) => {
                    return `<span title="${data}" style="
                        display: inline-block;
                        max-width: 150px;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        vertical-align: middle;
                    ">${data}</span>`;
                }
            },
            {
                data: 'status_pr',
                className: 'text-center',
                render: (data) => formatStatusBadge(data)
            }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari material...",
            paginate: {
                previous: "‹",
                next: "›"
            },
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ item",
            processing: "Memuat data..."
        }
    });
}

function loadData() {
    if (bomDataTableInstance) {
        bomDataTableInstance.ajax.reload(null, true);
        return;
    }

    initBomTable();
}

function exportBom(format = 'csv') {
    const params = buildParams({ format });

    if (bomDataTableInstance && bomDataTableInstance.search()) {
        params.set('search', bomDataTableInstance.search());
    }

    window.location.href = '/material-planning/export?' + params.toString();
}

function setButtonBusy(busy) {
    const button = document.getElementById('btnBomGenerate');
    if (!button) return;

    button.disabled = busy;
    button.innerHTML = busy
        ? '<span class="spinner-border spinner-border-sm me-1"></span> Mengimpor...'
        : '⚙️ Generate & Rekap Data';
}

function showUploadResult(type, message) {
    $('#bomUploadProgress').addClass('d-none');
    $('#bomUploadResult').removeClass('d-none').html(
        '<div class="alert alert-' + type + ' py-2 small">' + message + '</div>'
    );
}

// Backoff: 8 request maksimal per import (dulu 80 × tiap 1,5 detik).
// Polling dijeda saat tab tidak aktif dan dibatalkan bila import baru dimulai.
const POLL_DELAYS_MS = [0, 1000, 2000, 4000, 7000, 10000, 10000, 10000];
let activePollController = null;

function pollImportStatus(importId, options = {}) {
    if (activePollController) activePollController.abort();

    const controller = new AbortController();
    activePollController = controller;

    const onCompleted = options.onCompleted || function () {};
    const onFailed = options.onFailed || function () {};
    const onTimeout = options.onTimeout || function () {};
    const onError = options.onError || function () {};

    function finish() {
        if (activePollController === controller) activePollController = null;
    }

    function step(attempt) {
        if (controller.signal.aborted) return;

        if (document.hidden) {
            setTimeout(() => step(attempt), 3000);
            return;
        }

        fetch('/material-planning/import-status/' + encodeURIComponent(importId), {
            headers: { 'Accept': 'application/json' },
            signal: controller.signal
        })
            .then(res => res.json().then(json => ({ ok: res.ok, status: res.status, json })))
            .then(({ ok, status, json }) => {
                if (controller.signal.aborted) return;

                if (!ok) {
                    const message = json.message || ('HTTP ' + status);
                    if (status === 404 || status === 422) {
                        finish();
                        onFailed(message);
                        return;
                    }
                    throw new Error(message);
                }

                if (json.status === 'completed') {
                    finish();
                    onCompleted(json);
                } else if (json.status === 'failed') {
                    finish();
                    onFailed(json.message || 'Import gagal. Periksa log server.');
                } else if (attempt + 1 >= POLL_DELAYS_MS.length) {
                    finish();
                    onTimeout('Proses masih berjalan di background. Silakan cek kembali nanti.');
                } else {
                    setTimeout(() => step(attempt + 1), POLL_DELAYS_MS[attempt + 1]);
                }
            })
            .catch(err => {
                if (err.name === 'AbortError' || controller.signal.aborted) return;
                finish();
                onError('Gagal memeriksa status import: ' + err.message);
            });
    }

    step(0);
}

document.addEventListener('DOMContentLoaded', () => {
    filters.trainset = $('#select-trainset').val();
    loadData();

    // 2. Expander Accordion Toggle
    const expanderToggle = document.getElementById('expanderToggle');
    const expanderBody = document.getElementById('expanderBody');
    const expanderChevron = document.getElementById('expanderChevron');

    if (expanderToggle && expanderBody) {
        expanderToggle.addEventListener('click', () => {
            const isExpanded = expanderBody.style.display !== 'none';
            expanderBody.style.display = isExpanded ? 'none' : 'block';
            expanderChevron.style.transform = isExpanded ? 'rotate(-90deg)' : 'rotate(0deg)';
        });
    }

    // 3. Dropdown filter changes
    $('#select-proyek').on('change', function() {
        filters.proyek = $(this).val();
        scheduleLoad();
    });

    $('#select-group').on('change', function() {
        filters.group = $(this).val();
        scheduleLoad();
    });

    $('#select-trainset').on('change', function() {
        filters.trainset = $(this).val();
        scheduleLoad();
    });

    // 4. Status PR Switches
    $('.status-filter-switch').on('change', function() {
        const status = $(this).data('status');
        if (this.checked) {
            filters.selectedStatuses.add(status);
        } else {
            filters.selectedStatuses.delete(status);
        }
        scheduleLoad();
    });

    // 5. Export Buttons
    $('#btnExportBomCsv').on('click', () => exportBom('csv'));
    $('#btnExportBomXlsx').on('click', () => exportBom('xlsx'));

    // 6. File Uploader UI in Expander
    const bomFileInput = document.getElementById('bomFileInput');
    const bomFileLabel = document.getElementById('bomFileLabel');
    const bomStatusText = document.getElementById('bomStatusText');
    const btnBomGenerate = document.getElementById('btnBomGenerate');
    const bomDropzone = document.getElementById('bomDropzone');

    if (bomFileInput) {
        bomFileInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                const file = e.target.files[0];
                bomFileLabel.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                bomStatusText.textContent = 'File siap: ' + file.name;
                bomStatusText.className = 'text-success small fw-semibold';
            }
        });
    }

    if (bomDropzone) {
        bomDropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            bomDropzone.classList.add('dragover');
        });
        bomDropzone.addEventListener('dragleave', () => {
            bomDropzone.classList.remove('dragover');
        });
        bomDropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            bomDropzone.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                bomFileInput.files = e.dataTransfer.files;
                const file = e.dataTransfer.files[0];
                bomFileLabel.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                bomStatusText.textContent = 'File siap: ' + file.name;
                bomStatusText.className = 'text-success small fw-semibold';
            }
        });
    }

    // 7. Generate & Rekap Data Button (Upload BOM via /material-planning/import-excel)
    if (btnBomGenerate) {
        btnBomGenerate.addEventListener('click', () => {
            if (!bomFileInput.files.length) {
                alert('Pilih file Excel BOM terlebih dahulu.');
                return;
            }

            const file = bomFileInput.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            setButtonBusy(true);
            $('#bomUploadProgress').removeClass('d-none');
            $('#bomUploadResult').addClass('d-none').html('');

            $.ajax({
                url: '/material-planning/import-excel',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success && res.import_id) {
                        pollImportStatus(res.import_id, {
                            onCompleted: function (json) {
                                setButtonBusy(false);
                                showUploadResult(
                                    'success',
                                    'Import selesai — ' + Number(json.imported || 0).toLocaleString('id-ID') + ' baris data berhasil direkap.'
                                );
                                setTimeout(() => {
                                    location.reload()
                                }, 300)
                                clearDataCache();
                                loadData();
                            },
                            onFailed: function (message) {
                                setButtonBusy(false);
                                showUploadResult('danger', message);
                            },
                            onTimeout: function (message) {
                                setButtonBusy(false);
                                showUploadResult('warning', message);
                            },
                            onError: function (message) {
                                setButtonBusy(false);
                                showUploadResult('danger', message);
                            }
                        });
                    } else {
                        setButtonBusy(false);
                        showUploadResult('danger', res.message || 'Gagal memproses import.');
                    }
                },
                error: function(xhr) {
                    setButtonBusy(false);
                    const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat import.';
                    showUploadResult('danger', message);
                }
            });
        });
    }

    // 8. Import Purchase Requisition (Modal handling)
    $('#btnImport').on('click', function () {
        const form = document.getElementById('importForm');
        const fileInput = document.getElementById('importFile');
        const resultDiv = $('#importResult');
        const progressDiv = $('#importProgress');
        const btn = $(this);

        if (!fileInput.files.length) {
            resultDiv.removeClass('d-none').html(
                '<div class="alert alert-warning py-2 small">Pilih file terlebih dahulu.</div>'
            );
            return;
        }

        btn.prop('disabled', true).text('Mengimpor...');
        progressDiv.removeClass('d-none');
        resultDiv.addClass('d-none').html('');

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '/material-planning/import-queue',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success && res.import_id) {
                    progressDiv.addClass('d-none');
                    resultDiv.removeClass('d-none').html(
                        '<div class="alert alert-info py-2 small">' + res.message + '</div>'
                    );

                    const resetButton = () => btn.prop('disabled', false).text('Import');

                    pollImportStatus(res.import_id, {
                        onCompleted: function (json) {
                            resetButton();
                            resultDiv.removeClass('d-none').html(
                                '<div class="alert alert-success py-2 small">Import selesai — '
                                + Number(json.imported || 0).toLocaleString('id-ID')
                                + ' baris data berhasil diimpor.</div>'
                            );
                            // Tidak perlu reload seluruh halaman: cukup invalidasi cache
                            // dan muat ulang tabel (1 request, bukan +10 request aset).
                            clearDataCache();
                            loadData();
                        },
                        onFailed: function (message) {
                            resetButton();
                            resultDiv.removeClass('d-none').html(
                                '<div class="alert alert-danger py-2 small">' + message + '</div>'
                            );
                        },
                        onTimeout: function (message) {
                            resetButton();
                            resultDiv.removeClass('d-none').html(
                                '<div class="alert alert-warning py-2 small">' + message + '</div>'
                            );
                        },
                        onError: function (message) {
                            resetButton();
                            resultDiv.removeClass('d-none').html(
                                '<div class="alert alert-danger py-2 small">' + message + '</div>'
                            );
                        }
                    });
                } else {
                    progressDiv.addClass('d-none');
                    btn.prop('disabled', false).text('Import');
                    resultDiv.removeClass('d-none').html(
                        '<div class="alert alert-danger py-2 small">' + (res.message || 'Gagal import.') + '</div>'
                    );
                }
            },
            error: function (xhr) {
                progressDiv.addClass('d-none');
                btn.prop('disabled', false).text('Import');
                const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat import.';
                resultDiv.removeClass('d-none').html(
                    '<div class="alert alert-danger py-2 small">' + msg + '</div>'
                );
            }
        });
    });

    // 9. Existing Deviasi Table (DataTable)
    if ($('#deviasi-table').length) {
        $('#deviasi-table').DataTable({
            processing: false,
            serverSide: true,
            responsive: false,
            scrollX: true,
            scrollCollapse: true,
            ajax: function (data, callback, settings) {
                $.get("/sap-dataset/deviation", {
                    page: (data.start / data.length) + 1,
                    per_page: data.length,
                    search: data.search.value
                }).done(function (json) {
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
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (data, type, row, meta) => meta.row + 1
                },
                { data: 'pr_number', name: 'pr_number' },
                { data: 'wbs_elem', name: 'wbs_elem' },
                { data: 'wbs_description', name: 'wbs_description' },
                { data: 'material_code', name: 'material_code' },
                { data: 'material_description', name: 'material_description' },
                { data: 'total_records', name: 'total_records', className: 'text-center' },
                { data: 'total_requested', name: 'total_requested', className: 'text-end' },
                { data: 'total_ordered', name: 'total_ordered', className: 'text-end' },
                { data: 'selisih', name: 'selisih', className: 'text-end fw-semibold text-danger' },
                { data: 'unit', name: 'unit' },
                { data: 'po_number', name: 'po_number' },
                { data: 'created_by', name: 'created_by' }
            ]
        });
    }
});

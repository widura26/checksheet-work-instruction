let bomDataset = [];

let chartGroupInstance = null;
let chartStatusPrInstance = null;
let bomDataTableInstance = null;

const filters = {
    proyek: 'Semua',
    group: 'Semua',
    trainset: 'Semua',
    selectedStatuses: new Set()
};

let loadSeq = 0;
async function loadData() {
    const seq = ++loadSeq;
    const params = new URLSearchParams({
        proyek: filters.proyek,
        group: filters.group,
        trainset: filters.trainset
    });

    $('#bomCountBadge').text('...');
    let result = [];
    try {
        const res = await fetch('/material-planning/data?' + params, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        result = await res.json();
    } catch (err) {
        console.error(err);
        if (seq === loadSeq) alert('Gagal memuat data: ' + err.message);
    }

    if (seq !== loadSeq) return;   // ada request yang lebih baru, abaikan yang ini
    bomDataset = result;
    applyFilters();
}

function getFilteredData() {
    if (filters.selectedStatuses.size === 0) return bomDataset;
    return bomDataset.filter(item => filters.selectedStatuses.has(item.status_pr));
}

function updateMetrics(data) {
    const total = data.length;
    const jumlahMaterial = new Set(data.map(d => d.kode_material)).size;

    $('#kpi-total-item').text(total.toLocaleString('id-ID'));
    $('#kpi-jumlah-material').text(jumlahMaterial.toLocaleString('id-ID'));
    $('#bomCountBadge').text(total.toLocaleString('id-ID'));
}

function updateStatusPrTable(data) {
    const counts = {
        'BELUM PR': { komp: 0, piece: 0 },
        'PR PARSIAL': { komp: 0, piece: 0 },
        'SUDAH PR': { komp: 0, piece: 0 },
        'STOK DAN PR': { komp: 0, piece: 0 },
        'STOK': { komp: 0, piece: 0 },
        'STATUS LAIN / TIDAK DIKENAL': { komp: 0, piece: 0 },
    };

    data.forEach(item => {
        const st = item.status_pr;
        if (counts[st]) {
            if (item.group === 'KOMPONEN') {
                counts[st].komp++;
            } else {
                counts[st].piece++;
            }
        }
    });

    $('#sum-belum-komp').text(counts['BELUM PR'].komp);
    $('#sum-belum-piece').text(counts['BELUM PR'].piece);

    $('#sum-parsial-komp').text(counts['PR PARSIAL'].komp);
    $('#sum-parsial-piece').text(counts['PR PARSIAL'].piece);

    $('#sum-sudah-komp').text(counts['SUDAH PR'].komp);
    $('#sum-sudah-piece').text(counts['SUDAH PR'].piece);

    $('#sum-stokpr-komp').text(counts['STOK DAN PR'].komp);
    $('#sum-stokpr-piece').text(counts['STOK DAN PR'].piece);

    $('#sum-stok-komp').text(counts['STOK'].komp);
    $('#sum-stok-piece').text(counts['STOK'].piece);

    $('#sum-lain-komp').text(counts['STATUS LAIN / TIDAK DIKENAL'].komp);
    $('#sum-lain-piece').text(counts['STATUS LAIN / TIDAK DIKENAL'].piece);
}

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

const STATUS_PR_LABELS = ['BELUM PR', 'PR PARSIAL', 'SUDAH PR', 'STOK DAN PR', 'STOK', 'STATUS LAIN / TIDAK DIKENAL'];
const STATUS_PR_COLORS = ['#EF553B', '#FFA15A', '#00CC96', '#636EFA', '#AB63FA', '#7F7F7F'];
const GROUP_PALETTE    = ['#0068c9', '#83c9ff', '#ff2b2b', '#ffabab', '#29b09d', '#7defa1', '#ff8700', '#ffd16a'];

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

function updateCharts(data) {
    // Chart Group: dinamis sesuai nilai 'group'
    const groupCounts = {};
    data.forEach(item => {
        const g = item.group || '(kosong)';
        groupCounts[g] = (groupCounts[g] || 0) + 1;
    });
    const groupLabels = Object.keys(groupCounts).sort();
    const groupValues = groupLabels.map(g => groupCounts[g]);
    const groupColors = groupLabels.map((_, i) => GROUP_PALETTE[i % GROUP_PALETTE.length]);

    chartGroupInstance = renderDoughnut(
        chartGroupInstance, 'chart-group', groupLabels, groupValues, groupColors, 'item'
    );

    // Chart Status PR: 6 status, hanya yang > 0
    const statusCounts = Object.fromEntries(STATUS_PR_LABELS.map(s => [s, 0]));
    data.forEach(item => {
        const key = statusCounts[item.status_pr] !== undefined
            ? item.status_pr
            : 'STATUS LAIN / TIDAK DIKENAL';
        statusCounts[key]++;
    });

    const visible = STATUS_PR_LABELS.filter(s => statusCounts[s] > 0);

    chartStatusPrInstance = renderDoughnut(
        chartStatusPrInstance,
        'chart-status-pr',
        visible,
        visible.map(s => statusCounts[s]),
        visible.map(s => STATUS_PR_COLORS[STATUS_PR_LABELS.indexOf(s)]),
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

function initBomTable(data) {
    if ($.fn.DataTable.isDataTable('#bom-table')) {
        bomDataTableInstance.clear();
        bomDataTableInstance.rows.add(data);
        bomDataTableInstance.draw();
        return;
    }

    bomDataTableInstance = $('#bom-table').DataTable({
        data: data,
        pageLength: 10,
        lengthChange: false,
        ordering: true,
        searching: true,
        info: true,
        autoWidth: false,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        columns: [
            {
                data: null,
                render: (d, type, row, meta) => meta.settings._iDisplayStart + meta.row + 1,
                className: 'text-center text-muted fw-light'
            },
            { data: 'no', className: 'fw-semibold text-center' },
            { data: 'group' },
            { data: 'tindak_lanjut', render: $.fn.dataTable.render.text() },
            { data: 'deskripsi',     render: $.fn.dataTable.render.text() },
            { data: 'kebutuhan', className: 'text-end' },
            { data: 'qty_pr', className: 'text-end' },
            { data: 'kode_material' },
            { data: 'trainset' },
            {
                data: 'status_pr',
                className: 'text-center',
                render: (data) => formatStatusBadge(data)
            }
        ],
        drawCallback: function () {
            const api = this.api();
            const start = api.page.info().start;
            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.textContent = start + i + 1;
            });
        },
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari material...",
            paginate: {
                previous: "‹",
                next: "›"
            },
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ item"
        }
    });
}

function applyFilters() {
    const data = getFilteredData();
    updateMetrics(data);
    updateStatusPrTable(data);
    updateCharts(data);
    initBomTable(data);
}

function csvCell(v) {
    return `"${String(v ?? '').replace(/"/g, '""')}"`;
}

function exportBomToCsv() {
    const data = getFilteredData();
    if (!data.length) {
        alert('Tidak ada data untuk diekspor.');
        return;
    }

    const headers = [
        'NO', 'Kode Material Konversi', 'Group', 'Tindak Lanjut',
        'Deskripsi Material', 'Kebutuhan', 'Qty PR (BOM)',
        'Kode Material', 'Trainset', 'Status PR', 'Proyek'
    ];

    const lines = [headers.map(csvCell).join(',')];
    data.forEach(i => {
        lines.push([
            i.no, i.kode_konversi, i.group, i.tindak_lanjut, i.deskripsi,
            i.kebutuhan, i.qty_pr, i.kode_material, i.trainset, i.status_pr, i.proyek
        ].map(csvCell).join(','));
    });

    // \uFEFF = BOM supaya Excel membaca UTF-8 dengan benar
    const blob = new Blob(['\uFEFF' + lines.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    link.download = 'Dataset_BOM_Monitoring.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
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
        loadData();
    });

    $('#select-group').on('change', function() {
        filters.group = $(this).val();
        loadData();
    });

    $('#select-trainset').on('change', function() {
        filters.trainset = $(this).val();
        loadData();
    });

    // 4. Status PR Switches
    $('.status-filter-switch').on('change', function() {
        const status = $(this).data('status');
        if (this.checked) {
            filters.selectedStatuses.add(status);
        } else {
            filters.selectedStatuses.delete(status);
        }
        applyFilters();
    });

    // 5. CSV Export Button
    $('#btnExportBomCsv').on('click', exportBomToCsv);

    // 6. File Uploader UI in Expander
    const bomFileInput = document.getElementById('bomFileInput');
    const bomFileLabel = document.getElementById('bomFileLabel');
    const bomStatusText = document.getElementById('bomStatusText');
    const btnStreamlitGenerate = document.getElementById('btnStreamlitGenerate');
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

    // 7. Generate & Rekap Data Button (Upload BOM via /bom/import)
    if (btnStreamlitGenerate) {
        btnStreamlitGenerate.addEventListener('click', () => {
            if (!bomFileInput.files.length) {
                alert('Pilih file Excel BOM terlebih dahulu.');
                return;
            }

            const file = bomFileInput.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            btnStreamlitGenerate.disabled = true;
            btnStreamlitGenerate.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengimpor...';
            $('#bomStreamlitProgress').removeClass('d-none');
            $('#bomStreamlitResult').addClass('d-none').html('');

            $.ajax({
                url: '/material-planning/import-excel',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        pollBomStatus(res.import_id, 0);
                    } else {
                        btnStreamlitGenerate.disabled = false;
                        btnStreamlitGenerate.innerHTML = '⚙️ Generate & Rekap Data';
                        $('#bomStreamlitProgress').addClass('d-none');
                        $('#bomStreamlitResult').removeClass('d-none').html(
                            '<div class="alert alert-danger py-2 small">' + (res.message || 'Gagal import') + '</div>'
                        );
                    }
                },
                error: function(xhr) {
                    btnStreamlitGenerate.disabled = false;
                    btnStreamlitGenerate.innerHTML = '⚙️ Generate & Rekap Data';
                    $('#bomStreamlitProgress').addClass('d-none');
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat import.';
                    $('#bomStreamlitResult').removeClass('d-none').html(
                        '<div class="alert alert-danger py-2 small">' + msg + '</div>'
                    );
                }
            });
        });
    }

    function pollBomStatus(importId, attempt) {
        const MAX_ATTEMPTS = 50;
        fetch('/bom/status/' + encodeURIComponent(importId))
            .then(res => res.json())
            .then(data => {
                if (data.status === 'completed') {
                    loadData();
                    btnStreamlitGenerate.disabled = false;
                    btnStreamlitGenerate.innerHTML = '⚙️ Generate & Rekap Data';
                    $('#bomStreamlitProgress').addClass('d-none');
                    $('#bomStreamlitResult').removeClass('d-none').html(
                        '<div class="alert alert-success py-2 small">Import selesai — ' + data.imported + ' baris data berhasil direkap.</div>'
                    );
                } else if (data.status === 'failed') {
                    btnStreamlitGenerate.disabled = false;
                    btnStreamlitGenerate.innerHTML = '⚙️ Generate & Rekap Data';
                    $('#bomStreamlitProgress').addClass('d-none');
                    $('#bomStreamlitResult').removeClass('d-none').html(
                        '<div class="alert alert-danger py-2 small">Import gagal. Periksa log server.</div>'
                    );
                } else if (attempt + 1 >= MAX_ATTEMPTS) {
                    btnStreamlitGenerate.disabled = false;
                    btnStreamlitGenerate.innerHTML = '⚙️ Generate & Rekap Data';
                    $('#bomStreamlitProgress').addClass('d-none');
                    $('#bomStreamlitResult').removeClass('d-none').html(
                        '<div class="alert alert-warning py-2 small">Proses sedang berjalan di background...</div>'
                    );
                } else {
                    setTimeout(() => pollBomStatus(importId, attempt + 1), 1500);
                }
            })
            .catch(err => {
                btnStreamlitGenerate.disabled = false;
                btnStreamlitGenerate.innerHTML = '⚙️ Generate & Rekap Data';
                $('#bomStreamlitProgress').addClass('d-none');
                $('#bomStreamlitResult').removeClass('d-none').html(
                    '<div class="alert alert-danger py-2 small">Gagal memeriksa status: ' + err.message + '</div>'
                );
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
            url: '/sap-dataset/import-queue',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                progressDiv.addClass('d-none');
                btn.prop('disabled', false).text('Import');

                if (res.success) {
                    let html = '<div class="alert alert-success py-2 small">' + res.message + '</div>';
                    resultDiv.removeClass('d-none').html(html);
                    form.reset();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    resultDiv.removeClass('d-none').html(
                        '<div class="alert alert-danger py-2 small">' + res.message + '</div>'
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

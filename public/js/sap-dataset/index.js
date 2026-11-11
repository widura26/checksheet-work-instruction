import { bomDataset } from './bom-data.js';

let chartGroupInstance = null;
let chartStatusPrInstance = null;
let bomDataTableInstance = null;

// Filter state
const filters = {
    proyek: 'Semua',
    group: 'Semua',
    trainset: 'Semua',
    selectedStatuses: new Set()
};

function getFilteredData() {
    return bomDataset.filter(item => {
        if (filters.proyek !== 'Semua' && item.proyek !== filters.proyek) return false;
        if (filters.group !== 'Semua' && item.group !== filters.group) return false;
        if (filters.trainset !== 'Semua' && item.trainset !== filters.trainset) return false;
        if (filters.selectedStatuses.size > 0 && !filters.selectedStatuses.has(item.status_pr)) return false;
        return true;
    });
}

function updateMetrics(data) {
    const total = data.length;
    $('#kpi-total-item').text(total);
    $('#kpi-jumlah-material').text(total);
    $('#bomCountBadge').text(total);
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

function updateCharts(data) {
    // 1. Group Chart Data
    let piecePartCount = 0;
    let komponenCount = 0;
    data.forEach(item => {
        if (item.group === 'KOMPONEN') komponenCount++;
        else piecePartCount++;
    });

    const groupLabels = ['PIECE PART', 'KOMPONEN'];
    const groupValues = [piecePartCount, komponenCount];
    const groupColors = ['#0068c9', '#83c9ff'];

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

    if (chartGroupInstance) {
        chartGroupInstance.data.datasets[0].data = groupValues;
        chartGroupInstance.update();
    } else {
        const ctxGroup = document.getElementById('chart-group');
        if (ctxGroup) {
            chartGroupInstance = new Chart(ctxGroup, {
                type: 'doughnut',
                data: {
                    labels: groupLabels,
                    datasets: [{
                        data: groupValues,
                        backgroundColor: groupColors,
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
                    animation: {
                        duration: 600
                    },
                    plugins: {
                        legend: sharedLegendConfig,
                        tooltip: createTooltipConfig('item')
                    }
                }
            });
            // Remove any CoreUI chartjs-tooltip div injected by main.js global external handler
            ctxGroup.parentNode.querySelectorAll('div.chartjs-tooltip').forEach(el => el.remove());
        }
    }

    const statusCounts = {
        'SUDAH PR': 0,
        'BELUM PR': 0,
        'STOK': 0,
        'STATUS LAIN / TIDAK DIKENAL': 0
    };

    data.forEach(item => {
        if (statusCounts[item.status_pr] !== undefined) {
            statusCounts[item.status_pr]++;
        } else {
            statusCounts['STATUS LAIN / TIDAK DIKENAL']++;
        }
    });

    const statusLabels = ['SUDAH PR', 'BELUM PR', 'STOK', 'STATUS LAIN / TIDAK DIKENAL'];
    const statusValues = [
        statusCounts['SUDAH PR'],
        statusCounts['BELUM PR'],
        statusCounts['STOK'],
        statusCounts['STATUS LAIN / TIDAK DIKENAL']
    ];
    const statusColors = ['#00CC96', '#EF553B', '#AB63FA', '#7F7F7F'];

    if (chartStatusPrInstance) {
        chartStatusPrInstance.data.datasets[0].data = statusValues;
        chartStatusPrInstance.update();
    } else {
        const ctxStatus = document.getElementById('chart-status-pr');
        if (ctxStatus) {
            chartStatusPrInstance = new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusColors,
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
                    animation: {
                        duration: 600
                    },
                    plugins: {
                        legend: sharedLegendConfig,
                        tooltip: createTooltipConfig('material')
                    }
                }
            });
            // Remove any CoreUI chartjs-tooltip div injected by main.js global external handler
            ctxStatus.parentNode.querySelectorAll('div.chartjs-tooltip').forEach(el => el.remove());
        }
    }
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
                render: (data, type, row, meta) => meta.row + 1,
                className: 'text-center text-muted fw-light'
            },
            { data: 'no', className: 'fw-semibold text-center' },
            { data: 'kode_konversi' },
            { data: 'group' },
            { data: 'tindak_lanjut' },
            { data: 'deskripsi' },
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

// Export BOM dataset to CSV
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
    let csvContent = 'data:text/csv;charset=utf-8,' + headers.join(',') + '\n';
    data.forEach(item => {
        const row = [
            item.no,
            `"${item.kode_konversi}"`,
            `"${item.group}"`,
            `"${item.tindak_lanjut}"`,
            `"${item.deskripsi}"`,
            item.kebutuhan,
            item.qty_pr,
            `"${item.kode_material}"`,
            `"${item.trainset}"`,
            `"${item.status_pr}"`,
            `"${item.proyek}"`
        ];
        csvContent += row.join(',') + '\n';
    });
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'Dataset_BOM_Monitoring.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial render of Streamlit Dashboard Components
    applyFilters();

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
        applyFilters();
    });

    $('#select-group').on('change', function() {
        filters.group = $(this).val();
        applyFilters();
    });

    $('#select-trainset').on('change', function() {
        filters.trainset = $(this).val();
        applyFilters();
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

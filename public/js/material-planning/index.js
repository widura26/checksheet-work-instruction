const random = () => Math.round(Math.random() * 100);

function initBomAnalytics() {
    $.get("/project-code/data", { per_page: 1000 }).done(function (json) {
        const data = json.data || [];
        const total = json.total || 0;

        const projectCounts = {};
        data.forEach(function (row) {
            const name = row.name || row.project_code || 'Unknown';
            projectCounts[name] = (projectCounts[name] || 0) + 1;
        });

        const labels = Object.keys(projectCounts);
        const series = Object.values(projectCounts);

        $('#total-bom').text(total);
        $('#total-proyek').text(labels.length);

        const chartEl = document.querySelector('#bom-distribusi-chart');
        if (!chartEl || typeof ApexCharts === 'undefined') return;

        const chart = new ApexCharts(chartEl, {
            chart: { type: 'bar', height: 300, toolbar: { show: true } },
            series: [{ name: 'Jumlah BOM', data: series }],
            xaxis: { categories: labels, labels: { rotate: -45, style: { fontSize: '12px' } } },
            colors: ['#2E93fA'],
            plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '60%' } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#e7e9ed' },
            tooltip: { y: { formatter: function (val) { return val + ' entri'; } } }
        });
        chart.render();
    });
}

let pieChart1 = async (data) => {
    const piechart = new Chart(document.getElementById('canvas-5'), {
        type: 'doughnut',
        data: {
            labels: ["Belum PR", "Sudah PR"],
            datasets: [{
                data: [data.belum_pr, data.sudah_pr],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                hoverBackgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true
        }
    });
    piechart.render()
}

const pieChart2 = new Chart(document.getElementById('canvas-6'), {
    type: 'doughnut',
    data: {
        labels: ['Sudah PO', 'Belum PO'],
        datasets: [{
            data: [300, 50],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            hoverBackgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
        }]
    },
    options: {
        responsive: true
    }
});

const labels = ['Purchase Requisition', 'Purchase Order'];
const data = {
    labels: labels,
    datasets: [
        {
            label: 'My First Dataset',
            data: [65, 59],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 159, 64, 0.2)',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
            ],
            borderWidth: 1
        },
        {
            label: 'My First Dataset',
            data: [65, 59],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 159, 64, 0.2)',
            ],
            borderColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
            ],
            borderWidth: 1
        },
    ]
};

const barChart = new Chart(document.getElementById('canvas-7'), {
    type: 'bar',
    data: data,
    options: {
        showTooltips: false,
        plugins: {
            legend: {
                display: false,
                labels: {
                    color: 'rgb(255, 99, 132)'
                },
                position: 'top'
            },
            tooltip: {
                enabled: true
            }
        }
    }
});

const barChart1 = new Chart(document.getElementById('canvas-8'), {
    type: 'bar',
    data: {
        labels: ['Bill Of Material', 'Purchase Requisition'],
        datasets: [
            {
                label: 'My First Dataset',
                data: [30, 70],
                fill: false,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)'
                ],
                borderWidth: 1
            },
        ]
    },
    options: {
        indexAxis: 'y',
        showTooltips: false,
        plugins: {
            legend: {
                display: false,
                labels: {
                    color: 'rgb(255, 99, 132)'
                },
                position: 'top'
            },
            tooltip: {
                enabled: true
            }
        }
    }
});

const barChart2 = new Chart(document.getElementById('canvas-9'), {
    type: 'bar',
    data: {
        labels: ['Bill Of Material', 'Purchase Requisition'],
        datasets: [
            {
                label: 'My First Dataset',
                data: [30, 70],
                fill: false,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)'
                ],
                borderWidth: 1
            },
        ]
    },
    options: {
        indexAxis: 'y',
        showTooltips: false,
        plugins: {
            legend: {
                display: false,
                labels: {
                    color: 'rgb(255, 99, 132)'
                },
                position: 'top'
            },
            tooltip: {
                enabled: true
            }
        }
    }
});

pieChart2.render()
barChart.render()
barChart1.render()
barChart2.render()

/* Import Purchase Requisition */
const importModal = new coreui.Modal(document.getElementById('importExcelModal'));

$('#btnImport').on('click', function () {
    const form = document.getElementById('importForm');
    const fileInput = document.getElementById('importFile');
    const resultDiv = $('#importResult');
    const progressDiv = $('#importProgress');
    const btn = $(this);

    if (!fileInput.files.length) {
        resultDiv.removeClass('d-none').html(
            '<div class="alert alert-warning">Pilih file terlebih dahulu.</div>'
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
        url: '/material-planning/import',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            progressDiv.addClass('d-none');
            btn.prop('disabled', false).text('Import');

            if (res.success) {
                let html = '<div class="alert alert-success">' + res.message + '</div>';
                if (res.errors && res.errors.length > 0) {
                    html += '<div class="alert alert-warning"><strong>Error:</strong><ul class="mb-0 mt-1">';
                    res.errors.forEach(function (err) {
                        html += '<li>' + err + '</li>';
                    });
                    html += '</ul></div>';
                }
                resultDiv.removeClass('d-none').html(html);
                form.reset();
            } else {
                resultDiv.removeClass('d-none').html(
                    '<div class="alert alert-danger">' + res.message + '</div>'
                );
            }
        },
        error: function (xhr) {
            progressDiv.addClass('d-none');
            btn.prop('disabled', false).text('Import');
            const msg = xhr.responseJSON?.message || 'Terjadi kesalahan saat import.';
            console.log(xhr.responseJSON?.message)
            resultDiv.removeClass('d-none').html(
                '<div class="alert alert-danger">' + msg + '</div>'
            );
        }
    });
});

const getPurchaseRequisitionData = async () => {
    const response = await axios.get('/charts/purchase-requisitions');
    return response.data.jenis_pr;
}

document.addEventListener('DOMContentLoaded', async () => {
    const prdata = await getPurchaseRequisitionData()
    console.log(prdata);
    pieChart1(prdata)
})

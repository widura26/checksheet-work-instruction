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

const pieChart1 = new Chart(document.getElementById('canvas-5'), {
    type: 'doughnut',
    data: {
        labels: ['Sudah PR', 'Belum PR'],
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

pieChart1.render()
pieChart2.render()
barChart.render()
barChart1.render()
barChart2.render()

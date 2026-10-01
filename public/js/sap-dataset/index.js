const random = () => Math.round(Math.random() * 100);

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

const pieChart1 = async (data) => {
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
            responsive: true,
            showTooltips: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
    piechart.render()
}

const pieChart2 = async (data) => {
    const piechart = new Chart(document.getElementById('canvas-6'), {
        type: 'doughnut',
        data: {
            labels: ['Belum PO', 'Sudah PO'],
            datasets: [{
                data: [data.belum_po, data.sudah_po],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                hoverBackgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            showTooltips: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });

    piechart.render()
}

const barChart = async (data) => {
    const { prdata, podata } = data;
    const c = new Chart(document.getElementById('canvas-7'), {
        type: 'bar',
        data: {
            labels: ['Purchase Requisition', 'Purchase Order'],
            datasets: [
                {
                    label: 'My First Dataset',
                    data: [prdata.belum_pr, prdata.sudah_pr],
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
                    data: [podata.belum_po, podata.sudah_po],
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
        },
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
    c.render()
}

/* Import Purchase Requisition */
const importModal = new coreui.Modal(document.getElementById('importPrModal'));

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
        url: '/sap-dataset/import-queue',
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

const getPurchaseOrderData = async () => {
    const response = await axios.get('/charts/purchase-order');
    return response.data.jenis_po
}

const getBomPrData = async () => {
    const response = await axios.get('/charts/bompr');
    return response.data.bompr
}

const getPoPrData = async () => {
    const response = await axios.get('/charts/popr')
    return response.data.popr
}

document.addEventListener('DOMContentLoaded', async () => {
    const prdata = await getPurchaseRequisitionData();
    const podata = await getPurchaseOrderData();
    const bomprdata = await getBomPrData();
    const PoPrData = await getPoPrData();

    pieChart1(prdata)
    pieChart2(podata)

    const prpo = {
        "prdata": prdata,
        "podata": podata
    }
    barChart(prpo)
})

$(function() {
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
        columns: [{
                data: null,
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {
                data: 'pr_number',
                name: 'pr_number'
            },
            {
                data: 'wbs_elem',
                name: 'wbs_elem'
            },
            {
                data: 'wbs_description',
                name: 'wbs_description'
            },
            {
                data: 'material_code',
                name: 'material_code'
            },
            {
                data: 'material_description',
                name: 'material_description'
            },
            {
                data: 'total_records',
                name: 'total_records',
                className: 'text-center'
            },
            {
                data: 'total_requested',
                name: 'total_requested',
                className: 'text-end'
            },
            {
                data: 'total_ordered',
                name: 'total_ordered',
                className: 'text-end'
            },
            {
                data: 'selisih',
                name: 'selisih',
                className: 'text-end'
            },
            {
                data: 'unit',
                name: 'unit'
            },
            {
                data: 'po_number',
                name: 'po_number'
            },
            {
                data: 'created_by',
                name: 'created_by'
            }
        ]
    });
});

document.addEventListener('click', function(e) {
    let button = e.target.closest('.btn-delete-bom');
    if (!button) return;
    let form = button.closest('form');
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Data BOM dan seluruh dokumen terkait akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            button.disabled = true;
            button.innerHTML = 'Hapus...';
            form.submit();
        }
    });
});

$(function() {
        $('#projects-table').DataTable({
            processing: false,
            serverSide: true,
            responsive: true,
            scrollX: true,
            scrollCollapse: true,
            ajax: function (data, callback, settings) {
                $.get("/bill-of-material/data", {
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
                    data: 'project_code',
                    name: 'project_code'
                },
                {
                    data: 'name_project',
                    name: 'name_project'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'number',
                    name: 'number'
                },
                {
                    data: 'published_date',
                    name: 'published_date'
                },
                {
                    data: 'revise',
                    name: 'revise'
                },
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <div class="d-flex items-center gap-1 justify-content-center">
                                <a href="/bill-of-material/${data}/edit" class="btn btn-sm btn-primary">
                                    Edit
                                </a>
                                <form action="/bill-of-material/${data}/delete" method="POST" class="d-inline delete-form">
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-bom" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        `
                    }
                }
            ]
        });

        initBomAnalytics();
});

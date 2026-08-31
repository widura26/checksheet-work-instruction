import "./bootstrap";

import $ from "jquery";
window.$ = window.jQuery = $;

// DataTables
import "datatables.net-bs5";
import "datatables.net-responsive-bs5";

const convertDate = (timestamp) => {
    const date = new Date(timestamp);
    const formatted = date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

    return formatted;
}
window.convertDate = convertDate;

// Global DataTables defaults — seragamkan dengan menu "Kode Proses"
$.fn.DataTable.defaults.info = false;
$.fn.DataTable.defaults.autoWidth = false;
$.fn.DataTable.defaults.scrollX = true;
$.fn.DataTable.defaults.scrollCollapse = true;
$.fn.DataTable.defaults.responsive = true;

// Loading indicator: tampilkan spinner (bukan teks "Processing..." / "Loading...")
if (!$.fn.DataTable.defaults.language) {
    $.fn.DataTable.defaults.language = {};
}
const dataTableSpinner =
    '<div class="spinner-border text-primary" role="status">' +
    '<span class="visually-hidden">Memuat...</span></div>';
$.fn.DataTable.defaults.language.processing = dataTableSpinner;
$.fn.DataTable.defaults.language.loadingRecords = dataTableSpinner;

// Select2
import select2 from "select2/dist/js/select2.full";
select2(window, $);
// SweetAlert2
import Swal from "sweetalert2";
window.Swal = Swal;

// ApexCharts
import ApexCharts from "apexcharts";
window.ApexCharts = ApexCharts;

import "./offline/index";

import "./bootstrap";

import $ from "jquery";
window.$ = window.jQuery = $;

// DataTables
import "datatables.net-bs5";
import "datatables.net-responsive-bs5";

// Global DataTables defaults — seragamkan dengan menu "Kode Proses"
$.fn.DataTable.defaults.info = false;
$.fn.DataTable.defaults.autoWidth = false;
$.fn.DataTable.defaults.scrollX = true;
$.fn.DataTable.defaults.scrollCollapse = true;
$.fn.DataTable.defaults.responsive = true;

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

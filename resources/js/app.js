import "./bootstrap";

import $ from "jquery";
window.$ = window.jQuery = $;

// DataTables
import "datatables.net-bs5";
import "datatables.net-responsive-bs5";

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

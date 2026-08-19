<!-- CoreUI and necessary plugins-->
<script src="{{ asset('template/vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendors/simplebar/js/simplebar.min.js') }}"></script>

<script type="module">
    const header = document.querySelector('header.header');

    document.addEventListener('scroll', () => {
        if (header) {
            header.classList.toggle(
                'shadow-sm',
                document.documentElement.scrollTop > 0
            );
        }
    });
</script>
<!-- Plugins and scripts required by this view-->
<script src="{{ asset('template/vendors/chart.js/js/chart.umd.js') }}"></script>
<script src="{{ asset('template/vendors/@coreui/chartjs/js/coreui-chartjs.js') }}"></script>
<script src="{{ asset('template/vendors/@coreui/utils/js/index.js') }}"></script>
<script src="{{ asset('template/js/main.js') }}"></script>
@stack('js')

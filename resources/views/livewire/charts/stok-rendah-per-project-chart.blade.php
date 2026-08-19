<div>
    @if ($selectedProject)
        <div wire:ignore>
            <div id="stok-rendah-kategori-per-project"></div>
        </div>
    @endif
</div>
<script type="module">
    let categoryChartPerProject = null;

    document.addEventListener('livewire:init', () => {
        Livewire.on('projectUpdatedStokRendahPerProjectChart', (event) => {
            initStokRendahPerProjectChart(event.data);
        });
    });

    function initStokRendahPerProjectChart(data) {
        let el = document.querySelector("#stok-rendah-kategori-per-project");
        if (!el) return;

        if (categoryChartPerProject) {
            categoryChartPerProject.destroy();
        }
        categoryChartPerProject = new ApexCharts(el, {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
            },

            title: {
                text: 'Stok Rendah Per Project',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    color: '#263238'
                }
            },
            colors: chartColors,
            plotOptions: {
                bar: {
                    horizontal: false,
                    barHeight: '55%',
                    distributed: true
                }
            },
            dataLabels: {
                enabled: true
            },
            grid: {
                strokeDashArray: 4
            },
            series: [{
                name: 'Total',
                data: [
                    data.tool ?? 0,
                    data.consumable ?? 0,
                    data.apd ?? 0
                ]
            }],
            xaxis: {
                categories: ['Tool', 'Consumable', 'APD']
            }
        });

        categoryChartPerProject.render();
    }
</script>

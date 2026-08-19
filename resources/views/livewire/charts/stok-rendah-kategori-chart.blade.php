<div>
    @if (!$selectedProject)
        <div wire:ignore>
            <div id="stok-rendah-kategori"></div>
        </div>
    @endif
</div>
<script type="module">
    let categoryChart = null;

    document.addEventListener('livewire:init', () => {
        Livewire.on('projectUpdatedStokRendahChart', (event) => {
            initStokRendahChart(event.data);
        });
    });

    function initStokRendahChart(data) {

        let el = document.querySelector("#stok-rendah-kategori");
        if (!el) return;

        if (categoryChart) {
            categoryChart.destroy();
        }

        categoryChart = new ApexCharts(el, {
            chart: {
                type: 'bar',
                stacked: true,
                height: 300,
                toolbar: {
                    show: false
                }
            },

            title: {
                text: 'Stok Rendah per Kategori',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    color: '#263238'
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    distributed: false
                }
            },
            dataLabels: {
                enabled: true
            },
            grid: {
                strokeDashArray: 4
            },
            series: data.map(item => ({
                name: item.nama_project,
                data: [
                    item.tool ?? 0,
                    item.consumable ?? 0,
                    item.apd ?? 0
                ]
            })),
            xaxis: {
                categories: ['Tool', 'Consumable', 'APD']
            }
        });
        categoryChart.render();
    }
</script>

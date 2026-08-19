<div>
    @if ($selectedProject)
        <div wire:ignore>
            <div id="top-material-distribusi"></div>
        </div>
    @endif
</div>
<script type="module">
    let TopMaterialDistribusiChart = null;

    document.addEventListener('livewire:init', () => {
        Livewire.on('projectUpdatedTopMaterialDistribusiChart', (event) => {
            initTopMaterialDistribusiChart(event.data);
        });
    });

    function initTopMaterialDistribusiChart(data) {
        let el = document.querySelector("#top-material-distribusi");
        if (!el) return;
        if (TopMaterialDistribusiChart) {
            TopMaterialDistribusiChart.destroy();
        }
        TopMaterialDistribusiChart = new ApexCharts(el, {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
            },

            title: {
                text: 'Top 5 Material Distribusi',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    color: '#263238'
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
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
                data: data.total_distribusi || [0, 0, 0]
            }],
            xaxis: {
                categories: data.kode_material || []
            }
        });

        TopMaterialDistribusiChart.render();
    }
</script>

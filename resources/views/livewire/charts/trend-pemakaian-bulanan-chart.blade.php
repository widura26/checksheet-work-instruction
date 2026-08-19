<div>
    @if ($selectedProject)
        <div wire:ignore>
            <div id="trend-pemakaian-bulanan"></div>
        </div>
    @endif
</div>
<script type="module">
    let trendChart = null;

    document.addEventListener('livewire:init', () => {
        Livewire.on('projectUpdatedTrendChart', (event) => {
            initTrendChart(event.data);
        });
    });

    function initTrendChart(data) {
        let el = document.querySelector("#trend-pemakaian-bulanan");
        if (!el) return;
        if (trendChart) {
            trendChart.destroy();
        }
        const chartColors = ['#66DA26', '#2E93fA', '#ffc107'];
        trendChart = new ApexCharts(el, {
            chart: {
                type: 'line',
                height: 300,
                toolbar: {
                    show: false
                },
            },
            stroke: {
                width: 2,
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 0,
                    blur: 4,
                    opacity: 0.2
                }
            },
            title: {
                text: 'Trend Pemakaian Bulanan',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    color: '#263238'
                },
            },
            colors: chartColors,
            markers: {
                size: 5,
                strokeWidth: 3,
                strokeColors: chartColors,
                colors: ['#ffffff'],
                hover: {
                    size: 10
                }
            },
            series: [{
                    name: 'Consumable',
                    data: data.series.consumable || []
                },
                {
                    name: 'Tool',
                    data: data.series.tool || []
                },
                {
                    name: 'APD',
                    data: data.series.apd || []
                }
            ],
            xaxis: {
                categories: data.labels || []
            }
        });

        trendChart.render();
    }
</script>

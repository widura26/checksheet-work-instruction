<div>
    @if (!$selectedProject)
        <div class="row">
            <div class="col-md-6">
                <div wire:ignore>
                    <div id="distribusi-per-project-chart"></div>
                </div>
            </div>

            <div class="col-md-6 mt-5">
                <div id="custom-legend"></div>
            </div>
        </div>
    @endif
</div>
<script type="module">
    let distribusiPerProjectChart = null;

    document.addEventListener('livewire:init', () => {
        Livewire.on('projectUpdatedDistribusiChart', (event) => {
            initDistribusiPerProjectChart(event.data);
        });
    });

    function initDistribusiPerProjectChart(data) {
        let el = document.querySelector("#distribusi-per-project-chart");
        if (!el) return;
        if (distribusiPerProjectChart) {
            distribusiPerProjectChart.destroy();
        }
        distribusiPerProjectChart = new ApexCharts(el, {
            chart: {
                type: 'donut',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                }
            },
            title: {
                text: 'Total Distribusi Project',
                style: {
                    fontSize: '14px',
                    fontWeight: 'bold',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    color: '#263238'
                }
            },
            dataLabels: {
                enabled: true
            },
            series: data.total_distribusi || [],
            labels: data.project_id || [],
            legend: {
                position: 'bottom'
            }
        });
        distribusiPerProjectChart.render();
        const colorsChart = distribusiPerProjectChart.w.globals.colors;
        renderCustomLegend(data, "custom-legend", colorsChart);
    }
</script>

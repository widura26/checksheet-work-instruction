<div>
    @if ($selectedProject)
        <h1>
        </h1>
        <div class="row">
            <div class="col-md-6">
                <div wire:ignore>
                    <div id="persentase-distribusi-chart"></div>
                </div>
            </div>

            <div class="col-md-6 mt-5">
                <div id="custom-legend-distribusi"></div>
            </div>
        </div>
    @endif
    <script type="module">
        let persentaseDistribusiChart = null;

        document.addEventListener('livewire:init', () => {
            Livewire.on('projectUpdatedPresentasiDistribusiChart', (event) => {
                initPersentaseDistribusiChart(event.data);
            });
        });

        function initPersentaseDistribusiChart(data) {
            let el = document.querySelector("#persentase-distribusi-chart");
            if (!el) return;
            if (persentaseDistribusiChart) {
                persentaseDistribusiChart.destroy();
            }
            persentaseDistribusiChart = new ApexCharts(el, {
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
                colors: chartColors,
                series: data.total_distribusi || [],
                labels: data.jenis_material || [],
                legend: {
                    position: 'bottom'
                }
            });
            persentaseDistribusiChart.render();
            renderCustomLegendPersentase(data);
        }
    </script>
</div>

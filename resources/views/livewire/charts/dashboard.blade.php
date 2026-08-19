<div>
    <div class="row mb-3 d-flex justify-content-end">
        <div class="col-lg-2 col-sm-12">
            <select class="form-select" wire:model.live="selectedYear">
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-12">
            <select class="form-control" wire:model.live="selectedProject" id="projectSelect">
                <option value="">--- Semua Proyek ---</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->project_id }}">{{ $project->nama_project }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <livewire:charts.kpi-chart :selectedProject="$selectedProject" />
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 col-sm-12">
                    <livewire:charts.distribusi-per-project-chart :selectedProject="$selectedProject" :selectedYear="$selectedYear" />
                </div>
                <div class="col-lg-6 col-sm-12">
                    <livewire:charts.stok-rendah-kategori-chart :selectedProject="$selectedProject" :key="'stok-' . $selectedProject" />
                </div>
                <div class="col-lg-6 col-sm-12">
                    <livewire:charts.persentase-distribusi-chart :selectedYear="$selectedYear" :selectedProject="$selectedProject"
                        :key="'persen-' . $selectedProject" />
                </div>
                <div class="col-lg-6 col-sm-12">
                    <livewire:charts.top-material-distribusi-chart :selectedProject="$selectedProject" />
                </div>
                <div class="col-lg-12 col-sm-12">
                    <livewire:charts.trend-pemakaian-bulanan-chart :selectedYear="$selectedYear" :selectedProject="$selectedProject"
                        :key="'trend-' . $selectedProject" />
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function renderCustomLegend(data, containerId, colors) {
        const container = document.getElementById(containerId);
        container.innerHTML = "";

        const total = data.total_distribusi.reduce((a, b) => a + b, 0);

        data.project_id.forEach((project, index) => {
            const value = data.total_distribusi[index];
            const percent = total === 0 ?
                0 :
                ((value / total) * 100).toFixed(1);

            const color = colors[index % colors.length];

            container.innerHTML += `
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="me-2"
                              style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${color}">
                        </span>
                        ${project}
                    </div>
                    <div>
                        <strong>${percent}%</strong>
                    </div>
                </div>

                <div class="small text-muted">${value} Distribusi</div>

                <div class="progress mt-1" style="height:6px;">
                    <div class="progress-bar"
                         style="width:${percent}%; background:${color}">
                    </div>
                </div>
            </div>
        `;
        });

        document.getElementById("total-project").innerText =
            data.project_id.length;
    }

    function renderCustomLegendPersentase(data) {
        let legendEl = document.getElementById('custom-legend-distribusi');
        if (!legendEl) return;

        legendEl.innerHTML = '';

        let series = data.total_distribusi || [];
        let labels = data.jenis_material || [];

        let total = series.reduce((a, b) => a + b, 0);

        series.forEach((value, index) => {
            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;

            let item = document.createElement('div');
            item.classList.add('mb-4');

            item.innerHTML = `
            <div class="d-flex justify-content-between">
                <strong>${labels[index]}</strong>
                <strong>${percentage}%</strong>
            </div>
            <div class="text-muted mb-1">${value} Distribusi</div>
            <div style="background:#e9ecef;height:6px;border-radius:4px;">
                <div style="
                    width:${percentage}%;
                    height:6px;
                    background:${chartColors[index]};
                    border-radius:4px;
                "></div>
            </div>
        `;
            legendEl.appendChild(item);
        });
    }
    const chartColors = ['#2E93fA', '#66DA26', '#ffc107'];
</script>

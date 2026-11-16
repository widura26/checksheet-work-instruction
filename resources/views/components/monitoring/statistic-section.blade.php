<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="st-metric-card">
            <div class="st-metric-label">Total Item</div>
            <div class="st-metric-value" id="kpi-total-item"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="st-metric-card">
            <div class="st-metric-label">Jumlah Material</div>
            <div class="st-metric-value" id="kpi-jumlah-material"></div>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="st-card h-100">
            <h5 class="st-card-title">Group</h5>
            <div style="position: relative; height: 320px;">
                <canvas id="chart-group"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="st-card h-100">
            <h5 class="st-card-title">Status PR</h5>
            <div style="position: relative; height: 320px;">
                <canvas id="chart-status-pr"></canvas>
            </div>
        </div>
    </div>
</div>
<div class="st-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="st-card-title mb-0">Jumlah per Status PR</h5>
        <div class="text-muted small">
            <span class="badge bg-light text-secondary border">Rekap Matriks</span>
        </div>
    </div>
    <div class="table-responsive st-matrix-scroll">
        <table class="table table-bordered align-middle st-table mb-0" id="table-status-pr">
            <thead id="table-status-pr-head"></thead>
            <tbody id="table-status-pr-body"></tbody>
        </table>
    </div>
</div>

<style>
    .st-matrix-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #table-status-pr th:first-child,
    #table-status-pr td:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
        min-width: 220px;
        background-color: #ffffff;
        box-shadow: 2px 0 0 rgba(49, 51, 63, 0.08);
    }

    #table-status-pr thead th:first-child {
        z-index: 3;
        background-color: #f8f9fb;
    }

    #table-status-pr tbody tr:hover td:first-child {
        background-color: #f8fafc;
    }
</style>

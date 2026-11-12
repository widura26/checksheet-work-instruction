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
    <div class="table-responsive">
        <table class="table table-bordered align-middle st-table mb-0" id="table-status-pr">
            <thead>
                <tr>
                    <th style="width: 50%;">Status PR</th>
                    <th class="text-end" style="width: 25%;">KOMPONEN</th>
                    <th class="text-end" style="width: 25%;">PIECE PART</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-semibold">BELUM PR</td>
                    <td class="text-end" id="sum-belum-komp">0</td>
                    <td class="text-end" id="sum-belum-piece">3</td>
                </tr>
                <tr>
                    <td class="fw-semibold">PR PARSIAL</td>
                    <td class="text-end" id="sum-parsial-komp">0</td>
                    <td class="text-end" id="sum-parsial-piece">0</td>
                </tr>
                <tr>
                    <td class="fw-semibold">SUDAH PR</td>
                    <td class="text-end" id="sum-sudah-komp">1</td>
                    <td class="text-end" id="sum-sudah-piece">209</td>
                </tr>
                <tr>
                    <td class="fw-semibold">STOK DAN PR</td>
                    <td class="text-end" id="sum-stokpr-komp">0</td>
                    <td class="text-end" id="sum-stokpr-piece">0</td>
                </tr>
                <tr>
                    <td class="fw-semibold">STOK</td>
                    <td class="text-end" id="sum-stok-komp">0</td>
                    <td class="text-end" id="sum-stok-piece">1</td>
                </tr>
                <tr>
                    <td class="fw-semibold">STATUS LAIN / TIDAK DIKENAL</td>
                    <td class="text-end" id="sum-lain-komp">0</td>
                    <td class="text-end" id="sum-lain-piece">1</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

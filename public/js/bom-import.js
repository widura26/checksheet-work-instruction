document.addEventListener('DOMContentLoaded', function () {
    const POLL_INTERVAL_MS = 1500;
    const MAX_POLL_ATTEMPTS = 80;

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function showResult(type, message) {
        const resultDiv = document.getElementById('bomImportResult');
        const alert = document.createElement('div');
        alert.className = 'alert alert-' + type;
        alert.textContent = message;
        resultDiv.innerHTML = '';
        resultDiv.appendChild(alert);
        resultDiv.classList.remove('d-none');
    }

    function setBusy(busy) {
        const btn = document.getElementById('btnBomImport');
        const progress = document.getElementById('bomImportProgress');
        btn.disabled = busy;
        btn.textContent = busy ? 'Mengimpor...' : 'Import';
        progress.classList.toggle('d-none', !busy);
    }

    function pollStatus(importId, attempt) {
        fetch('/bom/status/' + encodeURIComponent(importId))
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                if (data.status === 'completed') {
                    setBusy(false);
                    showResult('success', 'Import selesai — ' + data.imported + ' baris.');
                } else if (data.status === 'failed') {
                    setBusy(false);
                    showResult('danger', 'Import gagal. Cek log server (import_id: ' + importId + ').');
                } else if (attempt + 1 >= MAX_POLL_ATTEMPTS) {
                    setBusy(false);
                    showResult('warning', 'Masih diproses — coba lagi nanti.');
                } else {
                    setTimeout(function () {
                        pollStatus(importId, attempt + 1);
                    }, POLL_INTERVAL_MS);
                }
            })
            .catch(function (error) {
                setBusy(false);
                showResult('danger', 'Gagal cek status import: ' + error.message);
            });
    }

    const btnBomImport = document.getElementById('btnBomImport');
    if (btnBomImport) {
        btnBomImport.addEventListener('click', function () {
            const fileInput = document.getElementById('bomFile');
            const resultDiv = document.getElementById('bomImportResult');

            resultDiv.innerHTML = '';
            resultDiv.classList.add('d-none');

            if (!fileInput.files.length) {
                showResult('warning', 'Pilih file terlebih dahulu.');
                return;
            }

            setBusy(true);

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('_token', getCsrfToken());

            fetch('/bom/import', { method: 'POST', body: formData })
                .then(function (response) {
                    return response.json().then(function (json) {
                        if (!response.ok) {
                            const message = json.message
                                || Object.values(json).flat().join(' ')
                                || 'Server error: ' + response.status;
                            throw new Error(message);
                        }
                        return json;
                    });
                })
                .then(function (data) {
                    if (data.success) {
                        pollStatus(data.import_id, 0);
                    } else {
                        setBusy(false);
                        showResult('danger', data.message || 'Terjadi kesalahan saat import.');
                    }
                })
                .catch(function (error) {
                    setBusy(false);
                    showResult('danger', error.message || 'Terjadi kesalahan saat import.');
                });
        });
    }
});

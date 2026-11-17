document.addEventListener('DOMContentLoaded', function () {
    // Backoff: maksimal 8 request per import (dulu 80 request tiap 1,5 detik).
    const POLL_DELAYS_MS = [0, 1000, 2000, 4000, 7000, 10000, 10000, 10000];
    let activePollController = null;

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

    function finish() {
        activePollController = null;
    }

    function pollStatus(importId, attempt) {
        if (activePollController) activePollController.abort();

        const controller = new AbortController();
        activePollController = controller;

        function step(currentAttempt) {
            if (controller.signal.aborted) return;

            if (document.hidden) {
                setTimeout(function () { step(currentAttempt); }, 3000);
                return;
            }

            fetch('/bom/status/' + encodeURIComponent(importId), { signal: controller.signal })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Server error: ' + response.status);
                    }
                    return response.json();
                })
                .then(function (data) {
                    if (controller.signal.aborted) return;

                    if (data.status === 'completed') {
                        finish();
                        setBusy(false);
                        showResult('success', 'Import selesai — ' + data.imported + ' baris.');
                    } else if (data.status === 'failed') {
                        finish();
                        setBusy(false);
                        showResult('danger', 'Import gagal. Cek log server (import_id: ' + importId + ').');
                    } else if (currentAttempt + 1 >= POLL_DELAYS_MS.length) {
                        finish();
                        setBusy(false);
                        showResult('warning', 'Masih diproses — coba lagi nanti.');
                    } else {
                        setTimeout(function () {
                            step(currentAttempt + 1);
                        }, POLL_DELAYS_MS[currentAttempt + 1]);
                    }
                })
                .catch(function (error) {
                    if (error.name === 'AbortError' || controller.signal.aborted) return;
                    finish();
                    setBusy(false);
                    showResult('danger', 'Gagal cek status import: ' + error.message);
                });
        }

        step(0);
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
                        pollStatus(data.import_id);
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

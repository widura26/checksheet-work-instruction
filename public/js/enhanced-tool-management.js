/**
 * Enhanced Tool Management JavaScript Module
 * Handles dynamic form behavior with AJAX integration for intelligent tool creation/update
 * ini buatan AI wkwkwk
 */

class EnhancedToolManagement {
    constructor(options = {}) {
        this.options = {
            materialCodeSelector: "#kode_material",
            posisiCtSelector: "#posisi_ct_id",
            formSelector: "#myForm",
            loadingClass: "loading",
            errorClass: "is-invalid",
            successClass: "is-valid",
            readonlyClass: "readonly-field",
            ajaxTimeout: 10000,
            ...options,
        };

        this.currentMode = null; // 'create' or 'update'
        this.isLoading = false;
        this.xhr = null; // To hold the current AJAX request

        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        const materialCodeInput = $(this.options.materialCodeSelector);
        const posisiCtSelect = $(this.options.posisiCtSelector);
        const projectSelect = $("#project_id");

        // Use 'input' for instant feedback on typing/pasting, and 'change' for select dropdown
        materialCodeInput.on(
            "input",
            this.debounce(() => this.handleInputChange(), 500),
        );
        posisiCtSelect.on("change", () => this.handleInputChange());

        // Add event listener for project select
        projectSelect.on("change", () => this.handleInputChange());
    }

    debounce(func, delay) {
        let timeout;
        return function (...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    handleInputChange() {
        const kodeMaterial = $(this.options.materialCodeSelector).val().trim();
        const posisiCtId = $(this.options.posisiCtSelector).val();
        const selectedProject = $("#project_id").val();

        // Only proceed if all required fields have values
        if (!kodeMaterial || !posisiCtId || !selectedProject) {
            this.resetForm();
            return;
        }

        if (this.isLoading) {
            if (this.xhr) {
                this.xhr.abort(); // Abort previous request if a new one is made
            }
        }

        this.validateCombination(kodeMaterial, posisiCtId, selectedProject);
    }

    async validateCombination(kodeMaterial, posisiCtId, selectedProject) {
        this.setLoadingState(true);

        // Construct URL with query parameters
        const url = `/tool/api/tools/find?kode_material=${encodeURIComponent(
            kodeMaterial,
        )}&posisi_ct_id=${encodeURIComponent(
            posisiCtId,
        )}&project_id=${encodeURIComponent(selectedProject)}`;

        this.xhr = $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            timeout: this.options.ajaxTimeout,
        })
            .done((response) => {
                if (response.success) {
                    this.switchFormMode(response.mode, response.data);
                    this.populateFields(response.data);
                    const message =
                        response.mode === "update"
                            ? "Tool ditemukan dengan kombinasi posisi dan project yang sama - mode update"
                            : "Kode material valid - mode buat baru";
                    this.showSuccessFeedback(
                        $(this.options.materialCodeSelector),
                        message,
                    );
                }
            })
            .fail((xhr, status, error) => {
                this.showErrorFeedback(
                    $(this.options.materialCodeSelector),
                    "Kode Material Tidak Ditemukan.",
                );
                Swal.fire({
                    title: "Error",
                    text: "Kode Material Tidak Ditemukan Di Database",
                    icon: "error",
                });
                this.resetForm();
            })
            .always(() => {
                this.setLoadingState(false);
                this.xhr = null;
            });
    }

    switchFormMode(mode, data) {
        this.currentMode = mode;
        this.lastValidationData = data; // Store data for project selection changes
        $(document).trigger("toolManagement:modeChanged", { mode, data });
    }

    populateFields(data) {
        // Populate fields that are common or from master data
        $("#deskripsi")
            .val(data.deskripsi || "")
            .prop("readonly", true)
            .addClass(this.options.readonlyClass);
        $("#spesifikasi")
            .val(data.spesifikasi || "")
            .prop("readonly", true)
            .addClass(this.options.readonlyClass);
        $("#satuan")
            .val(data.satuan || "")
            .prop("readonly", true)
            .addClass(this.options.readonlyClass);

        if (this.currentMode === "update") {
            console.log(data);

            // Display current stock for the selected project
            this.displayCurrentStock(data.stock_project[0].pivot.qty || 0);
            $("#qty_ditambah").val(""); // Clear previous entry
        } else {
            // 'create' mode
            $("#qty").val(0); // Default to 0
        }
    }

    displayCurrentStock(currentStock) {
        const stockDisplay = $("#current_stock_display");

        if (!currentStock || currentStock === 0) {
            stockDisplay.html(
                '<p class="text-muted mb-0"><i class="fas fa-info-circle"></i> Tidak ada stok untuk kombinasi posisi dan project ini</p>',
            );
            return;
        }

        const selectedProject = $("#project_id option:selected").text();
        const selectedPosition = $("#posisi_ct_id option:selected").text();

        let stockHtml = `
            <div class="d-flex justify-content-between align-items-center py-2 px-3 bg-light rounded">
                <div>
                    <div class="fw-bold text-dark">${selectedProject}</div>
                    <div class="small text-muted">Posisi: ${selectedPosition}</div>
                    <div class="small text-muted">Stok saat ini</div>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary fs-5">${currentStock} pcs</span>
                </div>
            </div>
        `;

        stockDisplay.html(stockHtml);
    }

    resetForm() {
        $("#deskripsi, #spesifikasi, #satuan", "#posisi_ct_id", "#project_id")
            .val("")
            .prop("readonly", false)
            .removeClass(this.options.readonlyClass);

        // Clear quantity inputs
        $("#qty, #qty_ditambah").val("");

        // Clear stock display
        $("#current_stock_display").html("");

        // Reset project select
        $("#project_id").val("");

        // Reset form mode and trigger event
        this.currentMode = null;
        this.lastValidationData = null;
        $(document).trigger("toolManagement:formReset");

        // Clear feedback
        // this.clearFeedback($(this.options.materialCodeSelector));
        // this.clearFeedback($(this.options.posisiCtSelector));
    }

    setLoadingState(isLoading) {
        this.isLoading = isLoading;
        const loadingIndicator = $(".loading-indicator");
        if (isLoading) {
            loadingIndicator.show();
        } else {
            loadingIndicator.hide();
        }
    }

    showSuccessFeedback(input, message) {
        this.clearFeedback(input);
        input.addClass(this.options.successClass);
        const feedback = input.closest(".mb-3").find(".valid-feedback");
        if (feedback.length) {
            feedback.text(message).show();
        } else {
            input.after(`<div class="valid-feedback">${message}</div>`);
        }
    }

    showErrorFeedback(input, message) {
        this.clearFeedback(input);
        input.addClass(this.options.errorClass);

        let feedback = input.siblings(".invalid-feedback");

        if (!feedback.length) {
            feedback = $('<div class="invalid-feedback d-block"></div>');
            input.after(feedback);
        }

        feedback.text(message).addClass("d-block").show();
    }

    clearFeedback(input) {
        input.removeClass(
            `${this.options.successClass} ${this.options.errorClass}`,
        );

        input
            .siblings(".valid-feedback, .invalid-feedback")
            .removeClass("d-block")
            .hide()
            .text("");
    }
}

// Auto-initialize when DOM is ready
$(document).ready(function () {
    if ($(".enhanced-tool-form").length) {
        new EnhancedToolManagement();
    }
});

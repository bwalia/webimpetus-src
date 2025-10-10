<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    .cash-flow-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .section-header {
        font-size: 1.25rem;
        font-weight: 700;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .operating-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .investing-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .financing-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .summary-header {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .cash-flow-table {
        width: 100%;
        margin-top: 10px;
    }

    .cash-flow-table tr {
        border-bottom: 1px solid #f0f0f0;
    }

    .cash-flow-table td {
        padding: 10px 15px;
    }

    .cash-flow-table td:first-child {
        font-weight: 500;
        color: #374151;
    }

    .cash-flow-table td:last-child {
        text-align: right;
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

    .positive-amount {
        color: #059669;
    }

    .negative-amount {
        color: #dc2626;
    }

    .subtotal-row {
        background: #f9fafb;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .total-row {
        background: #e0e7ff;
        font-weight: 700;
        font-size: 1.1rem;
        border-top: 2px solid #667eea;
    }

    .filter-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .balance-check {
        padding: 15px;
        border-radius: 6px;
        margin-top: 20px;
        font-weight: 600;
    }

    .balance-check.balanced {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }

    .balance-check.unbalanced {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }

    .indent-1 {
        padding-left: 30px !important;
        font-size: 0.95rem;
        color: #6b7280;
    }

    .indent-2 {
        padding-left: 50px !important;
        font-size: 0.9rem;
        color: #9ca3af;
        font-style: italic;
    }
</style>

<!-- Filter Section -->
<div class="white_card_body">
    <div class="filter-section">
        <h5 style="margin-bottom: 20px;">
            <i class="fa fa-filter"></i> Report Period
        </h5>
        <form id="cashFlowFilterForm" class="form-inline">
            <div class="form-row align-items-end" style="width: 100%;">
                <div class="form-group col-md-3">
                    <label for="period_select">Accounting Period</label>
                    <select id="period_select" class="form-control" style="width: 100%;">
                        <option value="custom">Custom Date Range</option>
                        <?php foreach ($periods as $period): ?>
                            <option value="<?= $period['uuid'] ?>"
                                    data-start="<?= $period['start_date'] ?>"
                                    data-end="<?= $period['end_date'] ?>"
                                    <?= $period['is_current'] ? 'selected' : '' ?>>
                                <?= $period['period_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control"
                           value="<?= $start_date ?>" required style="width: 100%;">
                </div>

                <div class="form-group col-md-3">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control"
                           value="<?= $end_date ?>" required style="width: 100%;">
                </div>

                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fa fa-chart-line"></i> Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cash Flow Statement -->
<div class="white_card_body">
    <div id="cashFlowReport" style="display: none;">

        <!-- Beginning Cash Balance -->
        <div class="cash-flow-section">
            <div class="section-header summary-header">
                <i class="fa fa-money-bill-wave"></i>
                <span>Beginning Cash Balance</span>
            </div>
            <table class="cash-flow-table">
                <tr>
                    <td>Cash and Cash Equivalents at <span id="report_start_date"></span></td>
                    <td id="beginning_cash" class="positive-amount">$0.00</td>
                </tr>
            </table>
        </div>

        <!-- Operating Activities -->
        <div class="cash-flow-section">
            <div class="section-header operating-header">
                <i class="fa fa-cogs"></i>
                <span>Cash Flows from Operating Activities</span>
            </div>
            <table class="cash-flow-table">
                <tbody id="operating_activities_table">
                    <!-- Will be populated dynamically -->
                </tbody>
                <tr class="subtotal-row">
                    <td>Net Cash from Operating Activities</td>
                    <td id="net_operating">$0.00</td>
                </tr>
            </table>
        </div>

        <!-- Investing Activities -->
        <div class="cash-flow-section">
            <div class="section-header investing-header">
                <i class="fa fa-chart-pie"></i>
                <span>Cash Flows from Investing Activities</span>
            </div>
            <table class="cash-flow-table">
                <tbody id="investing_activities_table">
                    <!-- Will be populated dynamically -->
                </tbody>
                <tr class="subtotal-row">
                    <td>Net Cash from Investing Activities</td>
                    <td id="net_investing">$0.00</td>
                </tr>
            </table>
        </div>

        <!-- Financing Activities -->
        <div class="cash-flow-section">
            <div class="section-header financing-header">
                <i class="fa fa-university"></i>
                <span>Cash Flows from Financing Activities</span>
            </div>
            <table class="cash-flow-table">
                <tbody id="financing_activities_table">
                    <!-- Will be populated dynamically -->
                </tbody>
                <tr class="subtotal-row">
                    <td>Net Cash from Financing Activities</td>
                    <td id="net_financing">$0.00</td>
                </tr>
            </table>
        </div>

        <!-- Summary -->
        <div class="cash-flow-section">
            <div class="section-header summary-header">
                <i class="fa fa-calculator"></i>
                <span>Net Change in Cash</span>
            </div>
            <table class="cash-flow-table">
                <tr>
                    <td>Net Increase (Decrease) in Cash</td>
                    <td id="net_cash_change">$0.00</td>
                </tr>
                <tr>
                    <td>Cash at Beginning of Period</td>
                    <td id="beginning_cash_summary">$0.00</td>
                </tr>
                <tr class="total-row">
                    <td>Cash at End of Period</td>
                    <td id="ending_cash">$0.00</td>
                </tr>
            </table>

            <div id="balance_check" class="balance-check" style="display: none;">
                <!-- Will show balance status -->
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="text-right mt-3">
            <button type="button" class="btn btn-danger" onclick="exportCashFlowPDF()">
                <i class="fa fa-file-pdf"></i> Export PDF
            </button>
            <button type="button" class="btn btn-success" onclick="exportCashFlowExcel()">
                <i class="fa fa-file-excel"></i> Export Excel
            </button>
            <button type="button" class="btn btn-info" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="text-center" style="padding: 60px;">
        <i class="fa fa-spinner fa-spin fa-3x" style="color: #667eea;"></i>
        <p style="margin-top: 20px; color: #6b7280;">Select a period and click "Generate Report" to view Cash Flow Statement</p>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center" style="padding: 60px; display: none;">
        <i class="fa fa-exclamation-circle fa-3x" style="color: #f59e0b;"></i>
        <p style="margin-top: 20px; color: #6b7280;">No data available for the selected period</p>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    function formatCurrency(value) {
        const formatted = parseFloat(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return '$' + formatted;
    }

    function formatAmount(value) {
        const num = parseFloat(value);
        if (num >= 0) {
            return '<span class="positive-amount">' + formatCurrency(num) + '</span>';
        } else {
            return '<span class="negative-amount">(' + formatCurrency(Math.abs(num)) + ')</span>';
        }
    }

    // Period selector change handler
    $('#period_select').on('change', function() {
        const selected = $(this).find('option:selected');
        if (selected.val() !== 'custom') {
            $('#start_date').val(selected.data('start'));
            $('#end_date').val(selected.data('end'));
        }
    });

    // Form submission
    $('#cashFlowFilterForm').on('submit', function(e) {
        e.preventDefault();

        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Dates',
                text: 'Please select both start and end dates',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        if (startDate > endDate) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date Range',
                text: 'Start date must be before end date',
                confirmButtonColor: '#dc2626'
            });
            return;
        }

        generateCashFlow(startDate, endDate);
    });

    function generateCashFlow(startDate, endDate) {
        $('#loadingState').html('<i class="fa fa-spinner fa-spin fa-3x" style="color: #667eea;"></i><p style="margin-top: 20px;">Generating Cash Flow Statement...</p>').show();
        $('#cashFlowReport').hide();
        $('#emptyState').hide();

        $.ajax({
            url: '/cash-flow/generate',
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.status && response.data) {
                    displayCashFlow(response.data, startDate, endDate);
                } else {
                    $('#loadingState').hide();
                    $('#emptyState').show();
                }
            },
            error: function(xhr) {
                console.error('Error generating cash flow:', xhr);
                $('#loadingState').hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to generate cash flow statement',
                    confirmButtonColor: '#dc2626'
                });
            }
        });
    }

    function displayCashFlow(data, startDate, endDate) {
        // Update dates
        $('#report_start_date').text(new Date(startDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }));

        // Beginning cash
        $('#beginning_cash').html(formatAmount(data.beginning_cash));
        $('#beginning_cash_summary').html(formatAmount(data.beginning_cash));

        // Operating Activities
        let operatingHtml = '';
        data.operating_activities.forEach(activity => {
            const cssClass = activity.type === 'income' ? '' : 'indent-1';
            operatingHtml += `
                <tr>
                    <td class="${cssClass}">${activity.description}</td>
                    <td>${formatAmount(activity.amount)}</td>
                </tr>
            `;
        });
        $('#operating_activities_table').html(operatingHtml || '<tr><td colspan="2" class="text-center text-muted">No operating activities</td></tr>');
        $('#net_operating').html(formatAmount(data.net_operating));

        // Investing Activities
        let investingHtml = '';
        data.investing_activities.forEach(activity => {
            investingHtml += `
                <tr>
                    <td class="indent-1">${activity.description}</td>
                    <td>${formatAmount(activity.amount)}</td>
                </tr>
            `;
        });
        $('#investing_activities_table').html(investingHtml || '<tr><td colspan="2" class="text-center text-muted">No investing activities</td></tr>');
        $('#net_investing').html(formatAmount(data.net_investing));

        // Financing Activities
        let financingHtml = '';
        data.financing_activities.forEach(activity => {
            financingHtml += `
                <tr>
                    <td class="indent-1">${activity.description}</td>
                    <td>${formatAmount(activity.amount)}</td>
                </tr>
            `;
        });
        $('#financing_activities_table').html(financingHtml || '<tr><td colspan="2" class="text-center text-muted">No financing activities</td></tr>');
        $('#net_financing').html(formatAmount(data.net_financing));

        // Net change and ending cash
        $('#net_cash_change').html(formatAmount(data.net_cash_change));
        $('#ending_cash').html(formatAmount(data.ending_cash));

        // Balance check
        if (data.is_balanced) {
            $('#balance_check').html('<i class="fa fa-check-circle"></i> Cash flow statement is balanced')
                .removeClass('unbalanced').addClass('balanced').show();
        } else {
            $('#balance_check').html('<i class="fa fa-exclamation-triangle"></i> Warning: Calculated ending cash (' +
                formatCurrency(data.calculated_ending_cash) + ') does not match actual ending cash (' +
                formatCurrency(data.ending_cash) + ')')
                .removeClass('balanced').addClass('unbalanced').show();
        }

        // Show report
        $('#loadingState').hide();
        $('#cashFlowReport').show();
    }

    function exportCashFlowPDF() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'No Report Generated',
                text: 'Please generate a report first',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        window.open('/cash-flow/exportPDF?start_date=' + startDate + '&end_date=' + endDate, '_blank');
    }

    function exportCashFlowExcel() {
        Swal.fire({
            icon: 'info',
            title: 'Excel Export',
            text: 'Excel export feature will be implemented soon',
            confirmButtonColor: '#667eea'
        });
    }

    // Initialize - don't auto-generate, wait for user to click button
    $(document).ready(function() {
        // Could auto-generate if there's a current period
        <?php if (!empty($periods)): ?>
        // Auto-generate for current period on load
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        if (startDate && endDate) {
            generateCashFlow(startDate, endDate);
        }
        <?php endif; ?>
    });
</script>

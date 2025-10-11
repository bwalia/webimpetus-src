<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="journalEntryForm" method="post" action="/journal-entries/update">
            <input type="hidden" name="uuid" value="<?= @$entry->uuid ?>" />
            <input type="hidden" name="id" value="<?= @$entry->id ?>" />
            <input type="hidden" name="lines_data" id="lines_data" />

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="entry_number">Entry Number</label>
                        <input type="text" class="form-control" id="entry_number" name="entry_number"
                               value="<?= @$entry->entry_number ?>" readonly style="background: #f3f4f6; font-family: monospace; font-weight: 600;">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="entry_date">Entry Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="entry_date" name="entry_date"
                               value="<?= @$entry->entry_date ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="entry_type">Entry Type</label>
                        <select name="entry_type" id="entry_type" class="form-control">
                            <option value="Manual" <?= (@$entry->entry_type == 'Manual') ? 'selected' : '' ?>>Manual</option>
                            <option value="Sales Invoice" <?= (@$entry->entry_type == 'Sales Invoice') ? 'selected' : '' ?>>Sales Invoice</option>
                            <option value="Purchase Invoice" <?= (@$entry->entry_type == 'Purchase Invoice') ? 'selected' : '' ?>>Purchase Invoice</option>
                            <option value="Payment" <?= (@$entry->entry_type == 'Payment') ? 'selected' : '' ?>>Payment</option>
                            <option value="Receipt" <?= (@$entry->entry_type == 'Receipt') ? 'selected' : '' ?>>Receipt</option>
                            <option value="Adjustment" <?= (@$entry->entry_type == 'Adjustment') ? 'selected' : '' ?>>Adjustment</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <div class="mt-2">
                            <?php if (@$entry->is_posted): ?>
                                <span class="badge badge-success" style="padding: 8px 16px; font-size: 14px;">
                                    <i class="fa fa-check"></i> Posted
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning" style="padding: 8px 16px; font-size: 14px;">
                                    Draft
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"
                                  placeholder="Journal entry description..."><?= @$entry->description ?></textarea>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Journal Entry Lines -->
            <h5 class="mb-3"><i class="fa fa-list"></i> Journal Entry Lines</h5>

            <div class="table-responsive">
                <table class="table table-bordered" id="linesTable">
                    <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <tr>
                            <th style="width: 40%;">Account</th>
                            <th style="width: 30%;">Description</th>
                            <th style="width: 12%;">Debit</th>
                            <th style="width: 12%;">Credit</th>
                            <th style="width: 6%; text-align: center;">
                                <button type="button" class="btn btn-sm btn-light" id="addLineBtn" title="Add Line">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="linesBody">
                        <!-- Lines will be added here dynamically -->
                    </tbody>
                    <tfoot style="background: #f8fafc; font-weight: 700;">
                        <tr>
                            <td colspan="2" class="text-right">TOTALS:</td>
                            <td>
                                <input type="text" class="form-control" id="totalDebit" readonly
                                       style="background: #dbeafe; color: #0ea5e9; font-weight: 700; text-align: right;">
                            </td>
                            <td>
                                <input type="text" class="form-control" id="totalCredit" readonly
                                       style="background: #ede9fe; color: #8b5cf6; font-weight: 700; text-align: right;">
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right">DIFFERENCE:</td>
                            <td colspan="2">
                                <input type="text" class="form-control" id="difference" readonly
                                       style="background: #fef3c7; font-weight: 700; text-align: right;">
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                <strong>Note:</strong> Debits must equal credits for the entry to be balanced and posted.
            </div>

            <button type="submit" class="btn btn-primary" <?= @$entry->is_posted ? 'disabled' : '' ?>>
                <i class="fa fa-save"></i> Save Journal Entry
            </button>
            <a href="/journal-entries" class="btn btn-secondary">
                <i class="fa fa-times"></i> Cancel
            </a>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
let lineCounter = 0;
const accounts = <?= json_encode($accounts) ?>;
const existingLines = <?= json_encode(@$entry->lines ?? []) ?>;

$(document).ready(function() {
    // Load existing lines or add empty line
    if (existingLines && existingLines.length > 0) {
        existingLines.forEach(line => {
            addLine(line);
        });
    } else {
        addLine();
        addLine();
    }

    // Add line button
    $('#addLineBtn').on('click', function() {
        addLine();
    });

    // Form submission
    $('#journalEntryForm').on('submit', function(e) {
        e.preventDefault();

        // Collect lines data
        const lines = [];
        $('#linesBody tr').each(function() {
            const accountId = $(this).find('.account-select').val();
            const description = $(this).find('.line-description').val();
            const debit = $(this).find('.debit-amount').val();
            const credit = $(this).find('.credit-amount').val();

            if (accountId) {
                lines.push({
                    uuid_account_id: accountId,
                    description: description,
                    debit_amount: debit || 0,
                    credit_amount: credit || 0
                });
            }
        });

        $('#lines_data').val(JSON.stringify(lines));

        // Check if balanced
        const totalDebit = parseFloat($('#totalDebit').val() || 0);
        const totalCredit = parseFloat($('#totalCredit').val() || 0);
        const diff = Math.abs(totalDebit - totalCredit);

        if (diff > 0.01) {
            Swal.fire({
                icon: 'warning',
                title: 'Entry Not Balanced',
                text: 'Debits must equal credits. Difference: ' + diff.toFixed(2),
                confirmButtonColor: '#f59e0b'
            });
            return false;
        }

        this.submit();
    });
});

function addLine(lineData = null) {
    lineCounter++;

    let accountOptions = '<option value="">-- Select Account --</option>';
    accounts.forEach(account => {
        const selected = lineData && lineData.uuid_account_id === account.uuid ? 'selected' : '';
        accountOptions += `<option value="${account.uuid}" ${selected}>${account.account_code} - ${account.account_name}</option>`;
    });

    const html = `
        <tr data-line-id="${lineCounter}">
            <td>
                <select class="form-control account-select" required>
                    ${accountOptions}
                </select>
            </td>
            <td>
                <input type="text" class="form-control line-description" value="${lineData?.description || ''}" placeholder="Line description">
            </td>
            <td>
                <input type="number" step="0.01" class="form-control debit-amount" value="${lineData?.debit_amount || ''}"
                       placeholder="0.00" style="text-align: right;">
            </td>
            <td>
                <input type="number" step="0.01" class="form-control credit-amount" value="${lineData?.credit_amount || ''}"
                       placeholder="0.00" style="text-align: right;">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-line" title="Remove">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#linesBody').append(html);
    calculateTotals();

    // Bind events for this line
    $(`tr[data-line-id="${lineCounter}"] .debit-amount, tr[data-line-id="${lineCounter}"] .credit-amount`).on('input', function() {
        const row = $(this).closest('tr');

        // Clear opposite column when typing
        if ($(this).hasClass('debit-amount')) {
            row.find('.credit-amount').val('');
        } else {
            row.find('.debit-amount').val('');
        }

        calculateTotals();
    });

    $(`tr[data-line-id="${lineCounter}"] .remove-line`).on('click', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;

    $('#linesBody tr').each(function() {
        const debit = parseFloat($(this).find('.debit-amount').val() || 0);
        const credit = parseFloat($(this).find('.credit-amount').val() || 0);

        totalDebit += debit;
        totalCredit += credit;
    });

    $('#totalDebit').val(totalDebit.toFixed(2));
    $('#totalCredit').val(totalCredit.toFixed(2));

    const difference = totalDebit - totalCredit;
    $('#difference').val(difference.toFixed(2));

    // Color code the difference
    if (Math.abs(difference) < 0.01) {
        $('#difference').css('background', '#d1fae5').css('color', '#065f46');
    } else {
        $('#difference').css('background', '#fee2e2').css('color', '#991b1b');
    }
}
</script>

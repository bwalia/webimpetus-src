<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="receiptForm" method="post" action="<?php echo "/" . $tableName . "/update"; ?>">
            <input type="hidden" value="<?= @$receipt->uuid ?>" name="uuid" id="uuid">

            <div class="row">
                <div class="col-md-6">
                    <!-- Receipt Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="receipt_number">Receipt Number*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control"
                                   value="<?= @$receipt->receipt_number ?: 'Auto-generated' ?>"
                                   id="receipt_number" name="receipt_number">
                        </div>
                    </div>

                    <!-- Receipt Date -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="receipt_date">Receipt Date*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control required"
                                   value="<?= @$receipt->receipt_date ?: date('Y-m-d') ?>"
                                   id="receipt_date" name="receipt_date">
                        </div>
                    </div>

                    <!-- Receipt Type -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="receipt_type">Receipt Type*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="receipt_type" id="receipt_type" class="form-control required dashboard-dropdown">
                                <option value="Customer Payment" <?= @$receipt->receipt_type == 'Customer Payment' ? 'selected' : '' ?>>Customer Payment</option>
                                <option value="Sales Receipt" <?= @$receipt->receipt_type == 'Sales Receipt' ? 'selected' : '' ?>>Sales Receipt</option>
                                <option value="Deposit" <?= @$receipt->receipt_type == 'Deposit' ? 'selected' : '' ?>>Deposit</option>
                                <option value="Other" <?= @$receipt->receipt_type == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payer Name -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="payer_name">Payer Name*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control required"
                                   value="<?= @$receipt->payer_name ?>"
                                   id="payer_name" name="payer_name"
                                   placeholder="Who is paying?">
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="amount">Amount*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" step="0.01" class="form-control required"
                                   value="<?= @$receipt->amount ?>"
                                   id="amount" name="amount"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Currency -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="currency">Currency*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="currency" id="currency" class="form-control required dashboard-dropdown">
                                <option value="GBP" <?= @$receipt->currency == 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                <option value="USD" <?= @$receipt->currency == 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                <option value="EUR" <?= @$receipt->currency == 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                <option value="INR" <?= @$receipt->currency == 'INR' ? 'selected' : '' ?>>INR (₹)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Payment Method -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="payment_method">Payment Method*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="payment_method" id="payment_method" class="form-control required dashboard-dropdown">
                                <option value="Bank Transfer" <?= @$receipt->payment_method == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                <option value="Cheque" <?= @$receipt->payment_method == 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                                <option value="Cash" <?= @$receipt->payment_method == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                <option value="Credit Card" <?= @$receipt->payment_method == 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
                                <option value="Debit Card" <?= @$receipt->payment_method == 'Debit Card' ? 'selected' : '' ?>>Debit Card</option>
                                <option value="PayPal" <?= @$receipt->payment_method == 'PayPal' ? 'selected' : '' ?>>PayPal</option>
                                <option value="Stripe" <?= @$receipt->payment_method == 'Stripe' ? 'selected' : '' ?>>Stripe</option>
                                <option value="Other" <?= @$receipt->payment_method == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bank Account -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="bank_account_uuid">Bank Account*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="bank_account_uuid" id="bank_account_uuid" class="form-control required dashboard-dropdown">
                                <option value="">--Select Bank Account--</option>
                                <?php if (isset($bank_accounts) && !empty($bank_accounts)): ?>
                                    <?php foreach ($bank_accounts as $account): ?>
                                        <option value="<?= $account['uuid'] ?>"
                                                <?= @$receipt->bank_account_uuid == $account['uuid'] ? 'selected' : '' ?>>
                                            <?= $account['account_code'] ?> - <?= $account['account_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Reference -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="reference">Reference</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$receipt->reference ?>"
                                   id="reference" name="reference"
                                   placeholder="Transaction ID, Cheque #, etc.">
                        </div>
                    </div>

                    <!-- Invoice Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="invoice_number">Invoice Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$receipt->invoice_number ?>"
                                   id="invoice_number" name="invoice_number"
                                   placeholder="Related invoice number">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="status">Status*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="status" id="status" class="form-control required dashboard-dropdown">
                                <option value="Draft" <?= @$receipt->status == 'Draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="Pending" <?= @$receipt->status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Cleared" <?= @$receipt->status == 'Cleared' ? 'selected' : '' ?>>Cleared</option>
                                <option value="Cancelled" <?= @$receipt->status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="description">Description</label>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" name="description"
                                      id="description" rows="3"><?= @$receipt->description ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                $submitButtonText = 'Save Receipt';
                include(APPPATH . 'Views/common/submit-button.php');
            ?>

            <div class="row">
                <div class="col-md-12">
                    <?php if (!empty($receipt->uuid) && !empty($receipt->is_posted)): ?>
                        <span class="badge badge-info ml-2" style="font-size: 1rem; padding: 8px 12px;">
                            <i class="fa fa-check"></i> Posted to Journal
                        </span>
                    <?php elseif (!empty($receipt->uuid)): ?>
                        <button type="button" class="btn btn-success" onclick="postReceipt('<?= $receipt->uuid ?>')">
                            <i class="fa fa-check-circle"></i> Post to Journal
                        </button>
                        <a href="/receipts/pdf/<?= $receipt->uuid ?>" target="_blank" class="btn btn-info">
                            <i class="fa fa-print"></i> Print Receipt
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    function postReceipt(uuid) {
        if (!confirm('Are you sure you want to post this receipt to the journal? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: '/receipts/post/' + uuid,
            method: 'POST',
            success: function(response) {
                const result = JSON.parse(response);
                if (result.status) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            },
            error: function() {
                alert('Failed to post receipt!');
            }
        });
    }

    // Form validation
    $('#receiptForm').on('submit', function(e) {
        let isValid = true;
        $('.required').each(function() {
            if ($(this).val() == '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields!');
        }
    });
</script>

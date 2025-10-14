<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="paymentForm" method="post" action="<?php echo "/" . $tableName . "/update"; ?>">
            <input type="hidden" value="<?= @$payment->uuid ?>" name="uuid" id="uuid">

            <div class="row">
                <div class="col-md-6">
                    <!-- Payment Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="payment_number">Payment Number*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control"
                                   value="<?= @$payment->payment_number ?: 'Auto-generated' ?>"
                                   id="payment_number" name="payment_number">
                        </div>
                    </div>

                    <!-- Payment Date -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="payment_date">Payment Date*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control required"
                                   value="<?= @$payment->payment_date ?: date('Y-m-d') ?>"
                                   id="payment_date" name="payment_date">
                        </div>
                    </div>

                    <!-- Payment Type -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="payment_type">Payment Type*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="payment_type" id="payment_type" class="form-control required dashboard-dropdown">
                                <option value="Supplier Payment" <?= @$payment->payment_type == 'Supplier Payment' ? 'selected' : '' ?>>Supplier Payment</option>
                                <option value="Expense Payment" <?= @$payment->payment_type == 'Expense Payment' ? 'selected' : '' ?>>Expense Payment</option>
                                <option value="Refund" <?= @$payment->payment_type == 'Refund' ? 'selected' : '' ?>>Refund</option>
                                <option value="Other" <?= @$payment->payment_type == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payee Name -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="payee_name">Payee Name*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control required"
                                   value="<?= @$payment->payee_name ?>"
                                   id="payee_name" name="payee_name"
                                   placeholder="Who is being paid?">
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="amount">Amount*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" step="0.01" class="form-control required"
                                   value="<?= @$payment->amount ?>"
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
                                <option value="GBP" <?= @$payment->currency == 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                <option value="USD" <?= @$payment->currency == 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                <option value="EUR" <?= @$payment->currency == 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                <option value="INR" <?= @$payment->currency == 'INR' ? 'selected' : '' ?>>INR (₹)</option>
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
                                <option value="Bank Transfer" <?= @$payment->payment_method == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                <option value="Cheque" <?= @$payment->payment_method == 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                                <option value="Cash" <?= @$payment->payment_method == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                <option value="Credit Card" <?= @$payment->payment_method == 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
                                <option value="Debit Card" <?= @$payment->payment_method == 'Debit Card' ? 'selected' : '' ?>>Debit Card</option>
                                <option value="PayPal" <?= @$payment->payment_method == 'PayPal' ? 'selected' : '' ?>>PayPal</option>
                                <option value="Other" <?= @$payment->payment_method == 'Other' ? 'selected' : '' ?>>Other</option>
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
                                                <?= @$payment->bank_account_uuid == $account['uuid'] ? 'selected' : '' ?>>
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
                                   value="<?= @$payment->reference ?>"
                                   id="reference" name="reference"
                                   placeholder="Cheque #, Transaction ID, etc.">
                        </div>
                    </div>

                    <!-- Invoice Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="invoice_number">Invoice Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$payment->invoice_number ?>"
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
                                <option value="Draft" <?= @$payment->status == 'Draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="Pending" <?= @$payment->status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Completed" <?= @$payment->status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= @$payment->status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
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
                                      id="description" rows="3"><?= @$payment->description ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                $submitButtonText = 'Save Payment';
                include(APPPATH . 'Views/common/submit-button.php');
            ?>

            <div class="row">
                <div class="col-md-12">
                    <?php if (!empty($payment->uuid) && !empty($payment->is_posted)): ?>
                        <span class="badge badge-info ml-2" style="font-size: 1rem; padding: 8px 12px;">
                            <i class="fa fa-check"></i> Posted to Journal
                        </span>
                    <?php elseif (!empty($payment->uuid)): ?>
                        <button type="button" class="btn btn-success" onclick="postPayment('<?= $payment->uuid ?>')">
                            <i class="fa fa-check-circle"></i> Post to Journal
                        </button>
                        <a href="/payments/pdf/<?= $payment->uuid ?>" target="_blank" class="btn btn-info">
                            <i class="fa fa-print"></i> Print Remittance
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    function postPayment(uuid) {
        if (!confirm('Are you sure you want to post this payment to the journal? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: '/payments/post/' + uuid,
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
                alert('Failed to post payment!');
            }
        });
    }

    // Form validation
    $('#paymentForm').on('submit', function(e) {
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

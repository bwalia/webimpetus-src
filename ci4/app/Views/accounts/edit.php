<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="accountForm" method="post" action="/accounts/update">
            <input type="hidden" name="uuid" value="<?= @$account->uuid ?>" />
            <input type="hidden" name="id" value="<?= @$account->id ?>" />

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="account_code">Account Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_code" name="account_code"
                               value="<?= @$account->account_code ?>" required
                               placeholder="e.g., 1010, 2000, 4010">
                        <small class="form-text text-muted">Unique account identifier</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="account_name">Account Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_name" name="account_name"
                               value="<?= @$account->account_name ?>" required
                               placeholder="e.g., Cash, Accounts Receivable">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="account_type">Account Type <span class="text-danger">*</span></label>
                        <select name="account_type" id="account_type" class="form-control" required>
                            <option value="">-- Select Type --</option>
                            <option value="Asset" <?= (@$account->account_type == 'Asset') ? 'selected' : '' ?>>Asset</option>
                            <option value="Liability" <?= (@$account->account_type == 'Liability') ? 'selected' : '' ?>>Liability</option>
                            <option value="Equity" <?= (@$account->account_type == 'Equity') ? 'selected' : '' ?>>Equity</option>
                            <option value="Revenue" <?= (@$account->account_type == 'Revenue') ? 'selected' : '' ?>>Revenue</option>
                            <option value="Expense" <?= (@$account->account_type == 'Expense') ? 'selected' : '' ?>>Expense</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="account_subtype">Account Subtype</label>
                        <select name="account_subtype" id="account_subtype" class="form-control">
                            <option value="">-- Select Subtype --</option>
                            <optgroup label="Asset Subtypes" id="asset_subtypes" style="display: none;">
                                <option value="Current Asset">Current Asset</option>
                                <option value="Fixed Asset">Fixed Asset</option>
                                <option value="Other Asset">Other Asset</option>
                            </optgroup>
                            <optgroup label="Liability Subtypes" id="liability_subtypes" style="display: none;">
                                <option value="Current Liability">Current Liability</option>
                                <option value="Long-term Liability">Long-term Liability</option>
                            </optgroup>
                            <optgroup label="Equity Subtypes" id="equity_subtypes" style="display: none;">
                                <option value="Owner Equity">Owner Equity</option>
                            </optgroup>
                            <optgroup label="Revenue Subtypes" id="revenue_subtypes" style="display: none;">
                                <option value="Sales Revenue">Sales Revenue</option>
                                <option value="Service Revenue">Service Revenue</option>
                                <option value="Other Revenue">Other Revenue</option>
                            </optgroup>
                            <optgroup label="Expense Subtypes" id="expense_subtypes" style="display: none;">
                                <option value="Cost of Sales">Cost of Sales</option>
                                <option value="Operating Expense">Operating Expense</option>
                                <option value="Other Expense">Other Expense</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="normal_balance">Normal Balance <span class="text-danger">*</span></label>
                        <select name="normal_balance" id="normal_balance" class="form-control" required>
                            <option value="Debit" <?= (@$account->normal_balance == 'Debit') ? 'selected' : '' ?>>Debit</option>
                            <option value="Credit" <?= (@$account->normal_balance == 'Credit') ? 'selected' : '' ?>>Credit</option>
                        </select>
                        <small class="form-text text-muted">Auto-set based on type</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="parent_account_id">Parent Account</label>
                        <select name="parent_account_id" id="parent_account_id" class="form-control select2">
                            <option value="">-- No Parent (Top Level) --</option>
                            <?php foreach ($parent_accounts as $parent): ?>
                                <option value="<?= $parent['id'] ?>"
                                    <?= (@$account->parent_account_id == $parent['id']) ? 'selected' : '' ?>>
                                    <?= $parent['account_code'] ?> - <?= $parent['account_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">For sub-accounts</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="opening_balance">Opening Balance</label>
                        <input type="number" step="0.01" class="form-control" id="opening_balance" name="opening_balance"
                               value="<?= @$account->opening_balance ?>" placeholder="0.00">
                        <small class="form-text text-muted">Starting balance for this account</small>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Notes about this account..."><?= @$account->description ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active"
                                   name="is_active" value="1" <?= (!isset($account->is_active) || @$account->is_active) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_system_account"
                                   name="is_system_account" value="1" <?= (@$account->is_system_account) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_system_account">
                                System Account (Cannot be deleted)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Account
            </button>
            <a href="/accounts" class="btn btn-secondary">
                <i class="fa fa-times"></i> Cancel
            </a>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: '-- Select --',
            allowClear: true
        });

        // Set current subtype value
        var currentSubtype = '<?= @$account->account_subtype ?>';
        if (currentSubtype) {
            $('#account_subtype option[value="' + currentSubtype + '"]').prop('selected', true);
        }

        // Update normal balance and subtypes based on account type
        $('#account_type').on('change', function() {
            var type = $(this).val();

            // Auto-set normal balance
            if (type === 'Asset' || type === 'Expense') {
                $('#normal_balance').val('Debit');
            } else if (type === 'Liability' || type === 'Equity' || type === 'Revenue') {
                $('#normal_balance').val('Credit');
            }

            // Show relevant subtypes
            $('#account_subtype optgroup').hide();
            $('#account_subtype').val('');

            if (type) {
                $('#' + type.toLowerCase() + '_subtypes').show();
            }
        });

        // Trigger change on page load to show correct subtypes
        $('#account_type').trigger('change');
    });
</script>

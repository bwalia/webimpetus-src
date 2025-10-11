<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    .account-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .type-asset { background: #dbeafe; color: #1e40af; }
    .type-liability { background: #fee2e2; color: #991b1b; }
    .type-equity { background: #fce7f3; color: #831843; }
    .type-revenue { background: #d1fae5; color: #065f46; }
    .type-expense { background: #fef3c7; color: #92400e; }

    .balance-positive { color: #065f46; font-weight: 600; }
    .balance-negative { color: #991b1b; font-weight: 600; }
</style>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-between mb-3" style="padding-bottom: 0;">
        <div>
            <button type="button" id="initializeAccountsBtn" class="btn btn-info">
                <i class="fa fa-download"></i> Initialize Default Accounts
            </button>
            <a href="/balance-sheet" class="btn btn-success">
                <i class="fa fa-chart-bar"></i> Balance Sheet
            </a>
            <a href="/trial-balance" class="btn btn-warning">
                <i class="fa fa-balance-scale"></i> Trial Balance
            </a>
            <a href="/profit-loss" class="btn btn-primary">
                <i class="fa fa-chart-line"></i> Profit & Loss
            </a>
        </div>
        <div>
            <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
                <i class="fa fa-refresh"></i> Refresh
            </button>
            <a href="/accounts/edit" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add New Account
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="row">
        <div class="col-md-2">
            <div class="card text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                <div class="card-body">
                    <h6 style="color: rgba(255,255,255,0.8); font-size: 11px; margin-bottom: 8px;">ASSETS</h6>
                    <h3 id="totalAssets" style="margin: 0; color: white;">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;">
                <div class="card-body">
                    <h6 style="color: rgba(255,255,255,0.8); font-size: 11px; margin-bottom: 8px;">LIABILITIES</h6>
                    <h3 id="totalLiabilities" style="margin: 0; color: white;">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border: none;">
                <div class="card-body">
                    <h6 style="color: rgba(255,255,255,0.8); font-size: 11px; margin-bottom: 8px;">EQUITY</h6>
                    <h3 id="totalEquity" style="margin: 0; color: white;">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; border: none;">
                <div class="card-body">
                    <h6 style="color: rgba(255,255,255,0.8); font-size: 11px; margin-bottom: 8px;">REVENUE</h6>
                    <h3 id="totalRevenue" style="margin: 0; color: white;">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border: none;">
                <div class="card-body">
                    <h6 style="color: rgba(255,255,255,0.8); font-size: 11px; margin-bottom: 8px;">EXPENSES</h6>
                    <h3 id="totalExpenses" style="margin: 0; color: white;">0</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accounts Table -->
<div class="white_card_body">
    <div class="QA_table" id="accountsTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    const columnRenderers = {
        account_code: function(data, type, row) {
            return '<span style="font-family: monospace; font-weight: 600; color: #667eea;">' + data + '</span>';
        },
        account_name: function(data, type, row) {
            return '<a href="/accounts/edit/' + row.uuid + '" style="color: #334155; font-weight: 600;">' + data + '</a>';
        },
        account_type: function(data, type, row) {
            const typeClass = 'type-' + data.toLowerCase();
            return '<span class="account-badge ' + typeClass + '">' + data + '</span>';
        },
        account_subtype: function(data, type, row) {
            return data || '-';
        },
        current_balance: function(data, type, row) {
            const balance = parseFloat(data || 0);
            const formatted = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(Math.abs(balance));

            const className = balance >= 0 ? 'balance-positive' : 'balance-negative';
            return '<span class="' + className + '">' + formatted + '</span>';
        },
        normal_balance: function(data, type, row) {
            const color = data === 'Debit' ? '#0ea5e9' : '#8b5cf6';
            return '<span style="color: ' + color + '; font-weight: 600;"><i class="fa fa-' +
                   (data === 'Debit' ? 'arrow-up' : 'arrow-down') + '"></i> ' + data + '</span>';
        },
        is_active: function(data, type, row) {
            if (data == 1) {
                return '<span class="badge badge-success">Active</span>';
            } else {
                return '<span class="badge badge-secondary">Inactive</span>';
            }
        },
        actions: function(data, type, row) {
            let html = '<div class="btn-group" role="group">';
            html += '<a href="/accounts/edit/' + row.uuid + '" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a>';
            if (!row.is_system_account) {
                html += '<button type="button" class="btn btn-sm btn-danger delete-account" data-uuid="' + row.uuid + '" title="Delete"><i class="fa fa-trash"></i></button>';
            }
            html += '</div>';
            return html;
        }
    };

    let columnsTitle = ['Code', 'Account Name', 'Type', 'Subtype', 'Balance', 'Normal', 'Status', 'Actions'];
    let columnsMachineName = ['account_code', 'account_name', 'account_type', 'account_subtype', 'current_balance', 'normal_balance', 'is_active', 'actions'];

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "accounts",
        apiPath: "accounts/accountsList",
        selector: "accountsTable",
        columnRenderers: columnRenderers
    });

    // Calculate totals
    function updateSummaryCards() {
        fetch('/accounts/accountsList')
            .then(response => response.json())
            .then(result => {
                if (result.data) {
                    calculateTotals(result.data);
                }
            })
            .catch(error => console.error('Error loading summary:', error));
    }

    function calculateTotals(accounts) {
        let totals = {
            Asset: 0,
            Liability: 0,
            Equity: 0,
            Revenue: 0,
            Expense: 0
        };

        accounts.forEach(account => {
            if (account.is_active == 1) {
                totals[account.account_type] += parseFloat(account.current_balance || 0);
            }
        });

        $('#totalAssets').text(formatCurrency(totals.Asset));
        $('#totalLiabilities').text(formatCurrency(totals.Liability));
        $('#totalEquity').text(formatCurrency(totals.Equity));
        $('#totalRevenue').text(formatCurrency(totals.Revenue));
        $('#totalExpenses').text(formatCurrency(totals.Expense));
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(Math.abs(amount));
    }

    // Initialize default accounts
    $('#initializeAccountsBtn').on('click', function() {
        Swal.fire({
            title: 'Initialize Chart of Accounts?',
            text: 'This will add 30+ default accounts to your business. Existing accounts will not be affected.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Initialize',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/accounts/initializeChartOfAccounts',
                    method: 'POST',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    }
                });
            }
        });
    });

    // Delete account
    $(document).on('click', '.delete-account', function() {
        const uuid = $(this).data('uuid');

        Swal.fire({
            title: 'Delete Account?',
            text: 'This action cannot be undone if the account has no transactions.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/accounts/delete/' + uuid,
                    method: 'POST',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: response.message,
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonColor: '#dc2626'
                            });
                        }
                    }
                });
            }
        });
    });

    // Initialize
    $(document).ready(function() {
        updateSummaryCards();
    });
</script>

<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<?php if ($current_period): ?>
<div class="alert alert-info">
    <i class="fa fa-calendar-check"></i>
    <strong>Current Period:</strong> <?= $current_period['period_name'] ?>
    (<?= date('M d, Y', strtotime($current_period['start_date'])) ?> - <?= date('M d, Y', strtotime($current_period['end_date'])) ?>)
</div>
<?php else: ?>
<div class="alert alert-warning">
    <i class="fa fa-exclamation-triangle"></i>
    <strong>No Current Period Set</strong> - Please create and set an accounting period.
</div>
<?php endif; ?>

<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <a href="/accounting-periods/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Accounting Period
        </a>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="periodsTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    const columnRenderers = {
        period_name: function(data, type, row) {
            let html = '<a href="/accounting-periods/edit/' + row.uuid + '" style="font-weight: 600; color: #667eea;">' + data + '</a>';
            if (row.is_current == 1) {
                html += ' <span class="badge badge-success ml-2"><i class="fa fa-check"></i> CURRENT</span>';
            }
            return html;
        },
        start_date: function(data, type, row) {
            return new Date(data).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },
        end_date: function(data, type, row) {
            return new Date(data).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },
        is_closed: function(data, type, row) {
            if (data == 1) {
                return '<span class="badge badge-danger"><i class="fa fa-lock"></i> Closed</span>';
            } else {
                return '<span class="badge badge-success"><i class="fa fa-unlock"></i> Open</span>';
            }
        },
        actions: function(data, type, row) {
            let html = '<div class="btn-group" role="group">';
            html += '<a href="/accounting-periods/edit/' + row.uuid + '" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a>';

            if (row.is_current == 0 && row.is_closed == 0) {
                html += '<button type="button" class="btn btn-sm btn-success set-current" data-uuid="' + row.uuid + '" title="Set as Current"><i class="fa fa-check"></i></button>';
            }

            if (row.is_closed == 0) {
                html += '<button type="button" class="btn btn-sm btn-warning close-period" data-uuid="' + row.uuid + '" title="Close Period"><i class="fa fa-lock"></i></button>';
            }

            html += '</div>';
            return html;
        }
    };

    let columnsTitle = ['Period Name', 'Start Date', 'End Date', 'Status', 'Actions'];
    let columnsMachineName = ['period_name', 'start_date', 'end_date', 'is_closed', 'actions'];

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "accounting_periods",
        apiPath: "accounting-periods/periodsList",
        selector: "periodsTable",
        columnRenderers: columnRenderers
    });

    // Set current period
    $(document).on('click', '.set-current', function() {
        const uuid = $(this).data('uuid');

        Swal.fire({
            title: 'Set as Current Period?',
            text: 'This will be used for all accounting transactions.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Set Current',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/accounting-periods/set-current/' + uuid,
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

    // Close period
    $(document).on('click', '.close-period', function() {
        const uuid = $(this).data('uuid');

        Swal.fire({
            title: 'Close Accounting Period?',
            text: 'Once closed, no new transactions can be added to this period.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Close It',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/accounting-periods/close-period/' + uuid,
                    method: 'POST',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Closed',
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
</script>

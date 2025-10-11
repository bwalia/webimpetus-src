<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<style>
    .entry-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .type-manual { background: #dbeafe; color: #1e40af; }
    .type-sales { background: #d1fae5; color: #065f46; }
    .type-purchase { background: #fee2e2; color: #991b1b; }
    .status-posted { background: #d1fae5; color: #065f46; }
    .status-draft { background: #fef3c7; color: #92400e; }
</style>

<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/journal-entries/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Journal Entry
        </a>
    </div>
</div>

<div class="white_card_body">
    <div class="QA_table" id="journalEntriesTable"></div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    const columnRenderers = {
        entry_number: function(data, type, row) {
            return '<a href="/journal-entries/edit/' + row.uuid + '" style="font-family: monospace; font-weight: 600; color: #667eea;">' + data + '</a>';
        },
        entry_date: function(data, type, row) {
            return new Date(data).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },
        entry_type: function(data, type, row) {
            const typeClass = 'type-' + data.toLowerCase().replace(' ', '');
            return '<span class="entry-badge ' + typeClass + '">' + data + '</span>';
        },
        description: function(data, type, row) {
            return data || '-';
        },
        total_debit: function(data, type, row) {
            return '<span style="color: #0ea5e9; font-weight: 600;">' + parseFloat(data).toFixed(2) + '</span>';
        },
        total_credit: function(data, type, row) {
            return '<span style="color: #8b5cf6; font-weight: 600;">' + parseFloat(data).toFixed(2) + '</span>';
        },
        is_balanced: function(data, type, row) {
            if (data == 1) {
                return '<span class="badge badge-success"><i class="fa fa-check"></i> Balanced</span>';
            } else {
                return '<span class="badge badge-danger"><i class="fa fa-times"></i> Unbalanced</span>';
            }
        },
        is_posted: function(data, type, row) {
            if (data == 1) {
                return '<span class="entry-badge status-posted"><i class="fa fa-check"></i> Posted</span>';
            } else {
                return '<span class="entry-badge status-draft">Draft</span>';
            }
        },
        actions: function(data, type, row) {
            let html = '<div class="btn-group" role="group">';
            html += '<a href="/journal-entries/edit/' + row.uuid + '" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a>';

            if (row.is_posted == 0) {
                html += '<button type="button" class="btn btn-sm btn-success post-entry" data-uuid="' + row.uuid + '" title="Post"><i class="fa fa-check"></i></button>';
                html += '<button type="button" class="btn btn-sm btn-danger delete-entry" data-uuid="' + row.uuid + '" title="Delete"><i class="fa fa-trash"></i></button>';
            }

            html += '</div>';
            return html;
        }
    };

    let columnsTitle = ['Entry #', 'Date', 'Type', 'Description', 'Debit', 'Credit', 'Balanced', 'Status', 'Actions'];
    let columnsMachineName = ['entry_number', 'entry_date', 'entry_type', 'description', 'total_debit', 'total_credit', 'is_balanced', 'is_posted', 'actions'];

    initializeGridTable({
        columnsTitle,
        columnsMachineName,
        tableName: "journal_entries",
        apiPath: "journal-entries/journalEntriesList",
        selector: "journalEntriesTable",
        columnRenderers: columnRenderers
    });

    // Post entry
    $(document).on('click', '.post-entry', function() {
        const uuid = $(this).data('uuid');

        Swal.fire({
            title: 'Post Journal Entry?',
            text: 'Once posted, this entry cannot be edited or deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Post It',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/journal-entries/post/' + uuid,
                    method: 'POST',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Posted',
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

    // Delete entry
    $(document).on('click', '.delete-entry', function() {
        const uuid = $(this).data('uuid');

        Swal.fire({
            title: 'Delete Journal Entry?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/journal-entries/delete/' + uuid,
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
</script>

<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<div class="main_content_iner">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Project Jobs</h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">

                        <!-- Summary Cards -->
                        <?php if (isset($summary)): ?>
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5><?= $summary->total_jobs ?? 0 ?></h5>
                                        <small>Total Jobs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center bg-warning">
                                    <div class="card-body text-white">
                                        <h5><?= $summary->planning ?? 0 ?></h5>
                                        <small>Planning</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center bg-primary">
                                    <div class="card-body text-white">
                                        <h5><?= $summary->in_progress ?? 0 ?></h5>
                                        <small>In Progress</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center bg-success">
                                    <div class="card-body text-white">
                                        <h5><?= $summary->completed ?? 0 ?></h5>
                                        <small>Completed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center bg-secondary">
                                    <div class="card-body text-white">
                                        <h5><?= $summary->on_hold ?? 0 ?></h5>
                                        <small>On Hold</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-center bg-danger">
                                    <div class="card-body text-white">
                                        <h5><?= $summary->cancelled ?? 0 ?></h5>
                                        <small>Cancelled</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="mb-3">
                            <a href="/project_jobs/edit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> New Job
                            </a>
                            <a href="/project_job_scheduler/calendar" class="btn btn-secondary">
                                <i class="fa fa-calendar"></i> Calendar
                            </a>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="jobsTable">
                                <thead>
                                    <tr>
                                        <th>Job Number</th>
                                        <th>Job Name</th>
                                        <th>Project</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Assigned To</th>
                                        <th>Dates</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
$(document).ready(function() {
    $('#jobsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/project_jobs/jobsList',
            type: 'GET',
            dataSrc: 'data',
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                console.error('Response:', xhr.responseText);
                alert('Error loading jobs data. Check console for details.');
            }
        },
        columns: [
            { data: 'job_number' },
            { data: 'job_name' },
            { data: 'project_name' },
            { data: 'job_type' },
            {
                data: 'priority',
                render: function(data) {
                    const badges = {
                        'Low': 'secondary',
                        'Normal': 'info',
                        'High': 'warning',
                        'Urgent': 'danger'
                    };
                    return `<span class="badge badge-${badges[data] || 'secondary'}">${data}</span>`;
                }
            },
            {
                data: 'status',
                render: function(data) {
                    const badges = {
                        'Planning': 'warning',
                        'In Progress': 'primary',
                        'On Hold': 'secondary',
                        'Completed': 'success',
                        'Cancelled': 'danger'
                    };
                    return `<span class="badge badge-${badges[data] || 'secondary'}">${data}</span>`;
                }
            },
            {
                data: 'completion_percentage',
                render: function(data) {
                    const percentage = data || 0;
                    return `<div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: ${percentage}%" aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">${percentage}%</div>
                    </div>`;
                }
            },
            {
                data: null,
                render: function(row) {
                    if (row.assigned_user_name) return row.assigned_user_name;
                    if (row.assigned_employee_first_name) return row.assigned_employee_first_name + ' ' + row.assigned_employee_surname;
                    return 'Unassigned';
                }
            },
            {
                data: null,
                render: function(row) {
                    return (row.planned_start_date || 'N/A') + ' - ' + (row.planned_end_date || 'N/A');
                }
            },
            {
                data: 'uuid',
                render: function(data, type, row) {
                    return `
                        <a href="/project_jobs/edit/${data}" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="/project_job_phases/index/${data}" class="btn btn-sm btn-info" title="Phases">
                            <i class="fa fa-tasks"></i>
                        </a>
                        <a href="/project_jobs/delete/${data}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this job?')" title="Delete">
                            <i class="fa fa-trash"></i>
                        </a>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>

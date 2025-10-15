<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/project_job_scheduler/calendar" class="btn btn-secondary mr-2">
            <i class="fa fa-calendar"></i> Calendar
        </a>
        <a href="/project_jobs/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Job
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-briefcase"></i> Total Jobs</div>
            <div class="summary-card-value" id="totalJobs">0</div>
            <div class="summary-card-subtitle">all jobs</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-spinner"></i> In Progress</div>
            <div class="summary-card-value" id="inProgressJobs">0</div>
            <div class="summary-card-subtitle">active jobs</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-check-circle"></i> Completed</div>
            <div class="summary-card-value" id="completedJobs">0</div>
            <div class="summary-card-subtitle">finished</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-clock"></i> Estimated Hours</div>
            <div class="summary-card-value" id="totalEstimatedHours">0</div>
            <div class="summary-card-subtitle">total hours</div>
        </div>
    </div>
</div>

<!-- List View -->
<div class="white_card_body">
    <div class="main_content_iner">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-lg-12">

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

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
let jobsData = [];

$(document).ready(function() {
    // Initialize DataTable
    $('#jobsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/project_jobs/jobsList',
            type: 'GET',
            dataSrc: function(json) {
                // Store data for summary cards
                jobsData = json.data || [];
                updateJobSummaryCards(jobsData);
                return jobsData;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                console.error('Response:', xhr.responseText);
                alert('Error loading jobs data. Check console for details.');
            }
        },
        columns: [
            {
                data: 'job_number',
                render: function(data, type, row) {
                    return '<a href="/project_jobs/edit/' + row.uuid + '" style="color: #667eea; font-weight: 600;">' + data + '</a>';
                }
            },
            {
                data: 'job_name',
                render: function(data, type, row) {
                    return '<div style="border-left: 4px solid #667eea; padding-left: 8px;"><a href="/project_jobs/edit/' + row.uuid + '" style="color: #667eea; font-weight: 600;">' + data + '</a></div>';
                }
            },
            { data: 'project_name' },
            { data: 'job_type' },
            {
                data: 'priority',
                render: function(data) {
                    const priorities = {
                        'Low': { class: 'priority-low', style: 'background-color: #dbeafe; color: #1e40af;' },
                        'Normal': { class: 'priority-medium', style: 'background-color: #fef3c7; color: #92400e;' },
                        'High': { class: 'priority-high', style: 'background-color: #fed7aa; color: #9a3412;' },
                        'Urgent': { class: 'priority-critical', style: 'background-color: #fee2e2; color: #991b1b;' }
                    };
                    const priority = priorities[data] || priorities['Normal'];
                    return '<span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; display: inline-block; ' + priority.style + '">' + data + '</span>';
                }
            },
            {
                data: 'status',
                render: function(data) {
                    const statuses = {
                        'Planning': { style: 'background-color: #e5e7eb; color: #374151;' },
                        'In Progress': { style: 'background-color: #fed7aa; color: #9a3412;' },
                        'On Hold': { style: 'background-color: #fef3c7; color: #92400e;' },
                        'Completed': { style: 'background-color: #d1fae5; color: #065f46;' },
                        'Cancelled': { style: 'background-color: #fee2e2; color: #991b1b;' }
                    };
                    const status = statuses[data] || statuses['Planning'];
                    return '<span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; display: inline-block; text-transform: capitalize; ' + status.style + '">' + data + '</span>';
                }
            },
            {
                data: 'completion_percentage',
                render: function(data) {
                    const percentage = data || 0;
                    return '<div style="display: flex; align-items: center; gap: 8px;"><div style="width: 80px; height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; display: inline-block;"><div style="height: 100%; background: #10b981; width: ' + percentage + '%;"></div></div><span style="font-size: 0.8rem;">' + percentage + '%</span></div>';
                }
            },
            {
                data: null,
                render: function(row) {
                    if (row.assigned_user_name) return row.assigned_user_name;
                    if (row.assigned_employee_first_name) return row.assigned_employee_first_name + ' ' + row.assigned_employee_surname;
                    return '<span style="color: #9ca3af;">Unassigned</span>';
                }
            },
            {
                data: null,
                render: function(row) {
                    const startDate = row.planned_start_date || 'N/A';
                    const endDate = row.planned_end_date || 'N/A';
                    return '<small>' + startDate + ' → ' + endDate + '</small>';
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

// Update summary cards
function updateJobSummaryCards(jobs) {
    let totalJobs = jobs.length;
    let inProgressCount = 0;
    let completedCount = 0;
    let totalEstimatedHours = 0;

    jobs.forEach(job => {
        const status = (job.status || '').toLowerCase();

        if (status === 'in progress') {
            inProgressCount++;
        } else if (status === 'completed') {
            completedCount++;
        }

        totalEstimatedHours += parseFloat(job.estimated_hours || 0);
    });

    // Update UI
    document.getElementById('totalJobs').textContent = totalJobs;
    document.getElementById('inProgressJobs').textContent = inProgressCount;
    document.getElementById('completedJobs').textContent = completedCount;
    document.getElementById('totalEstimatedHours').textContent = totalEstimatedHours.toFixed(0) + 'h';

    console.log('Job metrics updated:', {
        total: totalJobs,
        inProgress: inProgressCount,
        completed: completedCount,
        totalHours: totalEstimatedHours
    });
}
</script>

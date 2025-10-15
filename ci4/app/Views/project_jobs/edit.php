<?php
// Helper function to format date from Y-m-d to m/d/Y for datepicker
function formatDateForInput($date) {
    if (empty($date)) return '';
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt ? $dt->format('m/d/Y') : '';
}
?>
<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">

        <form id="jobForm" method="post" action="/project_jobs/update" enctype="multipart/form-data">

            <!-- Row 1: Project and Job Name -->
            <div class="form-row">
                <div class="form-group required col-md-6">
                    <label for="uuid_project_id">Project</label>
                    <select id="uuid_project_id" name="uuid_project_id" class="form-control required">
                        <option value="" selected="">--Select Project--</option>
                        <?php if (isset($job) && !empty($job->uuid_project_id)): ?>
                            <option value="<?= $job->uuid_project_id ?>" selected><?= $job->project_name ?? '' ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group required col-md-6">
                    <label for="job_name">Job Name</label>
                    <input type="text" autocomplete="off" class="form-control required" id="job_name" name="job_name"
                        placeholder="Enter job name" value="<?= @$job->job_name ?>">
                </div>
            </div>

            <!-- Row 2: Job Number and Type -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="job_number">Job Number</label>
                    <input type="text" class="form-control" id="job_number" name="job_number"
                        placeholder="Auto-generated if left empty" value="<?= @$job->job_number ?>" readonly>
                    <small class="form-text text-muted">Auto-generated for new jobs</small>
                </div>

                <div class="form-group col-md-6">
                    <label for="job_type">Job Type</label>
                    <select name="job_type" id="job_type" class="form-control select2">
                        <option value="Development" <?php if (@$job->job_type == "Development") echo "selected" ?>>Development</option>
                        <option value="Design" <?php if (@$job->job_type == "Design") echo "selected" ?>>Design</option>
                        <option value="Testing" <?php if (@$job->job_type == "Testing") echo "selected" ?>>Testing</option>
                        <option value="Deployment" <?php if (@$job->job_type == "Deployment") echo "selected" ?>>Deployment</option>
                        <option value="Support" <?php if (@$job->job_type == "Support") echo "selected" ?>>Support</option>
                        <option value="Research" <?php if (@$job->job_type == "Research") echo "selected" ?>>Research</option>
                        <option value="Other" <?php if (@$job->job_type == "Other") echo "selected" ?>>Other</option>
                    </select>
                </div>
            </div>

            <!-- Row 3: Priority and Status -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control select2">
                        <option value="Low" <?php if (@$job->priority == "Low") echo "selected" ?>>Low</option>
                        <option value="Normal" <?php if (@$job->priority == "Normal" || empty($job)) echo "selected" ?>>Normal</option>
                        <option value="High" <?php if (@$job->priority == "High") echo "selected" ?>>High</option>
                        <option value="Urgent" <?php if (@$job->priority == "Urgent") echo "selected" ?>>Urgent</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control select2">
                        <option value="Planning" <?php if (@$job->status == "Planning" || empty($job)) echo "selected" ?>>Planning</option>
                        <option value="In Progress" <?php if (@$job->status == "In Progress") echo "selected" ?>>In Progress</option>
                        <option value="On Hold" <?php if (@$job->status == "On Hold") echo "selected" ?>>On Hold</option>
                        <option value="Completed" <?php if (@$job->status == "Completed") echo "selected" ?>>Completed</option>
                        <option value="Cancelled" <?php if (@$job->status == "Cancelled") echo "selected" ?>>Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Row 4: Assignment -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="assigned_to_user_id">Assign to User</label>
                    <select id="assigned_to_user_id" name="assigned_to_user_id" class="form-control">
                        <option value="">--Unassigned--</option>
                        <?php if (isset($users)): foreach ($users as $user):
                            $userId = is_object($user) ? $user->id : $user['id'];
                            $userName = is_object($user) ? $user->name : $user['name'];
                        ?>
                            <option value="<?= $userId ?>" <?php if (@$job->assigned_to_user_id == $userId) echo "selected" ?>>
                                <?= $userName ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="assigned_to_employee_id">Assign to Employee</label>
                    <select id="assigned_to_employee_id" name="assigned_to_employee_id" class="form-control">
                        <option value="">--Unassigned--</option>
                        <?php if (isset($employees)): foreach ($employees as $employee):
                            $employeeId = is_object($employee) ? $employee->id : $employee['id'];
                            $employeeName = is_object($employee) ? ($employee->first_name . ' ' . $employee->surname) : ($employee['first_name'] . ' ' . $employee['surname']);
                        ?>
                            <option value="<?= $employeeId ?>" <?php if (@$job->assigned_to_employee_id == $employeeId) echo "selected" ?>>
                                <?= $employeeName ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>

            <!-- Row 5: Planned Dates -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="planned_start_date">Planned Start Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="planned_start_date"
                        name="planned_start_date" placeholder="" value="<?= formatDateForInput(@$job->planned_start_date) ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="planned_end_date">Planned End Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="planned_end_date"
                        name="planned_end_date" placeholder="" value="<?= formatDateForInput(@$job->planned_end_date) ?>">
                    <span class="form-control-feedback text-danger" id="dateError"></span>
                </div>
            </div>

            <!-- Row 6: Actual Dates -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="actual_start_date">Actual Start Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="actual_start_date"
                        name="actual_start_date" placeholder="" value="<?= formatDateForInput(@$job->actual_start_date) ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="actual_end_date">Actual End Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="actual_end_date"
                        name="actual_end_date" placeholder="" value="<?= formatDateForInput(@$job->actual_end_date) ?>">
                </div>
            </div>

            <!-- Row 7: Estimated Hours and Cost -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="estimated_hours">Estimated Hours</label>
                    <input type="number" class="form-control" id="estimated_hours" name="estimated_hours"
                        step="0.25" placeholder="0.00" value="<?= @$job->estimated_hours ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="estimated_cost">Estimated Cost</label>
                    <input type="number" class="form-control" id="estimated_cost" name="estimated_cost"
                        step="0.01" placeholder="0.00" value="<?= @$job->estimated_cost ?>">
                </div>
            </div>

            <!-- Row 8: Hourly Rate and Billable -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="hourly_rate">Hourly Rate</label>
                    <input type="number" class="form-control" id="hourly_rate" name="hourly_rate"
                        step="0.01" placeholder="0.00" value="<?= @$job->hourly_rate ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="billable">Billable</label>
                    <select name="billable" id="billable" class="form-control select2">
                        <option value="1" <?php if (@$job->billable == 1 || empty($job)) echo "selected" ?>>Yes</option>
                        <option value="0" <?php if (@$job->billable == 0 && !empty($job)) echo "selected" ?>>No</option>
                    </select>
                </div>
            </div>

            <!-- Row 9: Completion Percentage -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="completion_percentage">Completion Percentage: <span id="percentage_display"><?= @$job->completion_percentage ?? 0 ?>%</span></label>
                    <input type="range" class="form-control-range" id="completion_percentage" name="completion_percentage"
                        min="0" max="100" step="5" value="<?= @$job->completion_percentage ?? 0 ?>">
                </div>
            </div>

            <!-- Row 10: Job Description -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="job_description">Job Description</label>
                    <textarea class="form-control" id="job_description" name="job_description" rows="4"><?= @$job->job_description ?></textarea>
                </div>
            </div>

            <!-- Row 11: Notes -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= @$job->notes ?></textarea>
                </div>
            </div>

            <input type="hidden" name="id" value="<?= @$job->id ?>" />
            <input type="hidden" name="uuid" value="<?= @$job->uuid ?>" />

            <button type="submit" id="jobSubmit" class="btn btn-primary">Submit</button>
            <a href="/project_jobs" class="btn btn-secondary">Cancel</a>
        </form>

        <!-- Phases Section (only show when editing existing job) -->
        <?php if (isset($job) && !empty($job->uuid)): ?>
        <hr class="my-4">
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Job Phases</h4>
                <a href="/project_job_phases/edit/0/<?= $job->uuid ?>" class="btn btn-sm btn-success">
                    <i class="fa fa-plus"></i> Add Phase
                </a>
            </div>
            <a href="/project_job_phases/index/<?= $job->uuid ?>" class="btn btn-info">
                <i class="fa fa-tasks"></i> Manage Phases
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
$(document).ready(function() {
    // Initialize Select2 for project search
    $("#uuid_project_id").select2({
        ajax: {
            url: "/common/searchProjects",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term || ''
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.uuid,
                            text: item.name
                        }
                    })
                };
            }
        },
        minimumInputLength: 0,
        placeholder: '--Select Project--',
        allowClear: true
    });

    // Initialize other Select2 dropdowns
    $("#assigned_to_user_id, #assigned_to_employee_id, #job_type, #priority, #status, #billable").select2();

    // Update percentage display when slider changes
    $("#completion_percentage").on("input", function() {
        $("#percentage_display").text($(this).val() + "%");
    });

    // Form validation
    $("#jobSubmit").click(function(event) {
        // Validate job name
        if ($("#job_name").val().trim() === "") {
            alert("Job Name is required");
            event.preventDefault();
            return false;
        }

        // Validate project selection
        if ($("#uuid_project_id").val() === "") {
            alert("Please select a Project");
            event.preventDefault();
            return false;
        }

        // Validate dates
        const plannedStart = $("#planned_start_date").val();
        const plannedEnd = $("#planned_end_date").val();
        if (plannedStart && plannedEnd) {
            if (!validateEndDate(plannedStart, plannedEnd, event)) {
                return false;
            }
        }
    });

    // Date validation
    $("#planned_end_date").change(function() {
        const startDate = $("#planned_start_date").val();
        const endDate = $(this).val();
        if (startDate && endDate) {
            validateEndDate(startDate, endDate, null);
        }
    });

    function validateEndDate(startDateStr, endDateStr, evt) {
        const endDate = new Date(endDateStr);
        const startDate = new Date(startDateStr);
        const timeDifference = endDate - startDate;
        const daysDifference = Math.round(timeDifference / (1000 * 60 * 60 * 24));

        if (daysDifference < 0) {
            $("#dateError").text("Planned End Date should be after Planned Start Date.");
            if (evt !== null) {
                evt.preventDefault();
            }
            return false;
        } else {
            $("#dateError").text("");
            return true;
        }
    }
});
</script>

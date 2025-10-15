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

        <form id="phaseForm" method="post" action="/project_job_phases/update" enctype="multipart/form-data">

            <!-- Job Context -->
            <?php if (isset($job)): ?>
            <div class="alert alert-info mb-4">
                <strong>Job:</strong> <?= $job->job_name ?> (<?= $job->job_number ?>)<br>
                <strong>Project:</strong> <?= $job->project_name ?? 'N/A' ?>
            </div>
            <?php endif; ?>

            <!-- Row 1: Phase Name and Number -->
            <div class="form-row">
                <div class="form-group required col-md-6">
                    <label for="phase_name">Phase Name</label>
                    <input type="text" autocomplete="off" class="form-control required" id="phase_name" name="phase_name"
                        placeholder="Enter phase name" value="<?= @$phase->phase_name ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="phase_number">Phase Number</label>
                    <input type="text" class="form-control" id="phase_number" name="phase_number"
                        placeholder="Auto-generated if left empty" value="<?= @$phase->phase_number ?>" readonly>
                    <small class="form-text text-muted">Auto-generated for new phases</small>
                </div>
            </div>

            <!-- Row 2: Phase Order and Status -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="phase_order">Phase Order</label>
                    <input type="number" class="form-control" id="phase_order" name="phase_order"
                        min="1" value="<?= @$phase->phase_order ?? 1 ?>">
                    <small class="form-text text-muted">Lower numbers appear first</small>
                </div>

                <div class="form-group col-md-6">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control select2">
                        <option value="Not Started" <?php if (@$phase->status == "Not Started" || empty($phase)) echo "selected" ?>>Not Started</option>
                        <option value="In Progress" <?php if (@$phase->status == "In Progress") echo "selected" ?>>In Progress</option>
                        <option value="Completed" <?php if (@$phase->status == "Completed") echo "selected" ?>>Completed</option>
                        <option value="Blocked" <?php if (@$phase->status == "Blocked") echo "selected" ?>>Blocked</option>
                    </select>
                </div>
            </div>

            <!-- Row 3: Assignment -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="assigned_to_user_id">Assign to User</label>
                    <select id="assigned_to_user_id" name="assigned_to_user_id" class="form-control">
                        <option value="">--Unassigned--</option>
                        <?php if (isset($users)): foreach ($users as $user):
                            $userId = is_object($user) ? $user->id : $user['id'];
                            $userName = is_object($user) ? $user->name : $user['name'];
                        ?>
                            <option value="<?= $userId ?>" <?php if (@$phase->assigned_to_user_id == $userId) echo "selected" ?>>
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
                            <option value="<?= $employeeId ?>" <?php if (@$phase->assigned_to_employee_id == $employeeId) echo "selected" ?>>
                                <?= $employeeName ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>

            <!-- Row 4: Planned Dates -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="planned_start_date">Planned Start Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="planned_start_date"
                        name="planned_start_date" placeholder="" value="<?= formatDateForInput(@$phase->planned_start_date) ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="planned_end_date">Planned End Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="planned_end_date"
                        name="planned_end_date" placeholder="" value="<?= formatDateForInput(@$phase->planned_end_date) ?>">
                    <span class="form-control-feedback text-danger" id="dateError"></span>
                </div>
            </div>

            <!-- Row 5: Actual Dates -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="actual_start_date">Actual Start Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="actual_start_date"
                        name="actual_start_date" placeholder="" value="<?= formatDateForInput(@$phase->actual_start_date) ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="actual_end_date">Actual End Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="actual_end_date"
                        name="actual_end_date" placeholder="" value="<?= formatDateForInput(@$phase->actual_end_date) ?>">
                </div>
            </div>

            <!-- Row 6: Estimated and Actual Hours -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="estimated_hours">Estimated Hours</label>
                    <input type="number" class="form-control" id="estimated_hours" name="estimated_hours"
                        step="0.25" placeholder="0.00" value="<?= @$phase->estimated_hours ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="actual_hours">Actual Hours</label>
                    <input type="number" class="form-control" id="actual_hours" name="actual_hours"
                        step="0.25" placeholder="0.00" value="<?= @$phase->actual_hours ?>" readonly>
                    <small class="form-text text-muted">Calculated from timesheets</small>
                </div>
            </div>

            <!-- Row 7: Dependencies and Completion -->
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="depends_on_phase_uuid">Depends On Phase</label>
                    <select id="depends_on_phase_uuid" name="depends_on_phase_uuid" class="form-control select2">
                        <option value="">--No Dependency--</option>
                        <?php if (isset($availablePhases)): foreach ($availablePhases as $availPhase): ?>
                            <option value="<?= $availPhase->uuid ?>" <?php if (@$phase->depends_on_phase_uuid == $availPhase->uuid) echo "selected" ?>>
                                <?= $availPhase->phase_name ?> (<?= $availPhase->phase_number ?>)
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                    <small class="form-text text-muted">This phase cannot start until the selected phase is completed</small>
                </div>

                <div class="form-group col-md-6">
                    <label for="completion_percentage">Completion Percentage: <span id="percentage_display"><?= @$phase->completion_percentage ?? 0 ?>%</span></label>
                    <input type="range" class="form-control-range" id="completion_percentage" name="completion_percentage"
                        min="0" max="100" step="5" value="<?= @$phase->completion_percentage ?? 0 ?>">
                </div>
            </div>

            <!-- Row 8: Phase Description -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="phase_description">Phase Description</label>
                    <textarea class="form-control" id="phase_description" name="phase_description" rows="3"><?= @$phase->phase_description ?></textarea>
                </div>
            </div>

            <!-- Row 9: Deliverables -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="deliverables">Deliverables</label>
                    <textarea class="form-control" id="deliverables" name="deliverables" rows="3" placeholder="List expected deliverables for this phase"><?= @$phase->deliverables ?></textarea>
                </div>
            </div>

            <!-- Row 10: Acceptance Criteria -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="acceptance_criteria">Acceptance Criteria</label>
                    <textarea class="form-control" id="acceptance_criteria" name="acceptance_criteria" rows="3" placeholder="Define criteria for phase completion"><?= @$phase->acceptance_criteria ?></textarea>
                </div>
            </div>

            <!-- Row 11: Notes -->
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2"><?= @$phase->notes ?></textarea>
                </div>
            </div>

            <input type="hidden" name="id" value="<?= @$phase->id ?>" />
            <input type="hidden" name="uuid" value="<?= @$phase->uuid ?>" />
            <input type="hidden" name="uuid_project_job_id" value="<?= $jobUuid ?>" />

            <button type="submit" id="phaseSubmit" class="btn btn-primary">Submit</button>
            <a href="/project_job_phases/index/<?= $jobUuid ?>" class="btn btn-secondary">Cancel</a>
        </form>

    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
$(document).ready(function() {
    // Initialize Select2 dropdowns
    $("#assigned_to_user_id, #assigned_to_employee_id, #status, #depends_on_phase_uuid").select2();

    // Update percentage display when slider changes
    $("#completion_percentage").on("input", function() {
        $("#percentage_display").text($(this).val() + "%");
    });

    // Form validation
    $("#phaseSubmit").click(function(event) {
        // Validate phase name
        if ($("#phase_name").val().trim() === "") {
            alert("Phase Name is required");
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

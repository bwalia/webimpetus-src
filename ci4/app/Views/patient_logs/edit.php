<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="patientLogForm" method="post" action="<?php echo "/" . $tableName . "/update"; ?>">
            <input type="hidden" value="<?= @$patient_log->uuid ?>" name="uuid" id="uuid">

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-info-circle"></i> Basic Information</h5>

                    <!-- Log Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="log_number">Log Number*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control"
                                   value="<?= @$patient_log->log_number ?: 'Auto-generated' ?>"
                                   id="log_number" name="log_number">
                        </div>
                    </div>

                    <!-- Patient -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="patient_contact_id">Patient*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="patient_contact_id" id="patient_contact_id" class="form-control required dashboard-dropdown">
                                <option value="">-- Select Patient --</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?= $patient['id'] ?>" <?= @$patient_log->patient_contact_id == $patient['id'] ? 'selected' : '' ?>>
                                        <?= $patient['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Staff -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="staff_uuid">Staff Member*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="staff_uuid" id="staff_uuid" class="form-control required dashboard-dropdown">
                                <option value="">-- Select Staff --</option>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['uuid'] ?>" <?= @$patient_log->staff_uuid == $s['uuid'] ? 'selected' : '' ?>>
                                        <?= $s['staff_number'] ?> - <?= $s['department'] ?> - <?= $s['job_title'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Log Category -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="log_category">Category*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="log_category" id="log_category" class="form-control required dashboard-dropdown">
                                <option value="General" <?= @$patient_log->log_category == 'General' ? 'selected' : '' ?>>General</option>
                                <option value="Medication" <?= @$patient_log->log_category == 'Medication' ? 'selected' : '' ?>>Medication</option>
                                <option value="Vital Signs" <?= @$patient_log->log_category == 'Vital Signs' ? 'selected' : '' ?>>Vital Signs</option>
                                <option value="Treatment/Procedure" <?= @$patient_log->log_category == 'Treatment/Procedure' ? 'selected' : '' ?>>Treatment/Procedure</option>
                                <option value="Lab Result" <?= @$patient_log->log_category == 'Lab Result' ? 'selected' : '' ?>>Lab Result</option>
                                <option value="Admission" <?= @$patient_log->log_category == 'Admission' ? 'selected' : '' ?>>Admission</option>
                                <option value="Discharge" <?= @$patient_log->log_category == 'Discharge' ? 'selected' : '' ?>>Discharge</option>
                            </select>
                        </div>
                    </div>

                    <!-- Log Type -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="log_type">Log Type</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$patient_log->log_type ?>"
                                   id="log_type" name="log_type"
                                   placeholder="e.g., Routine Check, Emergency">
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="title">Title</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$patient_log->title ?>"
                                   id="title" name="title"
                                   placeholder="Brief title">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="description">Description</label>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" rows="3"
                                      id="description" name="description"
                                      placeholder="Detailed description"><?= @$patient_log->description ?></textarea>
                        </div>
                    </div>

                    <!-- Performed DateTime -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="performed_datetime">Performed Date/Time*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="datetime-local" class="form-control required"
                                   value="<?= @$patient_log->performed_datetime ? date('Y-m-d\TH:i', strtotime($patient_log->performed_datetime)) : date('Y-m-d\TH:i') ?>"
                                   id="performed_datetime" name="performed_datetime">
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="priority">Priority</label>
                        </div>
                        <div class="col-md-8">
                            <select name="priority" id="priority" class="form-control dashboard-dropdown">
                                <option value="Normal" <?= @$patient_log->priority == 'Normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="High" <?= @$patient_log->priority == 'High' ? 'selected' : '' ?>>High</option>
                                <option value="Urgent" <?= @$patient_log->priority == 'Urgent' ? 'selected' : '' ?>>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="status">Status*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="status" id="status" class="form-control required dashboard-dropdown">
                                <option value="Draft" <?= @$patient_log->status == 'Draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="Scheduled" <?= @$patient_log->status == 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                <option value="In Progress" <?= @$patient_log->status == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= @$patient_log->status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= @$patient_log->status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Is Flagged -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="is_flagged">Flag for Review</label>
                        </div>
                        <div class="col-md-8">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" value="1"
                                       id="is_flagged" name="is_flagged"
                                       <?= @$patient_log->is_flagged ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_flagged">
                                    Flag this log for review
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Flag Reason -->
                    <div class="row form-group" id="flag_reason_row" style="display: <?= @$patient_log->is_flagged ? 'flex' : 'none' ?>;">
                        <div class="col-md-4">
                            <label for="flag_reason">Flag Reason</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$patient_log->flag_reason ?>"
                                   id="flag_reason" name="flag_reason"
                                   placeholder="Why is this flagged?">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-notes-medical"></i> Category-Specific Fields</h5>

                    <!-- Note: Show/hide based on category -->
                    <p class="text-muted">Fields will appear based on selected category</p>

                    <!-- Medication Fields (shown when category = Medication) -->
                    <div id="medication_fields" style="display: <?= @$patient_log->log_category == 'Medication' ? 'block' : 'none' ?>;">
                        <h6 class="mt-3 mb-2">Medication Details</h6>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="medication_name">Medication Name</label></div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="<?= @$patient_log->medication_name ?>"
                                       id="medication_name" name="medication_name">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="dosage">Dosage</label></div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="<?= @$patient_log->dosage ?>"
                                       id="dosage" name="dosage" placeholder="e.g., 500mg">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="route">Route</label></div>
                            <div class="col-md-8">
                                <select name="route" id="route" class="form-control dashboard-dropdown">
                                    <option value="">-- Select --</option>
                                    <option value="Oral" <?= @$patient_log->route == 'Oral' ? 'selected' : '' ?>>Oral</option>
                                    <option value="IV" <?= @$patient_log->route == 'IV' ? 'selected' : '' ?>>IV</option>
                                    <option value="IM" <?= @$patient_log->route == 'IM' ? 'selected' : '' ?>>IM</option>
                                    <option value="Subcutaneous" <?= @$patient_log->route == 'Subcutaneous' ? 'selected' : '' ?>>Subcutaneous</option>
                                    <option value="Topical" <?= @$patient_log->route == 'Topical' ? 'selected' : '' ?>>Topical</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="frequency">Frequency</label></div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="<?= @$patient_log->frequency ?>"
                                       id="frequency" name="frequency" placeholder="e.g., Twice daily">
                            </div>
                        </div>
                    </div>

                    <!-- Vital Signs Fields -->
                    <div id="vital_signs_fields" style="display: <?= @$patient_log->log_category == 'Vital Signs' ? 'block' : 'none' ?>;">
                        <h6 class="mt-3 mb-2">Vital Signs</h6>

                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="blood_pressure_systolic">BP Systolic</label>
                                <input type="number" class="form-control" value="<?= @$patient_log->blood_pressure_systolic ?>"
                                       id="blood_pressure_systolic" name="blood_pressure_systolic" placeholder="120">
                            </div>
                            <div class="col-md-6">
                                <label for="blood_pressure_diastolic">BP Diastolic</label>
                                <input type="number" class="form-control" value="<?= @$patient_log->blood_pressure_diastolic ?>"
                                       id="blood_pressure_diastolic" name="blood_pressure_diastolic" placeholder="80">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="heart_rate">Heart Rate (bpm)</label>
                                <input type="number" class="form-control" value="<?= @$patient_log->heart_rate ?>"
                                       id="heart_rate" name="heart_rate">
                            </div>
                            <div class="col-md-6">
                                <label for="temperature">Temperature (°C)</label>
                                <input type="number" step="0.1" class="form-control" value="<?= @$patient_log->temperature ?>"
                                       id="temperature" name="temperature">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="respiratory_rate">Respiratory Rate</label>
                                <input type="number" class="form-control" value="<?= @$patient_log->respiratory_rate ?>"
                                       id="respiratory_rate" name="respiratory_rate">
                            </div>
                            <div class="col-md-6">
                                <label for="oxygen_saturation">SpO2 (%)</label>
                                <input type="number" class="form-control" value="<?= @$patient_log->oxygen_saturation ?>"
                                       id="oxygen_saturation" name="oxygen_saturation">
                            </div>
                        </div>
                    </div>

                    <!-- Lab Result Fields -->
                    <div id="lab_result_fields" style="display: <?= @$patient_log->log_category == 'Lab Result' ? 'block' : 'none' ?>;">
                        <h6 class="mt-3 mb-2">Lab Result Details</h6>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="test_name">Test Name</label></div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="<?= @$patient_log->test_name ?>"
                                       id="test_name" name="test_name">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="test_result">Result</label></div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="<?= @$patient_log->test_result ?>"
                                       id="test_result" name="test_result">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="reference_range">Reference Range</label></div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="<?= @$patient_log->reference_range ?>"
                                       id="reference_range" name="reference_range">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-4"><label for="abnormal_flag">Abnormal Flag</label></div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="<?= @$patient_log->abnormal_flag ?>"
                                       id="abnormal_flag" name="abnormal_flag" placeholder="e.g., HIGH, LOW">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end">
                        <a href="/patient_logs" class="btn btn-secondary mr-2">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Patient Log
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    $(document).ready(function() {
        // Show/hide category-specific fields
        $('#log_category').on('change', function() {
            const category = $(this).val();

            $('#medication_fields').hide();
            $('#vital_signs_fields').hide();
            $('#lab_result_fields').hide();

            if (category === 'Medication') {
                $('#medication_fields').show();
            } else if (category === 'Vital Signs') {
                $('#vital_signs_fields').show();
            } else if (category === 'Lab Result') {
                $('#lab_result_fields').show();
            }
        });

        // Show/hide flag reason
        $('#is_flagged').on('change', function() {
            if ($(this).is(':checked')) {
                $('#flag_reason_row').show();
            } else {
                $('#flag_reason_row').hide();
            }
        });

        // Form validation
        $('#patientLogForm').on('submit', function(e) {
            var isValid = true;
            $('.required').each(function() {
                if ($(this).val() === '') {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    });
</script>

<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="hospitalStaffForm" method="post" action="<?php echo "/" . $tableName . "/update"; ?>">
            <input type="hidden" value="<?= @$hospital_staff->uuid ?>" name="uuid" id="uuid">

            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-user"></i> Basic Information</h5>

                    <!-- Staff Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="staff_number">Staff Number*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" readonly class="form-control"
                                   value="<?= @$hospital_staff->staff_number ?: 'Auto-generated' ?>"
                                   id="staff_number" name="staff_number">
                        </div>
                    </div>

                    <!-- User Link -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="user_id">User Account</label>
                        </div>
                        <div class="col-md-8">
                            <select name="user_id" id="user_id" class="form-control dashboard-dropdown">
                                <option value="">-- Select User --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= @$hospital_staff->user_id == $user['id'] ? 'selected' : '' ?>>
                                        <?= $user['name'] ?> (<?= $user['email'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Link to system user for login access</small>
                        </div>
                    </div>

                    <!-- Contact Link -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="contact_id">Contact</label>
                        </div>
                        <div class="col-md-8">
                            <select name="contact_id" id="contact_id" class="form-control dashboard-dropdown">
                                <option value="">-- Select Contact --</option>
                                <?php foreach ($contacts as $contact): ?>
                                    <option value="<?= $contact['id'] ?>" <?= @$hospital_staff->contact_id == $contact['id'] ? 'selected' : '' ?>>
                                        <?= $contact['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Link to contact details</small>
                        </div>
                    </div>

                    <!-- Employee Link -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="employee_id">Employee</label>
                        </div>
                        <div class="col-md-8">
                            <select name="employee_id" id="employee_id" class="form-control dashboard-dropdown">
                                <option value="">-- Select Employee --</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee['id'] ?>" <?= @$hospital_staff->employee_id == $employee['id'] ? 'selected' : '' ?>>
                                        <?= $employee['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Link to HR employee record</small>
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="department">Department*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control required"
                                   value="<?= @$hospital_staff->department ?>"
                                   id="department" name="department"
                                   placeholder="e.g., Cardiology, Emergency">
                        </div>
                    </div>

                    <!-- Job Title -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="job_title">Job Title*</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control required"
                                   value="<?= @$hospital_staff->job_title ?>"
                                   id="job_title" name="job_title"
                                   placeholder="e.g., Consultant, Staff Nurse">
                        </div>
                    </div>

                    <!-- Specialization -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="specialization">Specialization</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->specialization ?>"
                                   id="specialization" name="specialization"
                                   placeholder="e.g., Cardiothoracic Surgery">
                        </div>
                    </div>

                    <!-- Grade -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="grade">Grade</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->grade ?>"
                                   id="grade" name="grade"
                                   placeholder="e.g., Band 5, Consultant">
                        </div>
                    </div>

                    <!-- Qualification -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="qualification">Qualification</label>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" rows="2"
                                      id="qualification" name="qualification"
                                      placeholder="e.g., MBBS, BSc Nursing"><?= @$hospital_staff->qualification ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-certificate"></i> Professional Registration</h5>

                    <!-- GMC Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="gmc_number">GMC Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->gmc_number ?>"
                                   id="gmc_number" name="gmc_number"
                                   placeholder="For doctors">
                        </div>
                    </div>

                    <!-- NMC Number -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="nmc_number">NMC Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->nmc_number ?>"
                                   id="nmc_number" name="nmc_number"
                                   placeholder="For nurses">
                        </div>
                    </div>

                    <!-- Professional Registration -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="professional_registration">Registration Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->professional_registration ?>"
                                   id="professional_registration" name="professional_registration"
                                   placeholder="Other professional registration">
                        </div>
                    </div>

                    <!-- Registration Expiry -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="registration_expiry">Registration Expiry</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->registration_expiry ?>"
                                   id="registration_expiry" name="registration_expiry">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4"><i class="fa fa-briefcase"></i> Employment Details</h5>

                    <!-- Employment Type -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="employment_type">Employment Type*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="employment_type" id="employment_type" class="form-control required dashboard-dropdown">
                                <option value="Full-time" <?= @$hospital_staff->employment_type == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                <option value="Part-time" <?= @$hospital_staff->employment_type == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                <option value="Contract" <?= @$hospital_staff->employment_type == 'Contract' ? 'selected' : '' ?>>Contract</option>
                                <option value="Locum" <?= @$hospital_staff->employment_type == 'Locum' ? 'selected' : '' ?>>Locum</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contract Dates -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="contract_start_date">Contract Start</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->contract_start_date ?>"
                                   id="contract_start_date" name="contract_start_date">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="contract_end_date">Contract End</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->contract_end_date ?>"
                                   id="contract_end_date" name="contract_end_date">
                        </div>
                    </div>

                    <!-- Shift Pattern -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="shift_pattern">Shift Pattern</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->shift_pattern ?>"
                                   id="shift_pattern" name="shift_pattern"
                                   placeholder="e.g., 4 on 4 off">
                        </div>
                    </div>

                    <!-- Work Hours Per Week -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="work_hours_per_week">Hours Per Week</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" step="0.5" class="form-control"
                                   value="<?= @$hospital_staff->work_hours_per_week ?>"
                                   id="work_hours_per_week" name="work_hours_per_week"
                                   placeholder="e.g., 37.5">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-shield-alt"></i> Permissions & Access</h5>

                    <!-- Security Clearance -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="security_clearance">Security Clearance</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->security_clearance ?>"
                                   id="security_clearance" name="security_clearance">
                        </div>
                    </div>

                    <!-- Access Areas -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="access_areas">Access Areas</label>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" rows="2"
                                      id="access_areas" name="access_areas"
                                      placeholder="Authorized areas"><?= @$hospital_staff->access_areas ?></textarea>
                        </div>
                    </div>

                    <!-- Can Prescribe -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="can_prescribe">Can Prescribe</label>
                        </div>
                        <div class="col-md-8">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" value="1"
                                       id="can_prescribe" name="can_prescribe"
                                       <?= @$hospital_staff->can_prescribe ? 'checked' : '' ?>>
                                <label class="form-check-label" for="can_prescribe">
                                    Staff member can prescribe medications
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Can Authorize Procedures -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="can_authorize_procedures">Can Authorize Procedures</label>
                        </div>
                        <div class="col-md-8">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" value="1"
                                       id="can_authorize_procedures" name="can_authorize_procedures"
                                       <?= @$hospital_staff->can_authorize_procedures ? 'checked' : '' ?>>
                                <label class="form-check-label" for="can_authorize_procedures">
                                    Staff member can authorize procedures
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-graduation-cap"></i> Training & Compliance</h5>

                    <!-- Mandatory Training Status -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="mandatory_training_status">Training Status*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="mandatory_training_status" id="mandatory_training_status" class="form-control required dashboard-dropdown">
                                <option value="Up to Date" <?= @$hospital_staff->mandatory_training_status == 'Up to Date' ? 'selected' : '' ?>>Up to Date</option>
                                <option value="Due Soon" <?= @$hospital_staff->mandatory_training_status == 'Due Soon' ? 'selected' : '' ?>>Due Soon</option>
                                <option value="Overdue" <?= @$hospital_staff->mandatory_training_status == 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                            </select>
                        </div>
                    </div>

                    <!-- Last Training Date -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="last_training_date">Last Training</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->last_training_date ?>"
                                   id="last_training_date" name="last_training_date">
                        </div>
                    </div>

                    <!-- Next Training Due -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="next_training_due">Next Training Due</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->next_training_due ?>"
                                   id="next_training_due" name="next_training_due">
                        </div>
                    </div>

                    <!-- DBS Check -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="dbs_check_date">DBS Check Date</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->dbs_check_date ?>"
                                   id="dbs_check_date" name="dbs_check_date">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="dbs_check_expiry">DBS Expiry</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->dbs_check_expiry ?>"
                                   id="dbs_check_expiry" name="dbs_check_expiry">
                        </div>
                    </div>

                    <!-- Occupational Health -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="occupational_health_clearance">OH Clearance</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->occupational_health_clearance ?>"
                                   id="occupational_health_clearance" name="occupational_health_clearance"
                                   placeholder="Occupational health clearance status">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-phone"></i> Emergency Contact</h5>

                    <!-- Emergency Contact Name -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="emergency_contact_name">Contact Name</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->emergency_contact_name ?>"
                                   id="emergency_contact_name" name="emergency_contact_name">
                        </div>
                    </div>

                    <!-- Emergency Contact Phone -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="emergency_contact_phone">Contact Phone</label>
                        </div>
                        <div class="col-md-8">
                            <input type="tel" class="form-control"
                                   value="<?= @$hospital_staff->emergency_contact_phone ?>"
                                   id="emergency_contact_phone" name="emergency_contact_phone">
                        </div>
                    </div>

                    <!-- Emergency Contact Relationship -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="emergency_contact_relationship">Relationship</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->emergency_contact_relationship ?>"
                                   id="emergency_contact_relationship" name="emergency_contact_relationship"
                                   placeholder="e.g., Spouse, Parent">
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="mb-3"><i class="fa fa-info-circle"></i> Status & Notes</h5>

                    <!-- Status -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="status">Status*</label>
                        </div>
                        <div class="col-md-8">
                            <select name="status" id="status" class="form-control required dashboard-dropdown">
                                <option value="Active" <?= @$hospital_staff->status == 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="On Leave" <?= @$hospital_staff->status == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                                <option value="Inactive" <?= @$hospital_staff->status == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="Suspended" <?= @$hospital_staff->status == 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>

                    <!-- Leave Type -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="leave_type">Leave Type</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                   value="<?= @$hospital_staff->leave_type ?>"
                                   id="leave_type" name="leave_type"
                                   placeholder="e.g., Annual, Sick, Maternity">
                        </div>
                    </div>

                    <!-- Leave Dates -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="leave_start_date">Leave Start</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->leave_start_date ?>"
                                   id="leave_start_date" name="leave_start_date">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="leave_end_date">Leave End</label>
                        </div>
                        <div class="col-md-8">
                            <input type="date" class="form-control"
                                   value="<?= @$hospital_staff->leave_end_date ?>"
                                   id="leave_end_date" name="leave_end_date">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="notes">Notes</label>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" rows="3"
                                      id="notes" name="notes"
                                      placeholder="Additional notes"><?= @$hospital_staff->notes ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-end">
                        <a href="/hospital_staff" class="btn btn-secondary mr-2">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Hospital Staff
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
        $('#hospitalStaffForm').on('submit', function(e) {
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

<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="deploymentForm" method="post" action="/deployments/update">
            <input type="hidden" name="uuid" value="<?= @$deployment->uuid ?>" />
            <input type="hidden" name="uuid_business_id" value="<?= session('uuid_business') ?>" />

            <nav>
                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-general-tab" data-toggle="tab" href="#nav-general"
                        role="tab">General Information</a>
                    <a class="nav-item nav-link" id="nav-technical-tab" data-toggle="tab" href="#nav-technical"
                        role="tab">Technical Details</a>
                    <a class="nav-item nav-link" id="nav-links-tab" data-toggle="tab" href="#nav-links"
                        role="tab">Links & Approval</a>
                    <a class="nav-item nav-link" id="nav-downtime-tab" data-toggle="tab" href="#nav-downtime"
                        role="tab">Downtime & Health</a>
                </div>
            </nav>

            <div class="tab-content py-3 px-3" id="nav-tabContent">
                <!-- General Information Tab -->
                <div class="tab-pane fade show active" id="nav-general" role="tabpanel">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="deployment_name">Deployment Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="deployment_name" name="deployment_name"
                                   value="<?= @$deployment->deployment_name ?>" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="uuid_service_id">Service</label>
                            <select name="uuid_service_id" id="uuid_service_id" class="form-control select2">
                                <option value="">-- Select Service --</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?= $service['uuid'] ?>"
                                        <?= (@$deployment->uuid_service_id == $service['uuid']) ? 'selected' : '' ?>>
                                        <?= $service['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="environment">Environment <span class="text-danger">*</span></label>
                            <select name="environment" id="environment" class="form-control" required>
                                <option value="Development" <?= (@$deployment->environment == 'Development') ? 'selected' : '' ?>>Development</option>
                                <option value="Testing" <?= (@$deployment->environment == 'Testing') ? 'selected' : '' ?>>Testing</option>
                                <option value="Acceptance" <?= (@$deployment->environment == 'Acceptance') ? 'selected' : '' ?>>Acceptance</option>
                                <option value="Production" <?= (@$deployment->environment == 'Production') ? 'selected' : '' ?>>Production</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="deployment_type">Deployment Type</label>
                            <select name="deployment_type" id="deployment_type" class="form-control">
                                <option value="Initial" <?= (@$deployment->deployment_type == 'Initial') ? 'selected' : '' ?>>Initial</option>
                                <option value="Update" <?= (@$deployment->deployment_type == 'Update') ? 'selected' : '' ?>>Update</option>
                                <option value="Hotfix" <?= (@$deployment->deployment_type == 'Hotfix') ? 'selected' : '' ?>>Hotfix</option>
                                <option value="Rollback" <?= (@$deployment->deployment_type == 'Rollback') ? 'selected' : '' ?>>Rollback</option>
                                <option value="Configuration" <?= (@$deployment->deployment_type == 'Configuration') ? 'selected' : '' ?>>Configuration</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="deployment_status">Status</label>
                            <select name="deployment_status" id="deployment_status" class="form-control">
                                <option value="Planned" <?= (@$deployment->deployment_status == 'Planned') ? 'selected' : '' ?>>Planned</option>
                                <option value="In Progress" <?= (@$deployment->deployment_status == 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                                <option value="Completed" <?= (@$deployment->deployment_status == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                <option value="Failed" <?= (@$deployment->deployment_status == 'Failed') ? 'selected' : '' ?>>Failed</option>
                                <option value="Rolled Back" <?= (@$deployment->deployment_status == 'Rolled Back') ? 'selected' : '' ?>>Rolled Back</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="priority">Priority</label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="Low" <?= (@$deployment->priority == 'Low') ? 'selected' : '' ?>>Low</option>
                                <option value="Medium" <?= (@$deployment->priority == 'Medium') ? 'selected' : '' ?>>Medium</option>
                                <option value="High" <?= (@$deployment->priority == 'High') ? 'selected' : '' ?>>High</option>
                                <option value="Critical" <?= (@$deployment->priority == 'Critical') ? 'selected' : '' ?>>Critical</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="version">Version</label>
                            <input type="text" class="form-control" id="version" name="version"
                                   value="<?= @$deployment->version ?>" placeholder="e.g., 2.1.0">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="deployment_date">Deployment Date</label>
                            <input type="datetime-local" class="form-control" id="deployment_date" name="deployment_date"
                                   value="<?= @$deployment->deployment_date ? date('Y-m-d\TH:i', strtotime($deployment->deployment_date)) : '' ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="completed_date">Completed Date</label>
                            <input type="datetime-local" class="form-control" id="completed_date" name="completed_date"
                                   value="<?= @$deployment->completed_date ? date('Y-m-d\TH:i', strtotime($deployment->completed_date)) : '' ?>">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="deployed_by">Deployed By</label>
                            <select name="deployed_by" id="deployed_by" class="form-control select2">
                                <option value="">-- Select User --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['uuid'] ?>"
                                        <?= (@$deployment->deployed_by == $user['uuid']) ? 'selected' : '' ?>>
                                        <?= $user['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= @$deployment->description ?></textarea>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="affected_components">Affected Components</label>
                            <textarea class="form-control" id="affected_components" name="affected_components" rows="2"
                                      placeholder="List of components, services, or systems affected by this deployment"><?= @$deployment->affected_components ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Technical Details Tab -->
                <div class="tab-pane fade" id="nav-technical" role="tabpanel">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="git_branch">Git Branch</label>
                            <input type="text" class="form-control" id="git_branch" name="git_branch"
                                   value="<?= @$deployment->git_branch ?>" placeholder="e.g., main, develop">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="git_commit_hash">Git Commit Hash</label>
                            <input type="text" class="form-control" id="git_commit_hash" name="git_commit_hash"
                                   value="<?= @$deployment->git_commit_hash ?>" placeholder="e.g., a1b2c3d4...">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="repository_url">Repository URL</label>
                            <input type="url" class="form-control" id="repository_url" name="repository_url"
                                   value="<?= @$deployment->repository_url ?>" placeholder="https://github.com/...">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="deployment_url">Deployment URL</label>
                            <input type="url" class="form-control" id="deployment_url" name="deployment_url"
                                   value="<?= @$deployment->deployment_url ?>" placeholder="https://app.example.com">
                        </div>

                        <div class="form-group col-md-12">
                            <label for="deployment_notes">Deployment Notes</label>
                            <textarea class="form-control" id="deployment_notes" name="deployment_notes" rows="5"
                                      placeholder="Technical notes, commands, scripts, configuration changes..."><?= @$deployment->deployment_notes ?></textarea>
                        </div>

                        <div class="form-group col-md-12">
                            <label for="rollback_plan">Rollback Plan</label>
                            <textarea class="form-control" id="rollback_plan" name="rollback_plan" rows="5"
                                      placeholder="Step-by-step rollback procedure if deployment fails..."><?= @$deployment->rollback_plan ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Links & Approval Tab -->
                <div class="tab-pane fade" id="nav-links" role="tabpanel">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="uuid_task_id">Related Task</label>
                            <select name="uuid_task_id" id="uuid_task_id" class="form-control select2">
                                <option value="">-- Select Task --</option>
                                <?php foreach ($tasks as $task): ?>
                                    <option value="<?= $task['uuid'] ?>"
                                        <?= (@$deployment->uuid_task_id == $task['uuid']) ? 'selected' : '' ?>>
                                        <?= $task['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="uuid_incident_id">Related Incident</label>
                            <select name="uuid_incident_id" id="uuid_incident_id" class="form-control select2">
                                <option value="">-- Select Incident --</option>
                                <?php foreach ($incidents as $incident): ?>
                                    <option value="<?= $incident['uuid'] ?>"
                                        <?= (@$deployment->uuid_incident_id == $incident['uuid']) ? 'selected' : '' ?>>
                                        <?= $incident['title'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="approval_required"
                                       name="approval_required" value="1" <?= (@$deployment->approval_required) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="approval_required">
                                    Approval Required
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="approved_by">Approved By</label>
                            <select name="approved_by" id="approved_by" class="form-control select2">
                                <option value="">-- Select Approver --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['uuid'] ?>"
                                        <?= (@$deployment->approved_by == $user['uuid']) ? 'selected' : '' ?>>
                                        <?= $user['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="approval_date">Approval Date</label>
                            <input type="datetime-local" class="form-control" id="approval_date" name="approval_date"
                                   value="<?= @$deployment->approval_date ? date('Y-m-d\TH:i', strtotime($deployment->approval_date)) : '' ?>">
                        </div>
                    </div>
                </div>

                <!-- Downtime & Health Tab -->
                <div class="tab-pane fade" id="nav-downtime" role="tabpanel">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="downtime_required"
                                       name="downtime_required" value="1" <?= (@$deployment->downtime_required) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="downtime_required">
                                    Downtime Required
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="downtime_start">Downtime Start</label>
                            <input type="datetime-local" class="form-control" id="downtime_start" name="downtime_start"
                                   value="<?= @$deployment->downtime_start ? date('Y-m-d\TH:i', strtotime($deployment->downtime_start)) : '' ?>">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="downtime_end">Downtime End</label>
                            <input type="datetime-local" class="form-control" id="downtime_end" name="downtime_end"
                                   value="<?= @$deployment->downtime_end ? date('Y-m-d\TH:i', strtotime($deployment->downtime_end)) : '' ?>">
                        </div>

                        <div class="form-group col-md-8">
                            <label for="health_check_url">Health Check URL</label>
                            <input type="url" class="form-control" id="health_check_url" name="health_check_url"
                                   value="<?= @$deployment->health_check_url ?>" placeholder="https://app.example.com/health">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="health_check_status">Health Status</label>
                            <select name="health_check_status" id="health_check_status" class="form-control">
                                <option value="Unknown" <?= (@$deployment->health_check_status == 'Unknown') ? 'selected' : '' ?>>Unknown</option>
                                <option value="Healthy" <?= (@$deployment->health_check_status == 'Healthy') ? 'selected' : '' ?>>Healthy</option>
                                <option value="Degraded" <?= (@$deployment->health_check_status == 'Degraded') ? 'selected' : '' ?>>Degraded</option>
                                <option value="Unhealthy" <?= (@$deployment->health_check_status == 'Unhealthy') ? 'selected' : '' ?>>Unhealthy</option>
                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="status"
                                       name="status" value="1" <?= (!isset($deployment->status) || @$deployment->status) ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="status">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Deployment
            </button>
            <a href="/deployments" class="btn btn-secondary">
                <i class="fa fa-times"></i> Cancel
            </a>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
    $(document).ready(function() {
        // Initialize Select2 for dropdowns
        $('.select2').select2({
            placeholder: '-- Select --',
            allowClear: true
        });
    });
</script>

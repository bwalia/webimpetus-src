<?php require_once(APPPATH . 'Views/common/header.php'); ?>
<?php require_once(APPPATH . 'Views/common/sidebar.php'); ?>

<section class="main_content dashboard_part large_header_bg">
    <?php require_once(APPPATH . 'Views/common/top-header.php'); ?>

    <div class="main_content_iner">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="white_card card_height_100 mb_30">
                        <div class="white_card_header">
                            <div class="box_header m-0">
                                <div class="main-title">
                                    <h3 class="m-0"><i class="fa fa-shield"></i> Deployment Permissions Management</h3>
                                </div>
                            </div>
                        </div>
                        <div class="white_card_body">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Deployment Permissions</strong> control which users can deploy to Production and Acceptance environments.
                                        Users without permissions can use a passcode to deploy.
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPermissionModal">
                                        <i class="fa fa-plus"></i> Grant Permission
                                    </button>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#generatePasscodeModal">
                                        <i class="fa fa-key"></i> Generate Passcode
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Current Permissions</h4>
                                    <table class="table table-bordered table-hover" id="permissionsTable">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Environment</th>
                                                <th>Can Deploy</th>
                                                <th>Granted By</th>
                                                <th>Granted Date</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($permissions)): ?>
                                                <?php foreach ($permissions as $perm): ?>
                                                    <tr>
                                                        <td><?= $perm['uuid_user_id'] ?></td>
                                                        <td><span class="badge badge-primary"><?= $perm['environment'] ?></span></td>
                                                        <td>
                                                            <?php if ($perm['can_deploy']): ?>
                                                                <span class="badge badge-success">Yes</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-danger">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $perm['granted_by'] ?? '-' ?></td>
                                                        <td><?= $perm['granted_date'] ? date('Y-m-d H:i', strtotime($perm['granted_date'])) : '-' ?></td>
                                                        <td>
                                                            <?php if ($perm['status']): ?>
                                                                <span class="badge badge-success">Active</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">Inactive</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-danger revoke-permission" data-id="<?= $perm['id'] ?>">
                                                                <i class="fa fa-trash"></i> Revoke
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No permissions configured yet.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-shield"></i> Grant Deployment Permission</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="permissionForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="uuid_user_id">User <span class="text-danger">*</span></label>
                        <select name="uuid_user_id" id="uuid_user_id" class="form-control" required>
                            <option value="">-- Select User --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['uuid'] ?>"><?= $user['name'] ?> (<?= $user['email'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="environment">Environment <span class="text-danger">*</span></label>
                        <select name="environment" id="environment" class="form-control" required>
                            <option value="Development">Development</option>
                            <option value="Testing">Testing</option>
                            <option value="Acceptance">Acceptance</option>
                            <option value="Production">Production</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Reason for granting permission..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Grant Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Generate Passcode Modal -->
<div class="modal fade" id="generatePasscodeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-key"></i> Generate Deployment Passcode</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="passcodeForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Note:</strong> The passcode will only be shown once. Make sure to copy it securely.
                    </div>

                    <div class="form-group">
                        <label for="pc_deployment_uuid">Deployment (Optional)</label>
                        <input type="text" name="deployment_uuid" id="pc_deployment_uuid" class="form-control"
                               placeholder="Leave empty for environment-wide passcode">
                    </div>

                    <div class="form-group">
                        <label for="pc_environment">Environment <span class="text-danger">*</span></label>
                        <select name="environment" id="pc_environment" class="form-control" required>
                            <option value="Development">Development</option>
                            <option value="Testing">Testing</option>
                            <option value="Acceptance">Acceptance</option>
                            <option value="Production">Production</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="expires_in">Expires In (hours) <span class="text-danger">*</span></label>
                        <select name="expires_in" id="expires_in" class="form-control" required>
                            <option value="1">1 hour</option>
                            <option value="6">6 hours</option>
                            <option value="12">12 hours</option>
                            <option value="24" selected>24 hours</option>
                            <option value="48">48 hours</option>
                            <option value="168">7 days</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="max_uses">Maximum Uses <span class="text-danger">*</span></label>
                        <input type="number" name="max_uses" id="max_uses" class="form-control" value="1" min="1" max="100" required>
                    </div>

                    <div id="generatedPasscodeDisplay" class="alert alert-success" style="display: none;">
                        <h4>Your Passcode:</h4>
                        <div style="font-size: 2rem; font-weight: bold; letter-spacing: 0.5rem; text-align: center; font-family: monospace;">
                            <span id="displayedPasscode"></span>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="copyPasscode()">
                            <i class="fa fa-copy"></i> Copy Passcode
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="generateBtn">Generate Passcode</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<script>
$(document).ready(function() {
    // Handle permission form submission
    $('#permissionForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '/deployments/savePermission',
            method: 'POST',
            data: $(this).serialize(),
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
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save permission',
                    confirmButtonColor: '#dc2626'
                });
            }
        });
    });

    // Handle passcode generation
    $('#passcodeForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '/deployments/generatePasscode',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    $('#displayedPasscode').text(response.passcode);
                    $('#generatedPasscodeDisplay').show();
                    $('#generateBtn').prop('disabled', true);

                    Swal.fire({
                        icon: 'success',
                        title: 'Passcode Generated',
                        html: `
                            <p><strong>Passcode:</strong> <span style="font-size: 1.5rem; font-family: monospace; letter-spacing: 0.3rem;">${response.passcode}</span></p>
                            <p><strong>Expires:</strong> ${response.expires_at}</p>
                            <p><strong>Max Uses:</strong> ${response.max_uses}</p>
                            <p class="text-danger mt-3"><small>This passcode will only be shown once. Make sure to copy it now.</small></p>
                        `,
                        confirmButtonColor: '#10b981'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#dc2626'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to generate passcode',
                    confirmButtonColor: '#dc2626'
                });
            }
        });
    });

    // Reset passcode modal when closed
    $('#generatePasscodeModal').on('hidden.bs.modal', function() {
        $('#passcodeForm')[0].reset();
        $('#generatedPasscodeDisplay').hide();
        $('#generateBtn').prop('disabled', false);
    });

    // Handle permission revocation
    $(document).on('click', '.revoke-permission', function() {
        const permissionId = $(this).data('id');

        Swal.fire({
            title: 'Revoke Permission?',
            text: 'Are you sure you want to revoke this deployment permission?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Revoke',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/deployments/savePermission',
                    method: 'POST',
                    data: { id: permissionId, status: 0 },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Revoked',
                                text: 'Permission has been revoked',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    }
                });
            }
        });
    });
});

function copyPasscode() {
    const passcode = $('#displayedPasscode').text();
    navigator.clipboard.writeText(passcode).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Passcode copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });
    });
}
</script>

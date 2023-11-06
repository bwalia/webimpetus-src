<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">
        <form method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="role_name">Role Name</label>
                        </div>
                        <div class="col-md-6">
                            <input type="input" maxlength="24" autocomplete="off" class="form-control required"
                                name="role_name" value="<?= @$roleData->role_name ?>">
                        </div>
                    </div>
                    <input type="hidden" name="uuid" value="<?php echo @$roleData->uuid; ?>" />
                    <?php 
                        $permissionsUUID = [];
                        foreach ($selectedPermissions as $key => $selectedPermission) {
                            array_push($permissionsUUID, $selectedPermission['permission_id']);
                        }
                    ?>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-group d-flex">
                                <label for="inputState" class="mr-5">Permissions</label>
                                <select id="user_permissions" name="user_permissions[]" multiple
                                    class="form-control select2">
                                    <?php
                                    foreach ($permissions as $row): ?>
                                        <option value="<?= $row['uuid']; ?>" <?php
                                          if (is_array($permissionsUUID) && in_array($row['uuid'], $permissionsUUID)) {
                                              echo "selected";
                                          } ?>><?= $row['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->
<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>

<!-- Action Buttons -->
<div class="white_card_body">
    <div class="d-flex justify-content-end mb-3" style="padding-bottom: 0;">
        <button type="button" onclick="window.location.reload();" class="btn btn-primary mr-2">
            <i class="fa fa-refresh"></i> Refresh
        </button>
        <a href="/roles/edit" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Role
        </a>
    </div>
</div>

<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Role Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($user_roles) && $user_roles) { ?>
                    <?php foreach ($user_roles as $row) : ?>
                        <tr data-link=<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>>
                            <td class="f_s_12 f_w_400"><?= $row['id']; ?></td>
                            <td class="f_s_12 f_w_400"><?= $row['role_name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
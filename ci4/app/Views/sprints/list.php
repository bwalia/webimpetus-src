<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Sprint Name</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Note</th>
                    <th scope="col">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sprints as $row) : ?>
                    <tr data-link=<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>>
                        <td class="f_s_12 f_w_400"><?= $row['id']; ?></td>
                        <td class="f_s_12 f_w_400"><?= $row['sprint_name']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= render_date(strtotime($row['start_date'])); ?></td>
                        <td class="f_s_12 f_w_400  "><?= render_date(strtotime($row['end_date'])); ?></td>
                        <td class="f_s_12 f_w_400"><?= $row['note']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= render_date(strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Tax Code</th>
                    <th scope="col">Tax Rate</th>
                    <th scope="col">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($taxes as $row) : ?>
                    <tr data-link=<?= "/" . $tableName . "/edit/" . $row['id']; ?>>
                        <td class="f_s_12 f_w_400"><?= $row['id']; ?></td>
                        <td class="f_s_12 f_w_400"><?= $row['tax_code']; ?></td>
                        <td class="f_s_12 f_w_400"><?= $row['tax_rate']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= render_date(strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
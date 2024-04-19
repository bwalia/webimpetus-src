<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table table-responsive">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Sprint Name</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Note</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Action</th>
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
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/sprints/deleterow/<?= $row['uuid']; ?>"> <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="/sprints/edit/<?= $row['uuid']; ?>"> <i class="fas fa-edit"></i> Edit</a>

                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
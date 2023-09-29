<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>

                    <th scope="col">Id</th>
                    <th scope="col">Client</th>
                    <th scope="col">Project</th>
                    <th scope="col">Budget</th>
                    <th scope="col">Rate</th>
                    <th scope="col">Currency</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">Active/Completed</th>

                    <th scope="col">Progress XY%</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($projects as $row) : ?>
                    <tr data-link=<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>>

                        <td class="f_s_12 f_w_400"><?= $row['id']; ?>
                        </td>
                        <td class="f_s_12 f_w_400"><?= $row['company_name']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['name']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['budget']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= $row['rate']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= getCurrency($row['currency']); ?></td>
                        <td class="f_s_12 f_w_400  "><?= render_date($row['start_date']); ?></td>
                        <td class="f_s_12 f_w_400  "><?= getStatus($row['active']); ?></td>

                        <td class="f_s_12 f_w_400  ">
                            <p class="pd10">
                                <?php
                                if (array_key_exists($row['id'], $task_progress)) {
                                    echo number_format($task_progress[$row['id']], 2) . " %";
                                } else {
                                    echo "";
                                }
                                ?>
                            </p>
                        </td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href=<?= "/" . $tableName . "/delete/" . $row['id']; ?>>
                                            <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>"> <i class="fas fa-edit"></i> Edit</a>


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
<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Code</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Is Default?</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $row) : ?>
                    <tr data-link=<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>>

                        <td class="f_s_12 f_w_400"><?= $row['id']; ?>
                        </td>
                        <td class="f_s_12 f_w_400"><?= $row['code']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['subject']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['is_default'] ? 'Yes' : 'No'; ?>
                        </td>

                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href=<?= "/" . $tableName . "/deleterow/" . $row['uuid']; ?> <i class="ti-trash"></i> Delete</a>
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
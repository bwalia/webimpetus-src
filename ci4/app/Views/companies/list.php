<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example123"
            class="table table-listing-items table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Company Name</th>
                    <th scope="col">Company Number</th>
                    <th scope="col">Type</th>
                    <th scope="col">SIC</th>
                    <th scope="col">Status</th>
                    <th scope="col">Email</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $row): ?>
                    <tr data-link="companies/edit/<?= $row['uuid']; ?>">
                        <td class="f_s_12 f_w_400"><?= $row['id']; ?>
                        </td>
                        <td class="f_s_12 f_w_400"><?= $row['company_name']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['company_number']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['company_type']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['sic_code']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  ">
                            <?php if ($row['status'] == 1) {
                                echo "Active";
                            } else {
                                echo "Inactive";
                            } ?>
                        </td>
                        <td class="f_s_12 f_w_400  ">
                            <p class="pd10"> <?= $row['email']; ?></p>
                        </td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');"
                                            href="/companies/deleterow/<?= $row['uuid']; ?>"> <i class="ti-trash"></i>
                                            Delete</a>
                                        <a class="dropdown-item" href="/companies/edit/<?= $row['uuid']; ?>"> <i
                                                class="fas fa-edit"></i> Edit</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $pager->links(); ?>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/scripts.php'); ?>
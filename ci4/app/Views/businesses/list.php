<?php 
    require_once(APPPATH . 'Views/common/list-title.php'); 
    $roles = getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false);
?>

<div class="white_card_body ">
    <div class="QA_table ">
        <!-- table-responsive -->
        <table id="example" cellpadding="5" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Created at</th>
                    <?php if ((!empty($_SESSION['role']) && $roles['role_name'] == "Administrator") || session('uuid') == 1) { ?><th scope="col" width="50">Action</th><?php } ?>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($businesses as $row) :
                    if ((isset($_SESSION['role']) && $roles['role_name'] == "Administrator") || session('uuid') == 1) {
                        $url = "data-link=/" . $tableName . "/edit/" . $row['uuid'];
                    } ?>
                    <tr <?= @$url; ?>>
                        <td class="f_s_12 f_w_100"> <?= $row['id']; ?> </td>
                        <td class="f_s_12 f_w_200"><?= $row['name']; ?></td>
                        <td class="f_s_12 f_w_100"><?= $row['created_at']; ?></td>
                        <?php if (!empty($_SESSION['role'])) { ?> <td class="f_s_12 f_w_400 text-right">
                                <div class="header_more_tool">
                                    <div class="dropdown">
                                        <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                            <i class="ti-more-alt"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href=<?= "/" . $tableName . "/deleterow/" . $row['uuid']; ?>> <i class="ti-trash"></i> Delete</a>
                                            <a class="dropdown-item" href=<?= "/" . $tableName . "/edit/" . $row['uuid']; ?>> <i class="fas fa-edit"></i> Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        <?php } ?>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
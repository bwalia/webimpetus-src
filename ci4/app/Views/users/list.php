<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table ">
        <input type="text" id="searchInput" placeholder="Search for names.." onkeyup="updateURL(this.value)"
            value="<?php echo $_GET['query'] ?? "" ?>">
        <button class="btn btn-primary" onclick="window.location.reload()">Search</button>
        <button class="btn btn-primary" onclick="resetSearch()">Reset</button>
        <!-- table-responsive -->
        <table id="userTable" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <!--th scope="col">
                        <input type="checkbox" class="check_all" onclick="set_check_all(this);">
                    </th-->
                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Address</th>

                    <th scope="col">Note</th>
                    <th scope="col">Status</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $row): ?>
                    <tr data-link="/users/edit/<?= $row['uuid']; ?>">
                        <!--td class="checkDocument">
                    <input type="checkbox" class="check_all" onclick="set_check_all(this);">
                </td-->
                        <td class="f_s_12 f_w_400"><?= $row['id']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= $row['name']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= $row['email']; ?></td>
                        <td class="f_s_12 f_w_400  "><?= $row['address']; ?></td>

                        <td class="f_s_12 f_w_400  ">
                            <p class="pd10"> <?= $row['notes']; ?></p>
                        </td>
                        <td class="f_s_12 f_w_400  ">
                            <div class="">
                                <label class="switch2">
                                    <input type="checkbox" class="checkb" data-url="users/status" name="checkb[]"
                                        value="<?= $row['id']; ?>" <?= ($row['status'] == 1) ? 'checked' : '' ?> />
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');"
                                            href="/users/deleterow/<?= $row['uuid']; ?>"> <i class="ti-trash"></i>
                                            Delete</a>
                                        <a class="dropdown-item" href="/users/edit/<?= $row['uuid']; ?>"> <i
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

<?php require_once (APPPATH . 'Views/users/footer.php'); ?>

<script>
    $('.table-listing-items  tr  td').on('click', function (e) {


        var dataClickable = $(this).parent().attr('data-link');
        if ($(this).is(':last-child') || $(this).is(':nth-last-child(2)')) {

        } else {
            if (dataClickable && dataClickable.length > 0) {

                window.location = dataClickable;
            }
        }

    });

    function updateURL(searchQuery) {
        var currentURL = window.location.href;
        var updatedURL = currentURL.split('?')[0];

        if (searchQuery.trim() !== "") {
            updatedURL += "?query=" + encodeURIComponent(searchQuery);
        }

        history.replaceState(null, null, updatedURL);
    }

    function resetSearch() {
        history.replaceState(null, null, "/users");
        window.location.reload();
    }
</script>
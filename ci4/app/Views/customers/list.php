<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table ">
        <input type="text" id="searchInput" placeholder="Search for names.." onkeyup="updateURL(this.value)">
        <button class="btn btn-primary" onclick="window.location.reload()">Search</button>
        <button class="btn btn-primary" onclick="resetSearch()">Reset</button>
        <!-- table-responsive -->
        <table id="customersTable" class="table table-listing-items table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Customer Name</th>
                    <th scope="col">Account Number</th>
                    <th scope="col">Status</th>
                    <th scope="col">Email</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $row) : ?>
                    <tr data-link="customers/edit/<?= $row['uuid']; ?>">
                        <td class="f_s_12 f_w_400"><?= $row['id']; ?>
                        </td>
                        <td class="f_s_12 f_w_400"><?= $row['company_name']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['acc_no']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  ">
                            <?php if ($row['status'] == 1) {
                                echo "Active";
                            } else {
                                echo  "Inactive";
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
                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/customers/deleterow/<?= $row['uuid']; ?>"> <i class="ti-trash"></i> Delete</a>
                                        <a class="dropdown-item" href="/customers/edit/<?= $row['uuid']; ?>"> <i class="fas fa-edit"></i> Edit</a>
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

<?php require_once(APPPATH . 'Views/common/scripts.php'); ?>

<script>
function updateURL(searchQuery) {
    // Get the current URL
    var currentURL = window.location.href;

    // Remove existing search query parameter, if any
    var updatedURL = currentURL.split('?')[0];

    // If search query is not empty, add it to the URL
    if (searchQuery.trim() !== "") {
        updatedURL += "?query=" + encodeURIComponent(searchQuery);
    }

    // Replace the current URL with the updated one
    history.replaceState(null, null, updatedURL);
}

function resetSearch() {
    history.replaceState(null, null, "/customers");
    window.location.reload();
}
</script>
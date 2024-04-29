<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- start section for body -->
<div class="white_card_body ">
    <div class="QA_table ">
        <input type="text" id="searchInput" placeholder="Search for names.." onkeyup="updateURL(this.value)"
            value="<?php echo $_GET['query'] ?? "" ?>">
        <button class="btn btn-primary" onclick="window.location.reload()">Search</button>
        <button class="btn btn-primary" onclick="resetSearch()">Reset</button>
        <!-- table-responsive -->
        <table id="catTble" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>

                    <th scope="col">Id</th>
                    <th scope="col">Name</th>
                    <th scope="col">Category Image</th>
                    <th scope="col">Note</th>
                    <th scope="col" width="50">Action</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($categories as $row): ?>
                    <tr data-link="categories/editrow/<?= $row['uuid']; ?>">

                        <td class="f_s_12 f_w_400"><?= $row['id']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  "><?= $row['name']; ?>
                        </td>
                        <td class="f_s_12 f_w_400  ">
                            <?php if (!empty($row['image_logo'])) {
                                echo render_image($row['image_logo']);
                            } ?>

                        </td>
                        <td class="f_s_12 f_w_400  ">
                            <p class="pd10"> <?= $row['notes']; ?></p>
                        </td>
                        <td class="f_s_12 f_w_400 text-right">
                            <div class="header_more_tool">
                                <div class="dropdown">
                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                        <i class="ti-more-alt"></i>
                                    </span>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');"
                                            href="/categories/deleterow/<?= $row['uuid']; ?>"> <i class="ti-trash"></i>
                                            Delete</a>
                                        <a class="dropdown-item" href="/categories/editrow/<?= $row['uuid']; ?>"> <i
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
<!-- end section for body -->
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
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
        history.replaceState(null, null, "/companies");
        window.location.reload();
    }
</script>
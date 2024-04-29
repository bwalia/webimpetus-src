<?php require_once (APPPATH . 'Views/tasks/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <input type="text" id="searchInput" placeholder="Search for names.." onkeyup="updateURL(this.value)"
            value="<?php echo $_GET['query'] ?? "" ?>">
        <button class="btn btn-primary" onclick="window.location.reload()">Search</button>
        <button class="btn btn-primary" onclick="resetSearch()">Reset</button>
        <!-- table-responsive -->
        <div class="table-responsive py-2">
            <table id="taskTable" class="table table-listing-items tableDocument table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Task Id</th>
                        <th scope="col">Task Title</th>
                        <th scope="col">Project</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Est. Hours</th>
                        <th scope="col">Rate</th>
                        <th scope="col">Total Timespent</th>
                        <th scope="col">Active</th>
                        <th scope="col">Status</th>
                        <th scope="col" width="50">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($tasks as $row): ?>
                        <tr data-link=<?= "/" . $tableName . "/editrow/" . $row['uuid']; ?>>

                            <td class="f_s_12 f_w_400"><?= $row['id']; ?>
                            </td>
                            <td class="f_s_12 f_w_400"><?= $row['name']; ?>
                            </td>
                            <td class="f_s_12 f_w_400  "><?= $row['project_name']; ?>
                            </td>
                            <td class="f_s_12 f_w_400  "><?= render_date($row['start_date']); ?></td>
                            <td class="f_s_12 f_w_400  "><?= render_date($row['end_date']); ?></td>
                            <td class="f_s_12 f_w_400  "><?= $row['estimated_hour']; ?></td>
                            <td class="f_s_12 f_w_400  "><?= $row['rate']; ?></td>
                            <td class="f_s_12 f_w_400  "><?= 1; ?></td>
                            <td class="f_s_12 f_w_400  "><?= getStatus($row['active']); ?></td>
                            <td class="f_s_12 f_w_400  "><?= @ucfirst($row['status']); ?></td>
                            <td class="f_s_12 f_w_400 text-right">
                                <div class="header_more_tool">
                                    <div class="dropdown">
                                        <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                            <i class="ti-more-alt"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                                            <a class="dropdown-item"
                                                onclick="return confirm('Are you sure want to delete?');" href=<?= "/" . $tableName . "/deleterow/" . $row['uuid']; ?>>
                                                <i class="ti-trash"></i> Delete</a>
                                            <a class="dropdown-item"
                                                href="<?= "/" . $tableName . "/editrow/" . $row['uuid']; ?>"> <i
                                                    class="fas fa-edit"></i> Edit</a>
                                            <a class="dropdown-item"
                                                href="<?= "/" . $tableName . "/clone/" . $row['uuid']; ?>"> <i
                                                    class="fas fa-copy"></i> Clone</a>
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>


                </tbody>
            </table>
            <?php echo $pager; ?>
        </div>
    </div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    var base_url = '<?php echo base_url('/tasks') ?>';
    $(document).ready(function () {
        $("#task_status").on("change", function (e) {
            var redirect_to = base_url;
            if ($(this).val() != "") {
                redirect_to = base_url + "?status=" + $(this).val();
            }
            window.location.replace(redirect_to);
        });
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
        history.replaceState(null, null, "/tasks");
        window.location.reload();
    }
</script>
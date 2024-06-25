<?php
require_once (APPPATH . 'Views/common/list-title.php');
$roles = getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false);

?>

<div class="white_card_body">
    <div class="QA_table businessTable" id="businessTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Name', 'Created at'];
    let columnsMachineName = ['id', 'name', 'created_at'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "businesses",
            apiPath: "api/v2/businesses",
            selector: "businessTable"
        }
    );
</script>
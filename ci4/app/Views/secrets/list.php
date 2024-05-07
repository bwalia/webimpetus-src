<?php
require_once (APPPATH . 'Views/common/list-title.php');
$roles = getResultWithoutBusiness("roles", ["uuid" => $_SESSION['role']], false);
?>

<div class="white_card_body ">
    <div class="QA_table" id="secretsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Key name', 'Services', 'Created at'];
    let columnsMachineName = ['id', 'key_name', 'name', 'created'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "secrets",
            apiPath: "api/v2/secrets",
            selector: "secretsTable"
        }
    );
</script>
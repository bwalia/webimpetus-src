<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<div class="white_card_body ">
    <div class="QA_table" id="domainsTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Service', 'Name', 'Notes'];
    let columnsMachineName = ['id', 'sname', 'name', 'notes'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "domains",
            apiPath: "domains/domainList",
            selector: "domainsTable"
        }
    );
</script>
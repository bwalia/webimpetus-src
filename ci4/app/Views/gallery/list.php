 <?php require_once (APPPATH.'Views/common/list-title.php'); ?>
 <div class="white_card_body ">
    <div class="QA_table gallaryTable" id="gallaryTable"></div>
</div>
<?php require_once (APPPATH.'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Code', 'Status', 'Created at'];
    let columnsMachineName = ['id', 'code', 'status', 'created'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "gallary",
            apiPath: "gallary/enquiriesList",
            selector: "gallaryTable"
        }
    );
</script>
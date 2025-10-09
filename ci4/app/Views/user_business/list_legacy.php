<?php require_once(APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table userBusinessTable" id="userBusinessTable"></div>
</div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'User', 'Primary Business'];
    let columnsMachineName = ['id', 'username', 'business_name'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "user_business",
            apiPath: "user_business/userBusinessList",
            selector: "userBusinessTable"
        }
    );
</script>
<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="contactsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'First Name', 'Email', 'Mobile', 'Web Access'];
    let columnsMachineName = ['id', 'first_name', 'email', 'mobile', 'allow_web_access'];
    initializeGridTable(
        {
            columnsTitle, 
            columnsMachineName, 
            tableName: "contacts", 
            apiPath: "contacts/contactsList", 
            selector: "contactsTable"
        }
    );
</script>
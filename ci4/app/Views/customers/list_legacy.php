<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table" id="customersTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/scripts.php'); ?>

<script>
    let columnsTitle = ['Id', 'Customer Name', 'Account Number', 'Status', 'Email'];
    let columnsMachineName = ['id', 'company_name', 'acc_no', 'status', 'email'];
    initializeGridTable({
        columnsTitle, 
        columnsMachineName, 
        tableName: "customers", 
        apiPath: "customers/customersList", 
        selector: "customersTable"
    });
</script>
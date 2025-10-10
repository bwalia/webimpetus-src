<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="employeesTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'First Name', 'Surname', 'Email', 'Mobile', 'Web Access'];
    let columnsMachineName = ['id', 'first_name', 'surname', 'email', 'mobile', 'allow_web_access'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "employees",
            apiPath: "employees/employeesList",
            selector: "employeesTable"
        }
    );
</script>
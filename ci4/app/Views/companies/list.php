<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table companiesTable" id="companiesTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/scripts.php'); ?>
<script>
    let columnsTitle = ['Id', 'Company Name', 'Company Number', 'Status', 'Type', 'SIC', 'Email'];
    let columnsMachineName = ['id', 'company_name', 'company_number', 'status', 'company_type', 'sic_code', 'email'];
    initializeGridTable({columnsTitle, columnsMachineName, tableName: "companies", apiPath: "companies/companiesList", selector: "companiesTable"});
</script>
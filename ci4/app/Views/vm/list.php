<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="vmTable"></div>
</div>
<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'VM Name', 'VM Code', 'VM Ram', 'VM Cpu Cores', 'Status'];
    let columnsMachineName = ['id', 'vm_name', 'vm_code', 'vm_ram_display', 'vm_cpu_cores', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "vm",
            apiPath: "api/v2/vm",
            selector: "vmTable"
        }
    );
</script>
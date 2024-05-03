<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table" id="menuTable"></div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Menu Name', 'Link', 'Icon'];
    let columnsMachineName = ['id', 'name', 'link', 'icon'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "menu",
            apiPath: "menu/menusList",
            selector: "menuTable"
        }
    );
</script>
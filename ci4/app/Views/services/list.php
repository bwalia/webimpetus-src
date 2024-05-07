<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>

<div class="white_card_body ">
    <div class="QA_table" id="servicesTable"></div>
</div>

<?php require_once (APPPATH . 'Views/services/footer.php'); ?>
<script>
    let columnsTitle = ['Id', 'Name', 'Category', 'Code', 'Note', 'Status'];
    let columnsMachineName = ['id', 'name', 'category', 'code', 'note', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "services",
            apiPath: "api/v2/services",
            selector: "servicesTable"
        }
    );

    $('.table-listing-items  tr  td').on('click', function (e) {
        var dataClickable = $(this).parent().attr('data-link');
        if ($(this).is(':last-child') || $(this).is(':nth-last-child(2)')) {

        } else {
            if (dataClickable && dataClickable.length > 0) {

                window.location = dataClickable;
            }
        }

    });
</script>
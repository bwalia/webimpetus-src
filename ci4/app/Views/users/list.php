<?php require_once (APPPATH . 'Views/common/list-title.php'); ?>
<!-- main content part here -->
<div class="white_card_body ">
    <div class="QA_table" id="usersTable"></div>
</div>

<?php require_once (APPPATH . 'Views/users/footer.php'); ?>

<script>
    let columnsTitle = ['Id', 'Name', 'Email', 'Address', 'Status'];
    let columnsMachineName = ['id', 'name', 'email', 'address', 'status'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "users",
            apiPath: "api/v2/users",
            selector: "usersTable"
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
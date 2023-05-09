<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">

        <form id="adddomain" method="post" action="/blocks/update" enctype="multipart/form-data">
            <div class="form-row">

                <div class="form-group col-md-12">
                    <label for="inputEmail4">Code</label>
                    <input type="text" class="form-control" id="title" name="code" placeholder="" value="<?= @$blocks->code ?>">
                    <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$blocks->id ?>" />
                </div>
                <div class="form-group col-md-12">
                    <label for="inputEmail4">Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="" value="<?= @$blocks->ctitlede ?>">
                </div>

                <div class="form-group col-md-12">
                    <label for="inputPassword4">Text</label>
                    <textarea class="form-control block-text" name="text" style="width:100%!important;height:250px"><?= @$blocks->text ?></textarea>
                </div>


                <div class="form-group col-md-12">
                    <label for="inputStatus">Status</label>
                </div>
                <div class="form-group col-md-12">
                    <label for="inputStatus"><input type="radio" value="1" class="form-control" id="status" name="status" <?= @$blocks->status == 1 ? 'checked' : '' ?>> Yes</label>
                    <label for="inputStatus"><input type="radio" <?= @$blocks->status == 0 ? 'checked' : '' ?> value="0" class="form-control" id="status" name="status"> No</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

<!-- main content part end -->

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    var role = "<?= $role ?>";
</script>
<script src="/assets/js/block_edit.js"></script>
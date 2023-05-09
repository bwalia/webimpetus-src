<?php require_once(APPPATH . 'Views/common/edit-title.php');
$categories = getResultArray("categories");
$customers = getResultArray("customers");
?>
<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="inputEmail4">Customer Name</label>
                        </div>
                        <div class="col-md-6">
                            <select id="client_id" name="client_id" class="form-control required dashboard-dropdown">
                                <option value="" selected="">--Select--</option>
                                <?php foreach ($customers as $row) : ?>
                                    <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$document->client_id) {
                                                                            echo "selected";
                                                                        } ?>><?= $row['company_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php if (isset($document) && !empty($document->file)) { ?>
                        <!--php if (strlen(trim(@$document->file)) > 0) { -->
                        <div class="row form-group ">
                            <iframe src="https://drive.google.com/viewerng/viewer?embedded=true&url=<?= @$document->file ?>" width="960" height="1200"></iframe>
                        </div><br><br>
                    <?php } ?>
                    <div class="row form-group ">
                        <div class="col-md-4">
                            <label for="inputAddress">File Upload</label>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-file">

                                <input type="file" name="file" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group ">
                        <div class="col-md-4">
                            <label for="inputEmail4">Document Date</label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control datepicker" id="document_date" name="document_date" placeholder="" value="<?= render_date(@$document->document_date) ?>">
                        </div>
                    </div>

                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="category_id">Category</label>
                        </div>
                        <div class="col-md-6">
                            <select name="category_id" class="form-control required dashboard-dropdown">
                                <option value="">--Select--</option>
                                <?php foreach ($categories as $row) : ?>
                                    <option value="<?= $row['id'] ?>" <?= ($row['id'] == @$document->category_id ? 'selected' : '') ?>>
                                        <?= $row['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row form-group ">
                        <div class="col-md-4">
                            <label for="inputEmail4">Metadata</label>
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" name="metadata" id="metadata" cols="30" rows="5"><?= @$document->metadata ?></textarea>
                        </div>
                    </div>

                </div>
            </div>

            <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$document->id ?>" />

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->

<script>
    $(document).on("click", ".form-check-input", function() {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });
</script>
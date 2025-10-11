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
                    <?php if (isset($document) && !empty($document->file)) {
                        // Get file extension to determine how to preview
                        $fileExt = pathinfo($document->file, PATHINFO_EXTENSION);
                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                        $isImage = in_array(strtolower($fileExt), $imageExts);
                    ?>
                        <div class="row form-group">
                            <div class="col-md-12">
                                <h5>Current Document Preview</h5>
                                <?php if ($isImage) { ?>
                                    <!-- Image preview -->
                                    <div style="border: 1px solid #ddd; padding: 10px; margin-bottom: 15px;">
                                        <img src="/documents/preview/<?= $document->uuid ?>"
                                             style="max-width: 100%; height: auto;"
                                             alt="Document preview" />
                                    </div>
                                <?php } else if (strtolower($fileExt) === 'pdf') { ?>
                                    <!-- PDF preview in iframe -->
                                    <div style="border: 1px solid #ddd; margin-bottom: 15px;">
                                        <iframe src="/documents/preview/<?= $document->uuid ?>"
                                                width="100%"
                                                height="800"
                                                style="border: none;"></iframe>
                                    </div>
                                <?php } else { ?>
                                    <!-- Other file types - show download link -->
                                    <div style="border: 1px solid #ddd; padding: 20px; margin-bottom: 15px; text-align: center;">
                                        <p><i class="fa fa-file" style="font-size: 48px; color: #666;"></i></p>
                                        <p>File type: <?= strtoupper($fileExt) ?></p>
                                        <a href="/documents/download/<?= $document->uuid ?>"
                                           class="btn btn-primary"
                                           target="_blank">
                                            <i class="fa fa-download"></i> Download Document
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div><br>
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
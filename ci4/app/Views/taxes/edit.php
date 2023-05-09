<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">

        <form method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="tax_code">Tax Code</label>
                        </div>
                        <div class="col-md-6">
                            <input type="input" maxlength="24" autocomplete="off" class="form-control required" name="tax_code" value="<?= @$taxe->tax_code ?>">
                        </div>
                    </div>
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="tax_rate">Tax Rate</label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" autocomplete="off" class="form-control required" name="tax_rate" value="<?= @$taxe->tax_rate ?>">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-4">
                            <label for="description">Description</label>
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" name="description"><?= @$taxe->description ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$taxe->id ?>" />

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->
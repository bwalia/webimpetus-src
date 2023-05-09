<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<div class="white_card_body">
    <div class="card-body">

        <form method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="sprint_name">Sprint Name</label>
                        </div>
                        <div class="col-md-6">
                            <input type="input" autocomplete="off" class="form-control required" name="sprint_name" value="<?= empty($sprint->sprint_name) ? "Sprint Week " . date("W") : $sprint->sprint_name ?>" />
                        </div>
                    </div>
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="start_date">Start Date</label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control datepicker required" name="start_date" placeholder="" value="<?= isset($sprint->start_date) && !empty($sprint->start_date) ? render_date(strtotime(@$sprint->start_date)) : '' ?>">
                        </div>
                    </div>
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="end_date">End Date</label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control datepicker required" name="end_date" placeholder="" value="<?= isset($sprint->end_date) && !empty($sprint->end_date) ? render_date(strtotime(@$sprint->end_date)) : '' ?>">
                        </div>
                    </div>
                    <div class="row form-group required">
                        <div class="col-md-4">
                            <label for="note">Note</label>
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" name="note"><?= @$sprint->note ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$sprint->id ?>" />

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->
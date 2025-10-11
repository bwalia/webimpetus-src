<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">
        <form id="periodForm" method="post" action="/accounting-periods/update">
            <input type="hidden" name="uuid" value="<?= @$period->uuid ?>" />
            <input type="hidden" name="id" value="<?= @$period->id ?>" />

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="period_name">Period Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="period_name" name="period_name"
                               value="<?= @$period->period_name ?>" required
                               placeholder="e.g., Fiscal Year 2024, Q1 2024">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="start_date">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                               value="<?= @$period->start_date ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="end_date">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="<?= @$period->end_date ?>" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?= @$period->notes ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_current"
                                   name="is_current" value="1" <?= (@$period->is_current) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_current">
                                <strong>Set as Current Period</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_closed"
                                   name="is_closed" value="1" <?= (@$period->is_closed) ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="is_closed">
                                <strong>Period Closed</strong>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Save Period
            </button>
            <a href="/accounting-periods" class="btn btn-secondary">
                <i class="fa fa-times"></i> Cancel
            </a>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>

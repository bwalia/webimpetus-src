<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
    <div class="card-body">

        <form action="/user_business/update" method="post" id="userform">


            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputState">Select User</label>
                    <select id="user_id" name="user_id" class="form-control select2">
                        <?php
                        foreach ($allUsers as $row) : ?>
                            <option value="<?= $row->id; ?>" <?php
                                                                if ($row->id == @$result[0]->user_id) {
                                                                    echo 'selected';
                                                                } ?>><?= $row->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo @$result[0]->id; ?>" />
            <?php
            $arr = [];
            if (isset($result[0]->user_business_id) && !is_null($result[0]->user_business_id)) {
                $arr = json_decode(@$result[0]->user_business_id);
            }
            ?>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputState">User Access to Workspaces (Brands or Businesses etc.)</label>
                    <select id="user_business_id" name="user_business_id[]" multiple class="form-control select2">
                        <?php
                        foreach ($userBusiness as $row) : ?>
                            <option value="<?= $row->uuid; ?>" <?php
                                                                if (is_array($arr) && in_array($row->uuid, $arr)) {
                                                                    echo "selected";
                                                                } ?>><?= $row->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="inputState">Primary Business</label>
                    <select id="primary_business_uuid" name="primary_business_uuid" class="form-control">
                        <option value="">--Select--</option>
                        <?php
                        foreach ($userBusiness as $row) : ?>
                            <?php //if (is_array($arr) && in_array($row->uuid, $arr)) { ?>
                                <option value="<?= $row->uuid; ?>" <?= ($row->uuid == @$result[0]->primary_business_uuid ? 'selected' : '') ?>><?= $row->name; ?></option>
                            <?php //} ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->
<script>
    /*
    $(document).on("change", "#user_business_id", function() {
        let text = $("#user_business_id option:selected").text();
        let val = $("#user_business_id").val();
        $('#user_business_id option').each(function() {
            if (this.value == val) {
                return false;
            }
        });
    }); */
</script>
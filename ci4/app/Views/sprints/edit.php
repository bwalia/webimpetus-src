<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<style>
    .white_card {
    min-width: 40%;
    background-color: #FFFFFF;
    -webkit-border-radius: 15px;
    -moz-border-radius: 15px;
    border-radius: 15px;
    display: inline-block;
    }

@media screen and (max-width: 992px) {
  .white_card {
    width: 60%;
    background-color: #FFFFFF;
    -webkit-border-radius: 15px;
    -moz-border-radius: 15px;
    border-radius: 15px;
  }
  
    @media screen and (max-width: 768px) {
        .white_card {
            width: 100%;
            background-color: #FFFFFF;
            -webkit-border-radius: 15px;
            -moz-border-radius: 15px;
            border-radius: 15px;
        }
    }
}
</style>

<div class="white_card_body">
    <div class="card-body">

        <form method="post" action=<?php echo "/" . $tableName . "/update"; ?> enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <div class="row form-group required">
                        <div class="col-12">
                            <label for="sprint_name" class="font-weight-bolder">Sprint Name</label>
                            <input type="input" autocomplete="off" class="form-control required" name="sprint_name" id="sprint_name"
                                value="<?= empty($sprint->sprint_name) ? "Sprint Week " . date("W") : $sprint->sprint_name ?>" />
                        </div>
                    </div>
                    <div class="row form-group required">
                        <div class="col-12">
                            <label class="font-weight-bolder" for="start_date">Start Date</label>
                            <input type="text" id="start_date" class="form-control datepicker required" name="start_date" placeholder=""
                                value="<?= isset($sprint->start_date) && !empty($sprint->start_date) ? render_date(strtotime(@$sprint->start_date)) : '' ?>">
                        </div>
                    </div>
                    <div class="row form-group required">
                        <div class="col-12">
                            <label class="font-weight-bolder" for="end_date">End Date</label>
                            <input type="text" id="end_date" class="form-control datepicker required" name="end_date" placeholder=""
                                value="<?= isset($sprint->end_date) && !empty($sprint->end_date) ? render_date(strtotime(@$sprint->end_date)) : '' ?>">
                                <span class="form-control-feedback" id="sprintsEndDateErr"></span>
                        </div>
                    </div>
                    <div class="row form-group required">
                        <div class="col-12">
                            <label class="font-weight-bolder" for="note">Note</label>
                            <textarea class="form-control required" name="note"><?= @$sprint->note ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$sprint->id ?>" />
            <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$sprint->uuid ?>" />

            <div class="row ">
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" id="sprintsSubmit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->

<script>
    $(".white_card.card_height_100").removeClass("card_height_100");

    $("#sprintsSubmit").click(function (event) {
        const startDate = $("#start_date").val();
        const deadLineDate = $("#end_date").val();
        validateEndDate(startDate, deadLineDate, event);
        validateName($("#sprint_name"), event);
    })

    $("#end_date").change(function () {
        const startDate = $("#start_date").val();
        const deadLineDate = $(this).val();
        validateEndDate(startDate, deadLineDate, null)
    })

    function validateEndDate(slipStartDate, slipEndDate, evt) {
        // Convert date strings to Date objects
        const endDate = new Date(slipEndDate);
        const startDate = new Date(slipStartDate);
        // Calculate the time difference in milliseconds
        const timeDifference = endDate - startDate;
        // Convert milliseconds to days (rounded to the nearest day)
        const daysDifference = Math.round(timeDifference / (1000 * 60 * 60 * 24));
        if (daysDifference < 0) {
            $("#sprintsEndDateErr").text("Sprint end date should be greater than the sprint start date.");
            if (evt !== null) {
                evt.preventDefault();
            }
            return false;
        } else {
            $("#sprintsEndDateErr").text("");
        }
    }
</script>
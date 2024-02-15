<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>
<?php $customers = getResultArray("customers"); ?>
<div class="white_card_body">
    <div class="card-body">

        <form id="addcustomer" method="post" action=<?php echo "/" . $tableName . "/update"; ?>
            enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group required col-md-6">
                    <label for="inputEmail4">Customer Name</label>
                    <select id="customers_id" name="customers_id" class="form-control required dashboard-dropdown">
                        <option value="" selected="">--Select--</option>
                        <?php foreach ($customers as $row): ?>
                            <option value="<?= $row['id']; ?>" <?php if ($row['id'] == @$project->customers_id) {
                                  echo "selected";
                              } ?>><?= $row['company_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group required col-md-6">
                    <label for="inputEmail4">Project Name</label>
                    <input type="input" autocomplete="off" class="form-control required" id="name" name="name"
                        placeholder="" value="<?= @$project->name ?>">
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4">Start Date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="start_date"
                        name="start_date" placeholder="" value="<?= render_date(@$project->start_date) ?>">
                </div>

                <div class="form-group  col-md-6">
                    <label for="inputEmail4">Deadline date</label>
                    <input autocomplete="off" type="text" class="form-control datepicker" id="deadline_date"
                        name="deadline_date" placeholder="" value="<?= render_date(@$project->deadline_date) ?>">
                        <span class="form-control-feedback" id="deadlineError"></span>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputEmail4"> Charge Rate</label>
                    <input type="number" class="form-control" id="rate" name="rate" placeholder=""
                        value="<?= @$project->rate ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="inputEmail4">Currency</label>

                    <select name="currency" id="currency" class="form-control select2">
                        <option value="gbp" <?php if (@$project->currency == "gbp")
                            echo "selected" ?>>GBP</option>
                            <option value="usd" <?php if (@$project->currency == "usd")
                            echo "selected" ?>>USD</option>
                            <option value="eur" <?php if (@$project->currency == "eur")
                            echo "selected" ?>>EUR</option>

                        </select>
                    </div>
                </div>
                <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$project->id ?>" />
                <input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$project->uuid ?>" />

            <div class="form-row">

                <div class="form-group col-md-6">
                    <label for="inputEmail4">Budget</label>
                    <input type="number" class="form-control" id="budget" name="budget" step=".01" placeholder=""
                        value="<?= @$project->budget ?>">
                </div>

                <div class="form-group col-md-6">
                    <label for="inputEmail4">Active</label>
                    <select name="active" id="active" class="form-control select2">
                        <option value="1" <?php if (@$project->active == 1)
                            echo "selected" ?>>Active</option>
                            <option value="2" <?php if (@$project->active == 2)
                            echo "selected" ?>>Completed</option>
                        </select>

                    </div>
                </div>



                <button type="submit" id="projectsSubmit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<!-- main content part end -->

<script>
    $(document).on("click", ".form-check-input", function () {
        if ($(this).prop("checked") == false) {
            $(this).val(0);
        } else {
            $(this).val(1);
        }
    });

    $("#projectsSubmit").click(function (event) {
        const startDate = $("#start_date").val();
        const deadLineDate = $("#deadline_date").val();
        validateName($("#name"), event);
        validateEndDate(startDate, deadLineDate, event)
    })

    $("#deadline_date").change(function () {
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
        console.log({timeDifference, daysDifference});
        if (daysDifference < 0) {
            $("#deadlineError").text("Deadline date should be greater than the start date.");
            if (evt !== null) {
                evt.preventDefault();
            }
            return false;
        } else {
            $("#deadlineError").text("");
        }
    }
</script>
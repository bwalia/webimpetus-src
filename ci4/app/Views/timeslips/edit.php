<?php require_once(APPPATH . 'Views/common/edit-title.php');

if (empty(@$timeslips['slip_start_date'])) {
    $startDate = time();
} else {
    $startDate = @$timeslips['slip_start_date'];
}
if (empty(@$timeslips['slip_timer_started'])) {
    $slip_timer_started = date("h:i:s a");
} else {
    $slip_timer_started = @$timeslips['slip_timer_started'];
}

?>
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

        <form id="addcat" method="post" action="<?php echo $actionUrl; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="task_name" class="font-weight-bolder"><?= lang('Timeslips.task_name');?> <span class="redstar">*</span></label>
                <select id="task_name" name="task_name" class="form-control required dashboard-dropdown">
                    <option value="">--Select--</option>
                    <?php foreach ($tasks as $row) { ?>
                        <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$timeslips['task_name']) ? 'selected' : '' ?>><?= $row['name']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="employee_name" class="font-weight-bolder"> <?=lang('Timeslips.employee_name');?> <span class="redstar">*</span></label>
                <select id="employee_name" name="employee_name" class="form-control required dashboard-dropdown">
                    <option value="">--Select--</option>
                    <?php foreach ($employees as $row) { ?>
                        <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$timeslips['employee_name']) ? 'selected' : '' ?>><?= $row['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="slip_start_date" class="font-weight-bolder"> <?=lang('Timeslips.slip_start_date');?> <span class="redstar">*</span></label>
                <div class="input-group">
                    <input type="text" id="slip_start_date" name="slip_start_date" class="form-control required datepicker" value="<?php echo render_date($startDate); ?>">
                    <span class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label for="slip_timer_started" class="font-weight-bolder"> <?=lang('Timeslips.slip_timer_started');?> </label>
                <div class="form-row">
                    <div class="col-sm-7 col-12 mb-1">
                        <div class="input-group">
                            <input id="slip_timer_started" name="slip_timer_started" class="form-control timepicker" value="<?php echo @$slip_timer_started; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-clock"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 col-12">
                        <button type="button" class="btn btn-block btn-info set-current-time"><?php echo lang('Timeslips.set_current_time');?></button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="slip_end_date" class="font-weight-bolder"> <?=lang('Timeslips.slip_end_date');?> </label>
                <div class="input-group">
                    <input id="slip_end_date" name="slip_end_date" class="form-control datepicker" value="<?php echo render_date(@$timeslips['slip_end_date'] ?? $startDate); ?>">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
                <span class="form-control-feedback" id="end_date_error"></span>
            </div>

            <div class="form-group">
                <label for="slip_end_date" class="font-weight-bolder"> <?=lang('Timeslips.slip_timer_end');?> </label>
                <div class="form-row">
                    <div class="col-sm-7 col-12 mb-1">
                        <div class="input-group">
                            <input id="slip_timer_end" name="slip_timer_end" class="form-control timepicker" value="<?php echo @$timeslips['slip_timer_end'] ?? @$slip_timer_started; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fa fa-clock"></i></span>
                            </div>
                        </div>
                        <span class="form-control-feedback" id="end_timer_error"></span>
                    </div>
                    <div class="col-sm-5 col-12">
                        <button type="button" class="btn btn-block btn-info set-current-time"><?php echo lang('Timeslips.set_current_time');?></button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="slip_description" class="font-weight-bolder"> <?=lang('Timeslips.slip_description');?><span class="redstar">*</span> </label>
                <textarea required id="slip_description" name="slip_description" class="form-control" rows="5"><?php echo @$timeslips['slip_description']; ?></textarea>
            </div>


            <div class="form-group">
                <label for="billing_status" class="font-weight-bolder"> <?=lang('Timeslips.billing_status');?> <span class="redstar">*</span></label>
                <select id="billing_status" name="billing_status" class="form-control dashboard-dropdown">
                    <!-- <option value="">--Select--</option> -->
                    <option value="SLA" <?= ('SLA' == @$timeslips['billing_status']) ? 'selected' : '' ?>>SLA</option>
                    <option value="chargeable" <?= ('chargeable' == @$timeslips['billing_status']) ? 'selected' : '' ?>>Chargeable</option>
                    <option value="Billed" <?= ('Billed' == @$timeslips['billing_status']) ? 'selected' : '' ?>>Billed</option>
                </select>
            </div>

            <!-- Total Hours -->
            <div class="form-group">
                <label for="slip_hours" class="font-weight-bolder"> <?=lang('Timeslips.slip_hours');?> </label>
                <div class="input-group">
                    <input id="slip_hours" name="slip_hours" class="form-control timepicker" value="<?php echo @$timeslips['slip_hours']; ?>">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-clock"></i></span>
                    </div>
                </div>
                <span class="form-control-feedback" id="slip_hours_error"></span>
            </div>

            <div class="advance-input" style="display: none;">
                <div class="form-group">
                    <label for="week_no" class="font-weight-bolder"><?=lang('Timeslips.week_no');?> </label>
                    <input id="week_no" readonly name="week_no" class="form-control" value="<?= empty($timeslips['week_no']) ? date("W") : $timeslips['week_no'] ?>">
                </div>
                <div class="form-group">
                    <label for="break_time" class="font-weight-bolder"> <?=lang('Timeslips.break_time');?> </label> <br/>
                    <input type="checkbox" id="break_time" name="break_time" <?php echo @$timeslips['break_time'] == '1' ? 'checked' : ''; ?>>
                    <label for="break_time"><?php echo lang('Timeslips.exclude_time');?></label>
                </div>

                <?php
                $showBreakStartAndEndTimer = 'display: none;';
                if (@$timeslips['break_time'] == 1) {
                    $showBreakStartAndEndTimer = '';
                } ?>
                <div class="break_time_detail form-row" style="<?php echo $showBreakStartAndEndTimer; ?>">
                    <div class="form-group col-md-6 col-12">
                        <label for="break_time_start" class="font-weight-bolder"> <?=lang('Timeslips.break_time_start');?> </label>
                        <input id="break_time_start" name="break_time_start" class="form-control timepicker" value="<?php echo @$timeslips['break_time_start']; ?>">
                    </div>
                    <div class="form-group col-md-6 col-12">
                        <label for="break_time_end" class="font-weight-bolder"> <?=lang('Timeslips.break_time_end');?> </label>
                        <input id="break_time_end" name="break_time_end" class="form-control timepicker" value="<?php echo @$timeslips['break_time_end']; ?>">
                        <span class="form-control-feedback" id="break_end_timer_error"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="slip_rate" class="font-weight-bolder"> <?=lang('Timeslips.slip_rate');?></label>
                    <input id="slip_rate" name="slip_rate" class="form-control" value="<?php echo @$timeslips['slip_rate']; ?>">
                </div>

                <div class="form-group">
                    <label for="slip_timer_accumulated_seconds" class="font-weight-bolder"> <?=lang('Timeslips.slip_timer_accumulated_seconds');?></label>
                    <input id="slip_timer_accumulated_seconds" name="slip_timer_accumulated_seconds" class="form-control" value="<?php echo @$timeslips['slip_timer_accumulated_seconds']; ?>">
                </div>
            </div>
            
            <div class="d-flex justify-content-end mb-4">
                <button type="button" class="btn btn-sm btn-light btn-advance btn-advance-down">Advance <i class="fa fa-chevron-down"></i></button>
                <button type="button" class="btn btn-sm btn-light btn-advance btn-advance-up" style="display: none;">Advance <i class="fa fa-chevron-up"></i></button>
            </div>

            <div class="d-flex justify-content-end">
                <a href="/<?php echo strtolower($tableName).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''); ?>" type="button" class="btn btn-secondary"><?php echo lang('Common.cancel');?></a>
                <button type="submit" class="btn btn-primary timeslip-submit-btn ml-2"><?php echo lang('Common.submit');?></button>
            </div>
        </form>
    </div>
</div>


<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
    $(document).ready(function() {
        setInterval(function() {
            calculateTime();
        }, 1000);
    })
    $(function() {
        const breakTime =  $("#break_time").is(":checked");
        $("#break_time").change(function() {
            var el = $(this);
            if (el.is(':checked')) {
                $('.break_time_detail').slideDown('slow');
            } else {
                $('.break_time_detail').slideUp('slow');
            }
        });

        $(".set-current-time").click(function() {
            var el = $(this);
            console.dir({element: el[0]});
            setCurrentTime(el, calculateTime)
        });

        $("#slip_end_date").change(function (evt) {
            const slipStartDate = document.getElementById("slip_start_date").value;
            const slipEndDate = evt.target.value;
            slipEndDateVarify(slipStartDate, slipEndDate, null);
        })

        $(".timeslip-submit-btn").click(function (e) {
            const slipStartDate = document.getElementById("slip_start_date").value;
            const slipEndDate = document.getElementById("slip_end_date").value;
            slipEndDateVarify(slipStartDate, slipEndDate, e);
            slipTimerVerify(e);
            if (breakTime) {
                const endValue = $("#break_time_end").val();
                const startValue = $("#break_time_start").val();
                validateBreakeEnd(startValue, endValue, e)
            }
        });

        $("#slip_timer_end").focusout(function () {
            slipTimerVerify(null);
        })

        if (breakTime) {
            $("#break_time_end").focusout(function () {
                const endValue = $(this).val();
                const startValue = $("#break_time_start").val();
                validateBreakeEnd(startValue, endValue, null)
            })
        }
    });

    function slipEndDateVarify (slipStartDate, slipEndDate, evt) {
         // Convert date strings to Date objects
        const endDate = new Date(slipEndDate);
        const startDate = new Date(slipStartDate);
        // Calculate the time difference in milliseconds
        const timeDifference = endDate - startDate;
        // Convert milliseconds to days (rounded to the nearest day)
        const daysDifference = Math.round(timeDifference / (1000 * 60 * 60 * 24));
        if (daysDifference < 0) {
            $("#end_date_error").text("Slip end date should be greater than the slip start date.");
            if (evt !== null) {
                evt.preventDefault();
            }
            return false;
        } else {
            $("#end_date_error").text("");
        }
    }

    function slipTimerVerify(evt) {
        const slipStartDate = document.getElementById("slip_start_date").value;
        const slipEndDate = document.getElementById("slip_end_date").value;
        const endDate = new Date(slipEndDate);
        const startDate = new Date(slipStartDate);
        const timeDifference = endDate - startDate;
        const daysDifference = Math.round(timeDifference / (1000 * 60 * 60 * 24));
        if (daysDifference == 0) {
            const slipStartTime = document.getElementById("slip_timer_started").value;
            const slipEndTimer = document.getElementById("slip_timer_end").value;
            const endedTime = new Date(`1970-01-01 ${slipEndTimer}`);
            const startedTime = new Date(`1970-01-01 ${slipStartTime}`);

            // Check if the first time is greater than the second time
            if (endedTime <= startedTime) {
                $("#end_timer_error").text("Slip end time should be greater than the slip start time.");
                if (evt !== null) {
                    evt.preventDefault();
                }
                return false; 
            } else {
                const timeDifference = endedTime - startedTime;

                // Convert milliseconds to hours, minutes, and seconds
                const secondsDifference = Math.abs(Math.floor(timeDifference / 1000));
                const minutesDifference = Math.floor(secondsDifference / 60);
                const hoursDifference = Math.floor(minutesDifference / 60);

                // Calculate the remaining minutes and seconds
                const remainingMinutes = minutesDifference % 60;
                const remainingSeconds = secondsDifference % 60;
                $("#end_timer_error").text("");
            }
        }
    }

    function validateBreakeEnd (startValue, endValue, evt) {
        const endedTime = new Date(`1970-01-01 ${endValue}`);
        const startedTime = new Date(`1970-01-01 ${startValue}`);

        // Check if the first time is greater than the second time
        if (endedTime <= startedTime) {
            $("#break_end_timer_error").text("Break end time should be greater than the Break start time.");
            if (evt !== null) {
                evt.preventDefault();
            }
            return false; 
        }
    }

    function setCurrentTime(el, callback) {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const seconds = now.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        // Convert hours to 12-hour format
        hours = hours % 12 || 12;
        // Add leading zero to minutes and seconds if necessary
        const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;
        const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;
        // Create the formatted time string
        const formattedTime = `${hours}:${formattedMinutes}:${formattedSeconds} ${ampm}`;
        el.closest('.form-row').find('input.timepicker').val(formattedTime);
        callback();
    }

    function calculateTime() {
        var startDate = $("#slip_start_date").val();
        var startTime = $("#slip_timer_started").val();
        var endDate = $("#slip_end_date").val();
        var endTime = $("#slip_timer_end").val();
        const breakTime =  $("#break_time").is(":checked");

        const startDatetimeString = startDate + ' ' + startTime;
        const endDatetimeString = endDate + ' ' + endTime;
        const startDateObj = new Date(startDatetimeString);
        const endDateObj = new Date(endDatetimeString);
        const timeDifferenceMs = endDateObj - startDateObj;
        const timeSeconds = Math.floor(timeDifferenceMs / 1000);
        const timeMinutes = Math.floor(timeSeconds / 60);
        const timeHours = Math.floor(timeMinutes / 60);
        let diffInHours = `${timeHours}.${timeMinutes % 60}`

        if (breakTime) {
            const breakTimeStVal = $("#break_time_start").val();
            const breakTimeEndVal = $("#break_time_end").val();
            const breakStartTime = new Date(`2000-01-01 ${breakTimeStVal}`);
            const breakEndTime = new Date(`2000-01-01 ${breakTimeEndVal}`);

            // Calculate the time difference in milliseconds
            const timeDifferenceMillis = breakEndTime - breakStartTime;

            // Convert the time difference to hours, minutes, and seconds
            const seconds = Math.floor(timeDifferenceMillis / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);

            // Calculate remaining minutes and seconds
            const remainingMinutes = minutes % 60;
            const remainingSeconds = seconds % 60;
            const [totalhours, totalminutes] = diffInHours.split('.').map(parseFloat);
            let newHours = totalhours - hours;
            let newMinutes = (totalminutes || 0) - remainingMinutes
            if (Math.sign(newMinutes) === -1) {
                newHours = newHours - 1;
                newMinutes = 60 + newMinutes;
            }
            diffInHours = `${newHours}.${newMinutes}`;
        }

        $('#slip_hours').val(diffInHours);
    }

    var roundUp = function(num, precision) {
        // Return '' if num is empty string
        if (typeof num === 'string' && !num) return '';

        // Remove exponential notation
        num = toPlainString(num);

        // Fixed round up
        var result = +((+(Math.round(+(num + 'e' + precision)) + 'e' + -precision)).toFixed(precision));

        // Remove exponential notation (once again)
        result = toPlainString(result);

        return result;
    };

    var toPlainString = function(num) {
        return ('' + num).replace(/(-?)(\d*)\.?(\d+)e([+-]\d+)/,
            function(a, b, c, d, e) {
                return e < 0 ?
                    b + '0.' + Array(1 - e - c.length).join(0) + c + d :
                    b + c + d + Array(e - d.length + 1).join(0);
            }
        );
    }

    $("#slip_start_date").change(function() {
        var slip_start_date = $(this).val();
        var start_date = new Date(slip_start_date);
        var onejan = new Date(start_date.getFullYear(), 0, 1);
        var today = new Date(start_date.getFullYear(), start_date.getMonth(), start_date.getDate());
        var dayOfYear = ((today - onejan + 86400000) / 86400000); // 24*60*60*1000
        var week_no = Math.ceil(dayOfYear / 7);
        week_no = week_no > 52 ? 52 : week_no;
        $("#week_no").val(week_no);
    });

    $(".btn-advance").click(function() {
        $(".btn-advance-down").toggle();
        $(".btn-advance-up").toggle();
        $(".advance-input").slideToggle("slow");
    });
</script>
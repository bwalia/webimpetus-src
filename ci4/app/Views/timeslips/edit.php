<?php require_once(APPPATH . 'Views/common/edit-title.php');

if (empty(@$timeslips['slip_start_date'])) {
    $startDate = time();
} else {
    $startDate = @$timeslips['slip_start_date'];
}
if (empty(@$timeslips['slip_timer_started'])) {
    $slip_timer_started = date("H:i:s", time());
} else {
    $slip_timer_started = @$timeslips['slip_timer_started'];
}

?>
<div class="white_card_body">
    <div class="card-body">

        <form id="addcat" method="post" action="<?php echo $actionUrl; ?>" enctype="multipart/form-data">

            <div class="form-row">

                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.task_name');//readableFieldName('task_name'); ?>
                    <span class="redstar">*</span>
                </div>
                <div class="form-group required col-md-4">
                    <select id="task_name" name="task_name" class="form-control required dashboard-dropdown">
                        <option value="">--Select--</option>
                        <?php foreach ($tasks as $row) { ?>
                            <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$timeslips['task_name']) ? 'selected' : '' ?>><?= $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.week_no'); //readableFieldName('week_no'); ?>
                </div>
                <div class="form-group col-md-4">
                    <input id="week_no" readonly name="week_no" class="form-control" value="<?= empty($timeslips['week_no']) ? date("W") : $timeslips['week_no'] ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.employee_name'); //readableFieldName('employee_name'); ?>
                </div>
                <div class="form-group required col-md-4">
                    <select id="employee_name" name="employee_name" class="form-control required dashboard-dropdown">
                        <option value="">--Select--</option>
                        <?php foreach ($employees as $row) { ?>
                            <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$timeslips['employee_name']) ? 'selected' : '' ?>><?= $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_start_date'); //readableFieldName('slip_start_date'); ?>
                    <span class="redstar">*</span>
                </div>
                <div class="form-group col-md-4">
                    <div class="input-group">
                        <input type="text" id="slip_start_date" name="slip_start_date" class="form-control required datepicker" value="<?php echo render_date($startDate); ?>">
                        <span class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_timer_started'); //readableFieldName('slip_timer_started'); ?>
                </div>
                <div class="form-group col-md-4">
                    <div class="input-group">
                        <input id="slip_timer_started" name="slip_timer_started" class="form-control timepicker" value="<?php echo @$slip_timer_started; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-clock"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-info set-current-time"><?php echo lang('Timeslips.set_current_time');?></button>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_end_date'); //readableFieldName('slip_end_date'); ?>
                </div>
                <div class="form-group col-md-4">
                    <div class="input-group">
                        <input id="slip_end_date" name="slip_end_date" class="form-control datepicker" value="<?php echo render_date(@$timeslips['slip_end_date']); ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_timer_end'); //readableFieldName('slip_timer_end'); ?>
                </div>
                <div class="form-group col-md-4">
                    <div class="input-group">
                        <input id="slip_timer_end" name="slip_timer_end" class="form-control timepicker" value="<?php echo @$timeslips['slip_timer_end']; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-clock"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-info set-current-time"><?php echo lang('Timeslips.set_current_time');?></button>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.break_time'); //readableFieldName('break_time'); ?>
                </div>
                <div class="form-group col-md-4">
                    <input type="checkbox" id="break_time" name="break_time" <?php echo @$timeslips['break_time'] == '1' ? 'checked' : ''; ?>>
                    <span><?php echo lang('Timeslips.exclude_time');?></span>
                </div>

            </div>
            <?php
            $showBreakStartAndEndTimer = 'display: none;';
            if (@$timeslips['break_time'] == 1) {
                $showBreakStartAndEndTimer = '';
            } ?>
            <div class="form-row" style="<?php echo $showBreakStartAndEndTimer; ?>">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.break_time_start'); //readableFieldName('break_time_start'); ?>
                </div>
                <div class="form-group col-md-3">
                    <div class="input-group">
                        <input id="break_time_start" name="break_time_start" class="form-control timepicker" value="<?php echo @$timeslips['break_time_start']; ?>">
                    </div>
                </div>

                <div class="form-group col-md-1"></div>
                <div class="form-group col-md-2">
                    <?php echo lang('Timeslips.break_time_end'); //readableFieldName('break_time_end'); ?>
                </div>
                <div class="form-group col-md-3">
                    <div class="input-group">
                        <input id="break_time_end" name="break_time_end" class="form-control timepicker" value="<?php echo @$timeslips['break_time_end']; ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_hours'); //readableFieldName('slip_hours'); ?>
                </div>
                <div class="form-group col-md-4">
                    <input id="slip_hours" name="slip_hours" class="form-control" value="<?php echo @$timeslips['slip_hours']; ?>">
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_description'); //readableFieldName('slip_description'); ?>
                    <span class="redstar">*</span>
                </div>
                <div class="form-group col-md-4">
                    <textarea id="slip_description" name="slip_description" class="form-control"><?php echo @$timeslips['slip_description']; ?></textarea>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_rate'); //readableFieldName('slip_rate'); ?>
                </div>
                <div class="form-group col-md-4">
                    <input id="slip_rate" name="slip_rate" class="form-control" value="<?php echo @$timeslips['slip_rate']; ?>">
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.slip_timer_accumulated_seconds'); //readableFieldName('slip_timer_accumulated_seconds'); ?>
                </div>
                <div class="form-group col-md-4">
                    <input id="slip_timer_accumulated_seconds" name="slip_timer_accumulated_seconds" class="form-control" value="<?php echo @$timeslips['slip_timer_accumulated_seconds']; ?>">
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <?php echo lang('Timeslips.charge_code'); //readableFieldName('Charge Code'); ?>
                </div>
                <div class="form-group required col-md-4">
                    <select id="billing_status" name="billing_status" class="form-control dashboard-dropdown">
                        <!-- <option value="">--Select--</option> -->
                        <option value="SLA" <?= ('SLA' == @$timeslips['billing_status']) ? 'selected' : '' ?>>SLA</option>
                        <option value="chargeable" <?= ('chargeable' == @$timeslips['billing_status']) ? 'selected' : '' ?>>Chargeable</option>
                        <option value="Billed" <?= ('Billed' == @$timeslips['billing_status']) ? 'selected' : '' ?>>Billed</option>
                    </select>
                </div>

            </div>

            <div class="form-row">
                <div class="col-md-3"></div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><?php echo lang('Common.submit');?></button>
                    <a href="/<?php echo strtolower($tableName).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''); ?>" type="button" class="btn btn-secondary"><?php echo lang('Common.cancel');?></a>
                </div>
            </div>
        </form>
    </div>
</div>


<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
    $(function() {
        $("#break_time").change(function() {
            var el = $(this);
            if (el.is(':checked')) {
                el.closest('.form-row').next().slideDown('slow');
            } else {
                el.closest('.form-row').next().slideUp('slow');
            }
        });

        $(".set-current-time").click(function() {
            var el = $(this);
            setCurrentTime(el, calculateTime)
        });
    })

    function setCurrentTime(el, callback) {
        var d = new Date();
        var h = d.getHours();
        h = h < 10 ? '0' + h : h;
        var m = d.getMinutes();
        m = m < 10 ? '0' + m : m;
        var s = d.getSeconds();
        s = s < 10 ? '0' + s : s;
        var meridiem = h >= 12 ? "pm" : "am";
        var time = h + ':' + m + ':' + s + ' ' + meridiem;
        el.closest('.form-row').find('input.timepicker').val(time);
        callback();
    }

    function calculateTime() {
        var startDate = $("#slip_start_date").val();
        var startTime = $("#slip_timer_started").val();
        var endDate = $("#slip_end_date").val();
        var endTime = $("#slip_timer_end").val();

        var startDateObj = Date.parse(startDate + ' ' + startTime);
        var endDateObj = Date.parse(endDate + ' ' + endTime);
        var diffInHours = roundUp((endDateObj - startDateObj) / 3600000, 2);

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
</script>
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

    /* Timer Widget Styles */
    .timer-widget {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 100%;
    }

    .timer-display {
        font-size: 3.5rem;
        font-weight: 700;
        text-align: center;
        font-family: 'Courier New', monospace;
        letter-spacing: 0.1em;
        margin: 20px 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .timer-controls {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .timer-btn {
        padding: 12px 30px;
        border: none;
        border-radius: 25px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        font-size: 16px;
    }

    .timer-btn-start {
        background-color: #10b981;
        color: white;
    }

    .timer-btn-start:hover {
        background-color: #059669;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }

    .timer-btn-pause {
        background-color: #f59e0b;
        color: white;
    }

    .timer-btn-pause:hover {
        background-color: #d97706;
        transform: translateY(-2px);
    }

    .timer-btn-stop {
        background-color: #ef4444;
        color: white;
    }

    .timer-btn-stop:hover {
        background-color: #dc2626;
        transform: translateY(-2px);
    }

    .timer-btn-reset {
        background-color: #6b7280;
        color: white;
    }

    .timer-btn-reset:hover {
        background-color: #4b5563;
        transform: translateY(-2px);
    }

    .timer-status {
        text-align: center;
        margin-top: 15px;
        font-size: 14px;
        opacity: 0.9;
    }

    .timer-status-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 12px;
        background-color: rgba(255,255,255,0.2);
        font-weight: 600;
    }

    .quick-time-btns {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .quick-time-btn {
        padding: 8px 16px;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 20px;
        background-color: rgba(255,255,255,0.1);
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 14px;
    }

    .quick-time-btn:hover {
        background-color: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.5);
    }

    .form-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }

    @media screen and (max-width: 992px) {
        .white_card {
            width: 60%;
        }
        .timer-display {
            font-size: 2.5rem;
        }
    }

    @media screen and (max-width: 768px) {
        .white_card {
            width: 100%;
        }
        .timer-display {
            font-size: 2rem;
        }
        .timer-controls {
            flex-direction: column;
        }
        .timer-btn {
            width: 100%;
        }
    }
</style>

<div class="white_card_body">
    <div class="card-body">

        <!-- Timer Widget -->
        <div class="timer-widget">
            <h3 class="text-center mb-3" style="font-weight: 600;">⏱️ Time Tracker</h3>
            <div class="timer-display" id="timerDisplay">00:00:00</div>

            <div class="timer-status">
                <span class="timer-status-badge" id="timerStatus">Ready to Start</span>
            </div>

            <div class="timer-controls">
                <button type="button" class="timer-btn timer-btn-start" id="startTimer">
                    <i class="fa fa-play"></i> Start
                </button>
                <button type="button" class="timer-btn timer-btn-pause" id="pauseTimer" style="display:none;">
                    <i class="fa fa-pause"></i> Pause
                </button>
                <button type="button" class="timer-btn timer-btn-stop" id="stopTimer" style="display:none;">
                    <i class="fa fa-stop"></i> Stop
                </button>
                <button type="button" class="timer-btn timer-btn-reset" id="resetTimer">
                    <i class="fa fa-redo"></i> Reset
                </button>
            </div>

            <div class="quick-time-btns" style="margin-top: 20px; justify-content: center;">
                <button type="button" class="quick-time-btn" data-minutes="15">+15 min</button>
                <button type="button" class="quick-time-btn" data-minutes="30">+30 min</button>
                <button type="button" class="quick-time-btn" data-minutes="60">+1 hour</button>
                <button type="button" class="quick-time-btn" data-minutes="120">+2 hours</button>
            </div>
        </div>

        <form id="addcat" method="post" action="<?php echo $actionUrl; ?>" enctype="multipart/form-data">

            <!-- Basic Information Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa fa-info-circle"></i> Basic Information
                </div>

                <div class="form-group">
                    <label for="task_name" class="font-weight-bolder"><?= lang('Timeslips.task_name');?> <span class="redstar">*</span></label>
                    <select id="task_name" name="task_name" class="form-control required dashboard-dropdown">
                        <option value="">--Select Task--</option>
                        <?php foreach ($tasks as $row) { ?>
                            <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$timeslips['task_name']) ? 'selected' : '' ?>><?= $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="employee_name" class="font-weight-bolder"> <?=lang('Timeslips.employee_name');?> <span class="redstar">*</span></label>
                    <select id="employee_name" name="employee_name" class="form-control required dashboard-dropdown">
                        <option value="">--Select Employee--</option>
                        <?php foreach ($employees as $row) { ?>
                            <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$timeslips['employee_name']) ? 'selected' : '' ?>><?= $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="slip_description" class="font-weight-bolder"> <?=lang('Timeslips.slip_description');?><span class="redstar">*</span> </label>
                    <textarea required id="slip_description" name="slip_description" class="form-control" rows="4" placeholder="Describe the work performed..."><?php echo @$timeslips['slip_description']; ?></textarea>
                </div>
            </div>

            <!-- Time Details Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa fa-clock"></i> Time Details
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slip_start_date" class="font-weight-bolder">Start Date <span class="redstar">*</span></label>
                            <div class="input-group">
                                <input type="text" id="slip_start_date" name="slip_start_date" class="form-control required datepicker" value="<?php echo render_date($startDate); ?>">
                                <span class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slip_timer_started" class="font-weight-bolder">Start Time</label>
                            <div class="input-group">
                                <input id="slip_timer_started" name="slip_timer_started" class="form-control timepicker" value="<?php echo @$slip_timer_started; ?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-info set-current-time-start" title="Set current time">
                                        <i class="fa fa-clock"></i> Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slip_end_date" class="font-weight-bolder">End Date</label>
                            <div class="input-group">
                                <input id="slip_end_date" name="slip_end_date" class="form-control datepicker" value="<?php echo render_date(@$timeslips['slip_end_date'] ?? $startDate); ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <span class="form-control-feedback text-danger" id="end_date_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slip_timer_end" class="font-weight-bolder">End Time</label>
                            <div class="input-group">
                                <input id="slip_timer_end" name="slip_timer_end" class="form-control timepicker" value="<?php echo @$timeslips['slip_timer_end'] ?? @$slip_timer_started; ?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-info set-current-time-end" title="Set current time">
                                        <i class="fa fa-clock"></i> Now
                                    </button>
                                </div>
                            </div>
                            <span class="form-control-feedback text-danger" id="end_timer_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slip_hours" class="font-weight-bolder">
                                <i class="fa fa-hourglass-half"></i> <?=lang('Timeslips.slip_hours');?>
                            </label>
                            <div class="input-group">
                                <input id="slip_hours" name="slip_hours" class="form-control form-control-lg" readonly style="font-weight: 600; font-size: 1.2rem;" value="<?php echo @$timeslips['slip_hours']; ?>">
                                <div class="input-group-append">
                                    <span class="input-group-text">hours</span>
                                </div>
                            </div>
                            <span class="form-control-feedback text-danger" id="slip_hours_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="billing_status" class="font-weight-bolder"> <?=lang('Timeslips.billing_status');?> <span class="redstar">*</span></label>
                            <select id="billing_status" name="billing_status" class="form-control dashboard-dropdown">
                                <option value="SLA" <?= ('SLA' == @$timeslips['billing_status']) ? 'selected' : '' ?>>SLA</option>
                                <option value="chargeable" <?= ('chargeable' == @$timeslips['billing_status']) ? 'selected' : '' ?>>Chargeable</option>
                                <option value="Billed" <?= ('Billed' == @$timeslips['billing_status']) ? 'selected' : '' ?>>Billed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Options (Collapsible) -->
            <div class="form-section" style="border: 2px dashed #dee2e6; background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="$('.advance-input').slideToggle(); $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');">
                    <div class="form-section-title mb-0" style="border-bottom: none; padding-bottom: 0;">
                        <i class="fa fa-cog"></i> Advanced Options
                    </div>
                    <i class="fa fa-chevron-down" style="color: #6b7280;"></i>
                </div>

                <div class="advance-input" style="display: none; margin-top: 15px;">
                    <div class="form-group">
                        <label for="week_no" class="font-weight-bolder"><?=lang('Timeslips.week_no');?> </label>
                        <input id="week_no" readonly name="week_no" class="form-control" value="<?= empty($timeslips['week_no']) ? date("W") : $timeslips['week_no'] ?>">
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="break_time" name="break_time" <?php echo @$timeslips['break_time'] == '1' ? 'checked' : ''; ?>>
                            <label class="custom-control-label font-weight-bolder" for="break_time">
                                <?php echo lang('Timeslips.exclude_time');?> (Break Time)
                            </label>
                        </div>
                    </div>

                    <?php
                    $showBreakStartAndEndTimer = 'display: none;';
                    if (@$timeslips['break_time'] == 1) {
                        $showBreakStartAndEndTimer = '';
                    } ?>
                    <div class="break_time_detail row" style="<?php echo $showBreakStartAndEndTimer; ?>">
                        <div class="form-group col-md-6">
                            <label for="break_time_start" class="font-weight-bolder"> <?=lang('Timeslips.break_time_start');?> </label>
                            <input id="break_time_start" name="break_time_start" class="form-control timepicker" value="<?php echo @$timeslips['break_time_start']; ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="break_time_end" class="font-weight-bolder"> <?=lang('Timeslips.break_time_end');?> </label>
                            <input id="break_time_end" name="break_time_end" class="form-control timepicker" value="<?php echo @$timeslips['break_time_end']; ?>">
                            <span class="form-control-feedback text-danger" id="break_end_timer_error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slip_rate" class="font-weight-bolder"> <?=lang('Timeslips.slip_rate');?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">£</span>
                            </div>
                            <input id="slip_rate" name="slip_rate" class="form-control" type="number" step="0.01" value="<?php echo @$timeslips['slip_rate']; ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">per hour</span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="slip_timer_accumulated_seconds" name="slip_timer_accumulated_seconds" value="<?php echo @$timeslips['slip_timer_accumulated_seconds']; ?>">
                </div>
            </div>

            <div class="d-flex justify-content-end" style="gap: 10px;">
                <a href="/<?php echo strtolower($tableName).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''); ?>" type="button" class="btn btn-secondary">
                    <i class="fa fa-times"></i> <?php echo lang('Common.cancel');?>
                </a>
                <button type="submit" class="btn btn-primary timeslip-submit-btn">
                    <i class="fa fa-check"></i> <?php echo lang('Common.submit');?>
                </button>
            </div>
        </form>
    </div>
</div>


<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
    // Timer state
    let timerInterval = null;
    let timerSeconds = 0;
    let timerRunning = false;
    let timerPaused = false;

    $(document).ready(function() {
        // Initialize timer if there's existing accumulated seconds
        const existingSeconds = parseInt($("#slip_timer_accumulated_seconds").val()) || 0;
        if (existingSeconds > 0) {
            timerSeconds = existingSeconds;
            updateTimerDisplay();
        }

        // Timer controls
        $("#startTimer").click(function() {
            startTimer();
        });

        $("#pauseTimer").click(function() {
            pauseTimer();
        });

        $("#stopTimer").click(function() {
            stopTimer();
        });

        $("#resetTimer").click(function() {
            resetTimer();
        });

        // Quick time buttons
        $(".quick-time-btn").click(function() {
            const minutes = parseInt($(this).data('minutes'));
            addQuickTime(minutes);
        });

        // Set current time buttons
        $(".set-current-time-start").click(function() {
            setCurrentTime($("#slip_timer_started"));
            calculateTime();
        });

        $(".set-current-time-end").click(function() {
            setCurrentTime($("#slip_timer_end"));
            calculateTime();
        });

        // Calculate on time/date field changes
        $("#slip_timer_started").change(function() {
            if (!timerRunning) {
                calculateTime();
            }
        });

        $("#slip_timer_end").change(function() {
            if (!timerRunning) {
                calculateTime();
            }
        });

        $("#slip_end_date").change(function() {
            if (!timerRunning) {
                calculateTime();
            }
        });

        // Break time toggle
        $("#break_time").change(function() {
            if ($(this).is(':checked')) {
                $('.break_time_detail').slideDown('slow');
            } else {
                $('.break_time_detail').slideUp('slow');
            }
            if (!timerRunning) {
                calculateTime();
            }
        });

        // Calculate on break time changes
        $("#break_time_start").change(function() {
            if (!timerRunning) {
                calculateTime();
            }
        });

        $("#break_time_end").change(function() {
            if (!timerRunning) {
                calculateTime();
            }
        });

        // Validation
        $("#slip_end_date").change(function (evt) {
            const slipStartDate = document.getElementById("slip_start_date").value;
            const slipEndDate = evt.target.value;
            slipEndDateVarify(slipStartDate, slipEndDate, null);
        });

        $(".timeslip-submit-btn").click(function (e) {
            const slipStartDate = document.getElementById("slip_start_date").value;
            const slipEndDate = document.getElementById("slip_end_date").value;
            slipEndDateVarify(slipStartDate, slipEndDate, e);
            slipTimerVerify(e);
            const breakTime = $("#break_time").is(":checked");
            if (breakTime) {
                const endValue = $("#break_time_end").val();
                const startValue = $("#break_time_start").val();
                validateBreakeEnd(startValue, endValue, e);
            }
        });

        $("#slip_timer_end").focusout(function () {
            slipTimerVerify(null);
        });

        const breakTime = $("#break_time").is(":checked");
        if (breakTime) {
            $("#break_time_end").focusout(function () {
                const endValue = $(this).val();
                const startValue = $("#break_time_start").val();
                validateBreakeEnd(startValue, endValue, null);
            });
        }

        $("#slip_start_date").change(function() {
            var slip_start_date = $(this).val();
            var start_date = new Date(slip_start_date);
            var onejan = new Date(start_date.getFullYear(), 0, 1);
            var today = new Date(start_date.getFullYear(), start_date.getMonth(), start_date.getDate());
            var dayOfYear = ((today - onejan + 86400000) / 86400000);
            var week_no = Math.ceil(dayOfYear / 7);
            week_no = week_no > 52 ? 52 : week_no;
            $("#week_no").val(week_no);
            if (!timerRunning) {
                calculateTime();
            }
        });
    });

    function startTimer() {
        if (!timerRunning) {
            timerRunning = true;
            timerPaused = false;

            // Set start time and date if not set
            if (!$("#slip_timer_started").val()) {
                console.log('Setting start time...');
                setCurrentTime($("#slip_timer_started"));
            }
            if (!$("#slip_start_date").val()) {
                const startDate = formatDate(new Date());
                console.log('Setting start date:', startDate);
                $("#slip_start_date").val(startDate).trigger('change');
            }

            console.log('Timer started at:', $("#slip_timer_started").val(), $("#slip_start_date").val());

            timerInterval = setInterval(function() {
                timerSeconds++;
                updateTimerDisplay();
                updateHoursFromTimer();
                $("#slip_timer_accumulated_seconds").val(timerSeconds);
            }, 1000);

            $("#startTimer").hide();
            $("#pauseTimer").show();
            $("#stopTimer").show();
            $("#timerStatus").text("Running").parent().css("color", "#10b981");
        }
    }

    function pauseTimer() {
        if (timerRunning && !timerPaused) {
            clearInterval(timerInterval);
            timerPaused = true;
            timerRunning = false;
            $("#pauseTimer").html('<i class="fa fa-play"></i> Resume');
            $("#timerStatus").text("Paused").parent().css("color", "#f59e0b");
        } else if (timerPaused) {
            startTimer();
            $("#pauseTimer").html('<i class="fa fa-pause"></i> Pause');
        }
    }

    function stopTimer() {
        clearInterval(timerInterval);
        timerRunning = false;
        timerPaused = false;

        console.log('Stopping timer...');
        console.log('Timer seconds:', timerSeconds);

        // Set end time and date
        console.log('Setting end time...');
        setCurrentTime($("#slip_timer_end"));

        if (!$("#slip_end_date").val()) {
            const endDate = formatDate(new Date());
            console.log('Setting end date:', endDate);
            $("#slip_end_date").val(endDate).trigger('change');
        }

        // Update form fields - use timer-based hours
        updateHoursFromTimer();

        console.log('Timer stopped. End time:', $("#slip_timer_end").val(), 'End date:', $("#slip_end_date").val());
        console.log('Total hours:', $("#slip_hours").val());

        $("#startTimer").show();
        $("#pauseTimer").hide();
        $("#stopTimer").hide();
        $("#pauseTimer").html('<i class="fa fa-pause"></i> Pause');
        $("#timerStatus").text("Stopped").parent().css("color", "#ef4444");
    }

    function resetTimer() {
        clearInterval(timerInterval);
        timerSeconds = 0;
        timerRunning = false;
        timerPaused = false;
        updateTimerDisplay();
        updateHoursFromTimer();
        $("#slip_timer_accumulated_seconds").val(0);
        $("#startTimer").show();
        $("#pauseTimer").hide();
        $("#stopTimer").hide();
        $("#pauseTimer").html('<i class="fa fa-pause"></i> Pause');
        $("#timerStatus").text("Ready to Start").parent().css("color", "white");
    }

    function updateTimerDisplay() {
        const hours = Math.floor(timerSeconds / 3600);
        const minutes = Math.floor((timerSeconds % 3600) / 60);
        const seconds = timerSeconds % 60;

        const display =
            (hours < 10 ? "0" + hours : hours) + ":" +
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);

        $("#timerDisplay").text(display);
    }

    function updateHoursFromTimer() {
        // Convert timer seconds to hours in decimal format (HH.MM)
        const totalMinutes = Math.floor(timerSeconds / 60);
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        const decimalHours = `${hours}.${minutes.toString().padStart(2, '0')}`;

        console.log('Updating hours from timer:', timerSeconds, 'seconds =', decimalHours, 'hours');
        $("#slip_hours").val(decimalHours);
    }

    function formatDate(date) {
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    function addQuickTime(minutes) {
        timerSeconds += (minutes * 60);
        updateTimerDisplay();
        updateHoursFromTimer();
        $("#slip_timer_accumulated_seconds").val(timerSeconds);
    }

    function setCurrentTime(inputElement) {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const seconds = now.getSeconds();
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12 || 12;
        const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;
        const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;
        const formattedTime = `${hours}:${formattedMinutes}:${formattedSeconds} ${ampm}`;

        // Update the value and trigger change event
        inputElement.val(formattedTime).trigger('change');

        console.log('Set time:', formattedTime, 'to', inputElement.attr('id'));
    }

    function calculateTime() {
        var startDate = $("#slip_start_date").val();
        var startTime = $("#slip_timer_started").val();
        var endDate = $("#slip_end_date").val();
        var endTime = $("#slip_timer_end").val();
        const breakTime = $("#break_time").is(":checked");

        if (!startDate || !startTime || !endDate || !endTime) return;

        const startDatetimeString = startDate + ' ' + startTime;
        const endDatetimeString = endDate + ' ' + endTime;
        const startDateObj = new Date(startDatetimeString);
        const endDateObj = new Date(endDatetimeString);
        const timeDifferenceMs = endDateObj - startDateObj;
        const timeSeconds = Math.floor(timeDifferenceMs / 1000);
        const timeMinutes = Math.floor(timeSeconds / 60);
        const timeHours = Math.floor(timeMinutes / 60);
        let diffInHours = `${timeHours}.${timeMinutes % 60}`;

        if (breakTime) {
            const breakTimeStVal = $("#break_time_start").val();
            const breakTimeEndVal = $("#break_time_end").val();
            if (breakTimeStVal && breakTimeEndVal) {
                const breakStartTime = new Date(`2000-01-01 ${breakTimeStVal}`);
                const breakEndTime = new Date(`2000-01-01 ${breakTimeEndVal}`);
                const timeDifferenceMillis = breakEndTime - breakStartTime;
                const seconds = Math.floor(timeDifferenceMillis / 1000);
                const minutes = Math.floor(seconds / 60);
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;

                const [totalhours, totalminutes] = diffInHours.split('.').map(parseFloat);
                let newHours = totalhours - hours;
                let newMinutes = (totalminutes || 0) - remainingMinutes;
                if (Math.sign(newMinutes) === -1) {
                    newHours = newHours - 1;
                    newMinutes = 60 + newMinutes;
                }
                diffInHours = `${newHours}.${newMinutes}`;
            }
        }

        $('#slip_hours').val(diffInHours);
    }

    function slipEndDateVarify(slipStartDate, slipEndDate, evt) {
        const endDate = new Date(slipEndDate);
        const startDate = new Date(slipStartDate);
        const timeDifference = endDate - startDate;
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

            if (endedTime <= startedTime) {
                $("#end_timer_error").text("Slip end time should be greater than the slip start time.");
                if (evt !== null) {
                    evt.preventDefault();
                }
                return false;
            } else {
                $("#end_timer_error").text("");
            }
        }
    }

    function validateBreakeEnd(startValue, endValue, evt) {
        if (!startValue || !endValue) return true;

        const endedTime = new Date(`1970-01-01 ${endValue}`);
        const startedTime = new Date(`1970-01-01 ${startValue}`);

        if (endedTime <= startedTime) {
            $("#break_end_timer_error").text("Break end time should be greater than the Break start time.");
            if (evt !== null) {
                evt.preventDefault();
            }
            return false;
        } else {
            $("#break_end_timer_error").text("");
        }
    }
</script>

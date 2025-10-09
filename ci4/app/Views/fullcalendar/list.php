<?php require_once(APPPATH . 'Views/fullcalendar/list-title.php'); ?>

<style>
    /* JIRA-style Calendar Enhancements */
    .calendar-wrapper {
        background: var(--bg-primary, #ffffff);
        border-radius: var(--radius-lg, 12px);
        padding: 24px;
        box-shadow: var(--shadow-md, 0 2px 8px rgba(0,0,0,0.08));
    }

    #calendar {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    /* FullCalendar Header Styling */
    .fc-header-toolbar {
        padding: 16px 0;
        margin-bottom: 20px !important;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: var(--gray-800, #1f2937) !important;
    }

    .fc-button {
        background: var(--primary, #667eea) !important;
        border: none !important;
        border-radius: var(--radius-md, 8px) !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        text-transform: capitalize !important;
        transition: all 0.2s ease !important;
    }

    .fc-button:hover {
        background: var(--primary-dark, #5a67d8) !important;
        transform: translateY(-1px);
    }

    .fc-button:active {
        transform: translateY(0);
    }

    .fc-button-active {
        background: var(--primary-dark, #5a67d8) !important;
    }

    /* Calendar Grid */
    .fc-daygrid-day {
        transition: background 0.2s ease;
    }

    .fc-daygrid-day:hover {
        background: var(--gray-50, #f9fafb) !important;
    }

    .fc-col-header-cell {
        background: var(--gray-50, #f9fafb) !important;
        padding: 12px 8px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
        color: var(--gray-600, #4b5563) !important;
    }

    .fc-daygrid-day-number {
        font-weight: 600 !important;
        color: var(--gray-700, #374151) !important;
        padding: 8px !important;
    }

    .fc-day-today {
        background: rgba(102, 126, 234, 0.08) !important;
    }

    .fc-day-today .fc-daygrid-day-number {
        background: var(--primary, #667eea) !important;
        color: white !important;
        border-radius: 50% !important;
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Calendar Events */
    .fc-event {
        border: none !important;
        border-radius: var(--radius-sm, 4px) !important;
        padding: 4px 8px !important;
        margin: 2px 4px !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3) !important;
    }

    .fc-event-title {
        font-weight: 600 !important;
    }

    /* Popup Form Styling */
    .new-event {
        background: var(--bg-primary, #ffffff) !important;
        border-radius: var(--radius-lg, 12px) !important;
        box-shadow: var(--shadow-xl, 0 8px 24px rgba(0,0,0,0.15)) !important;
        padding: 24px !important;
        border: 2px solid var(--primary, #667eea) !important;
        min-width: 400px !important;
    }

    .new-event h5 {
        color: var(--gray-800, #1f2937) !important;
        font-size: 1.125rem !important;
        font-weight: 700 !important;
        margin-bottom: 20px !important;
        padding-bottom: 12px !important;
        border-bottom: 2px solid var(--gray-100, #f3f4f6) !important;
    }

    .new-event h5 .date {
        color: var(--primary, #667eea) !important;
    }

    .new-event .close-pop {
        position: absolute !important;
        right: 16px !important;
        top: 16px !important;
        color: var(--gray-400, #9ca3af) !important;
        font-size: 1.25rem !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .new-event .close-pop:hover {
        color: var(--gray-700, #374151) !important;
        transform: rotate(90deg);
    }

    .new-event .form-group {
        margin-bottom: 16px !important;
    }

    .new-event label,
    .new-event .form-group > div:first-of-type {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: var(--gray-700, #374151) !important;
        margin-bottom: 6px !important;
    }

    .new-event .form-control {
        border: 1px solid var(--border-medium, #d1d5db) !important;
        border-radius: var(--radius-md, 8px) !important;
        padding: 8px 12px !important;
        font-size: 0.875rem !important;
        transition: all 0.2s ease !important;
    }

    .new-event .form-control:focus {
        outline: none !important;
        border-color: var(--primary, #667eea) !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15) !important;
    }

    .new-event .btn-primary {
        background: var(--primary, #667eea) !important;
        border: none !important;
        border-radius: var(--radius-md, 8px) !important;
        padding: 10px 24px !important;
        font-weight: 600 !important;
        transition: all 0.2s ease !important;
    }

    .new-event .btn-primary:hover {
        background: var(--primary-dark, #5a67d8) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3) !important;
    }

    .new-event .required {
        color: #ef4444 !important;
        font-weight: 700 !important;
    }

    /* Popup Pointer Arrow */
    .pointer .arrow {
        border-top: 12px solid var(--primary, #667eea) !important;
    }

    .pointer .arrow_border {
        border-top: 14px solid var(--primary, #667eea) !important;
    }

    /* Time Grid View Styling */
    .fc-timegrid-slot {
        height: 3em !important;
    }

    .fc-timegrid-slot-label {
        font-weight: 600 !important;
        color: var(--gray-600, #4b5563) !important;
    }

    /* List View Styling */
    .fc-list-event {
        transition: background 0.2s ease !important;
    }

    .fc-list-event:hover {
        background: var(--gray-50, #f9fafb) !important;
    }

    .fc-list-event-dot {
        background: var(--primary, #667eea) !important;
        border-color: var(--primary, #667eea) !important;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .new-event {
            min-width: 300px !important;
            max-width: 90vw !important;
        }

        .fc-toolbar-title {
            font-size: 1.125rem !important;
        }
    }
</style>

<script>

    var calendar;
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap',
            height: 'auto',
            expandRows: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            initialView: 'dayGridMonth',
            initialDate: '<?php echo render_date(time(), "", "Y-m-d"); ?>',
            navLinks: true, // can click day/week names to navigate views
            editable: true,
            selectable: true,
            nowIndicator: true,
            dayMaxEvents: true, // allow "more" link when too many events
            events: [
                <?php foreach ($timeslips as $eachSlip) {
                    $startDate = strtotime(trim($eachSlip['slip_start_date'] . ' ' . $eachSlip['slip_timer_started']));
                    $endDate = strtotime(trim($eachSlip['slip_end_date'] . ' ' . $eachSlip['slip_timer_end']));
                    // $splitted = explode(" ", $eachSlip['slip_timer_started']);
                    $titleStartDateHour = getTitleHour($eachSlip['slip_timer_started']);
                    $titleEndDateHour = getTitleHour($eachSlip['slip_timer_end']);
                    echo "{
                    id: '" . $eachSlip['id'] . "',
                    title: '" . "{" . render_date($startDate, "", "d-M") . " " . $titleStartDateHour . " - " . render_date($endDate, "", "d-M") . " " . $titleEndDateHour . "} " . $eachSlip['employee_name'] . ": " . $eachSlip['task_name'] . "',
                    start: '" . render_date($startDate, "", "Y-m-d H:i:s") . "',
                    end: '" . render_date($endDate, "", "Y-m-d H:i:s") . "',
                    url: '" . base_url('/' . $tableName . '/edit/' . $eachSlip['uuid']) . "',
                    allDay: true,
                },";
                } ?>
            ],
            dateClick: function (dateEventObj) {
                const weekNumber = getWeekNumber(dateEventObj.date);
                document.getElementById("week-number").value = weekNumber;
                
                var date = dateEventObj.date;
                var jsEvent = dateEventObj.jsEvent;
                var s_time = '';
                if (dateEventObj.view.type != "dayGridMonth") {
                    var date_arr = String(date).split(" ");
                    var timestring = '';
                    for (var i = 0; i < date_arr.length; i++) {
                        var contains = (date_arr[i].indexOf(":") > -1);
                        if (contains) {
                            timestring = date_arr[i];
                            break;
                        }
                    }
                    var time_arr = timestring.split(":");
                    if (time_arr[0] > 12) { var h = time_arr[0] - 12; s_time = h + ":" + time_arr[1] + "pm"; }
                    else { s_time = time_arr[0] + ":" + time_arr[1] + "am"; }
                }

                var cal_left = Number($('#calendar').offset().left);
                var cal_top = Number($('#calendar').offset().top);

                var left = jsEvent.pageX - cal_left - (Number($(".new-event").outerHeight()) / 2);
                var top = jsEvent.pageY - cal_top - Number($(".new-event").outerHeight()) - Number($(".arrow_border").outerHeight());

                if (left < 0) {
                    left = 0;
                } else if (left > (Number($('#calendar').outerWidth()) - Number($(".new-event").outerWidth()))) {
                    left = Number($('#calendar').outerWidth()) - Number($(".new-event").outerWidth());
                }
                if (top < 0) {
                    top = 0;
                }
                $(".new-event").css('left', left);
                $(".new-event").css('top', top);


                var converted = days[date.getDay()] + ", " + date.getDate() + " " + months[date.getMonth()];
                var curr_month = Number(date.getMonth()) + 1;
                var curr_date = date.getDate() + '/' + curr_month + '/' + date.getFullYear();

                $(".new-event").find('.date').html(converted);
                $("#curr_date").val(curr_date);
                $("#slip_timer_started").val(s_time);
                $("#slip_timer_end").val();
                $("select#task_name").val('');
                $("select#employee_name").val('');
                $("#slip_description").val('');
                $(".new-event").fadeIn("fast");
            }
        });

        calendar.render();

        $(".popup .close-pop").click(function () {
            $(".new-event").fadeOut("fast");
        });

        $("#slip_timer_end").focusout(function () {
            const startTime = $("#slip_timer_started").val();
            const endTime = $(this).val();
            validateEndTimer(startTime, endTime, null);
        })
    });

    // Function to get the week number
    function getWeekNumber(date) {
        // Copy date so we don't modify the original
        date = new Date(date);

        // Set to Monday of the current week
        date.setHours(0, 0, 0, 0);
        date.setDate(date.getDate() + 4 - (date.getDay() || 7));

        // Get the year for the current date
        const year = date.getFullYear();

        // Get the first day of the year
        const firstDay = new Date(year, 0, 1);

        // Calculate the week number
        const weekNumber = Math.ceil((((date - firstDay) / 86400000) + 1) / 7);

        return weekNumber;
    }

    function validateEndTimer(startTime, endTime, evt) {
        const endedTime = new Date(`1970-01-01 ${endTime}`);
        const startedTime = new Date(`1970-01-01 ${startTime}`);

        // Check if the first time is greater than the second time
        if (endedTime <= startedTime) {
            alert("Slip end time should be greater than the Slip start time.");
            $("#slip_timer_end").val("");
            return false;
        }
    }

    var new_event = new Array();


    function ValidateForm() {

        if (document.frm_task.task_name.value == "") {
            alert('Please Specify Task Name');
            document.frm_task.task_name.focus();
            return false;
        }

        if (document.frm_task.employee_name.value == "") {
            alert('Please specify employee name');
            document.frm_task.employee_name.focus();
            return false;
        }
        if (document.frm_task.slip_timer_started.value == "") {
            alert('Please Specify Start Time');
            document.frm_task.slip_timer_started.focus();
            return false;
        }

        if (document.frm_task.slip_timer_end.value == "") {
            alert('Please Specify End Time');
            document.frm_task.slip_timer_end.focus();
            return false;
        } else {
            const startTime = document.frm_task.slip_timer_started.value;
            const endTime = document.frm_task.slip_timer_end.value;
            validateEndTimer(startTime, endTime);
        }

        if (document.frm_task.slip_description.value == "") {
            alert('Please Specify Timeslip Description');
            document.frm_task.slip_description.focus();
            return false;
        }

        save();
    }

    function save() {

        var date = $("#curr_date").val();
        var s_time = $("#slip_timer_started").val();
        var e_time = $("#slip_timer_end").val();
        var task_id = $("#task_name").val();
        var emp_id = $("#employee_name").val();
        var descr = $("#slip_description").val();
        var weekNumber = $("#week-number").val();

        $.ajax({
            type: "GET",
            url: baseURL + "timeslips/savecalenderevent",
            data: {
                date: date,
                s_time: s_time,
                e_time: e_time,
                task_id: task_id,
                emp_id: emp_id,
                descr: descr,
                week_no: weekNumber
            },
            dataType: "json",
            cache: false,
            method: 'POST',
            success: function (response) {
                var obj = {};
                obj['id'] = response.uuid;
                obj['title'] = response.title;
                obj['start'] = response.start;
                obj['end'] = response.end;
                obj['url'] = baseURL + 'timeslips/edit/' + response.uuid;
                obj['allDay'] = true;
                console.log(obj);

                console.log(calendar);
                calendar.addEvent(obj)
                // $('#calendar').fullCalendar( 'removeEventSource', new_event );
                // $('#calendar').fullCalendar( 'addEventSource', new_event );
                $(".new-event").fadeOut("fast");
            }
        });

    }

    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

</script>

<div class="calendar-wrapper">
    <div id='calendar'></div>
    <div class="new-event popup" style="display:none">
        <div class="pointer">
            <div class="arrow"></div>
            <div class="arrow_border"></div>
        </div>
        <i class="close-pop fa fa-times"></i>
        <h5>Date <span class="date"></span></h5>
        <form name="frm_task" class="form-horizontal col-sm-12 col-lg-12">
            <input type="hidden" value="" name="curr_date" id="curr_date">
            <div class="form-group">
                Task Name <sup class="required">*</sup>
                <div class="ui-widget">
                    <select name="task_name" id="task_name" class="form-control dashboard-dropdown">
                        <option value="">--Select--</option>
                        <?php foreach ($tasks as $task) { ?>
                            <option value="<?php echo $task['id'] ?>"><?php echo $task['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                Employee Name <sup class="required">*</sup>
                <div class="ui-select">
                    <select name="employee_name" id="employee_name" class="form-control dashboard-dropdown">
                        <option value="">--Select--</option>
                        <?php foreach ($employees as $employee) { ?>
                            <option value="<?php echo $employee['id'] ?>"><?php echo $employee['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                Start Time <sup class="required">*</sup>
                <input type="text" class="event-input timepicker form-control" style="margin-left:0px;"
                    name="slip_timer_started" id="slip_timer_started">
                End Time <sup class="required">*</sup>
                <input type="text" class="event-input timepicker form-control" style="margin-left:0px;"
                    name="slip_timer_end" id="slip_timer_end">
            </div>
            <div class="form-group">
                Description <sup class="required">*</sup>
                <textarea name="slip_description" id="slip_description" rows="5" cols="10"
                    class="event-input form-control"></textarea>
            </div>
            <input type="hidden" name="week-number" id="week-number">

            <button type="button" class="btn btn-primary btn-color margin-right-5 btn-sm"
                onclick="return ValidateForm();">
                Create
            </button>
        </form>
    </div>
</div>
<script>
    function validateForm() {
        alert('validation');
    }
</script>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
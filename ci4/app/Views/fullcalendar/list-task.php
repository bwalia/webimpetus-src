<?php require_once(APPPATH . 'Views/fullcalendar/list-title-task.php'); ?>
<script>
    var calendar;
    document.addEventListener('DOMContentLoaded', function() {
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
            navLinks: true,
            editable: true,
            selectable: true,
            nowIndicator: true,
            dayMaxEvents: true,
            events: [
                <?php foreach ($tasks as $task) {
                    echo "{
                    id: '" . $task['id'] . "',
                    title: '" . $task['name'] . "',
                    start: '" . date('Y-m-d', $task['start_date']) . "',
                    end: '" . date('Y-m-d', $task['end_date']) . "',
                    url: '" . base_url('/' . $tableName . '/edit/' . $task['id']) . "',
                    allDay: true,
                    color: '" . ($task['active'] == 1 ? '#FFA500' : '#4cbb17') . "',
                    textColor: '#000000',
                },";
                } ?>
            ],
        });

        calendar.render();

        $(".popup .close-pop").click(function() {
            $(".new-event").fadeOut("fast");
        });
    });
</script>

<div class="calendar-wrapper">
    <div id='calendar'></div>
</div>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
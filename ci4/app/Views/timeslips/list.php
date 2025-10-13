<?php require_once (APPPATH . 'Views/timeslips/list-title.php'); ?>
<style>
    /* Timeslips JIRA-style enhancements */
    .column-wrap {
        width: 1%;
        white-space: nowrap;
    }

    /* Filter section styling */
    .white_card_header {
        background: var(--bg-primary, #ffffff);
        padding: 20px;
        border-bottom: 2px solid var(--gray-100, #f3f4f6);
    }

    .white_card_header .form-control {
        border: 1px solid var(--border-medium, #d1d5db);
        border-radius: var(--radius-md, 8px);
        padding: 8px 12px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .white_card_header .form-control:focus {
        outline: none;
        border-color: var(--primary, #667eea);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }

    /* Total records badge */
    .page_title_right span#total {
        background: var(--primary, #667eea);
        color: white;
        padding: 4px 12px;
        border-radius: var(--radius-md, 8px);
        font-weight: 700;
        margin-left: 8px;
    }

    /* DataTable wrapper */
    #tableWrapper {
        padding: 20px;
    }

    /* Enhanced table styling */
    .QA_table table {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .QA_table thead th {
        background: var(--gray-50, #f9fafb) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.5px !important;
        color: var(--gray-600, #4b5563) !important;
        padding: 12px 16px !important;
    }

    .QA_table tbody td {
        padding: 12px 16px !important;
        font-size: 0.875rem !important;
        color: var(--gray-700, #374151) !important;
    }

    .QA_table tbody tr:hover {
        background: var(--gray-50, #f9fafb) !important;
    }

    /* Override summary card styles to match sales_invoices exactly */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: var(--bg-primary, #ffffff) !important;
        border-radius: var(--radius-lg, 8px) !important;
        padding: 24px !important;
        box-shadow: var(--shadow-md, 0 4px 8px rgba(9, 30, 66, 0.15)) !important;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: none !important;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px !important;
        width: 100% !important;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }

    .summary-card.blue::before {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    }

    .summary-card.green::before {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    .summary-card.orange::before {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }

    .summary-card.purple::before {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg, 0 8px 16px rgba(9, 30, 66, 0.2)) !important;
    }

    .summary-card-title {
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: var(--gray-600, #6b7280) !important;
        margin-bottom: 12px !important;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: none !important;
        letter-spacing: normal !important;
    }

    .summary-card-value {
        font-size: 2rem !important;
        font-weight: 700 !important;
        color: var(--gray-900, #111827) !important;
        line-height: 1 !important;
        margin-bottom: 8px !important;
    }

    .summary-card-subtitle {
        font-size: 0.75rem !important;
        color: var(--gray-500, #6b7280) !important;
        margin-top: 0 !important;
    }
</style>

<!-- Summary Cards for Timeslips -->
<div class="white_card_body">
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-card-title"><i class="fa fa-clock"></i> This Week</div>
            <div class="summary-card-value" id="totalHoursWeek">0.0</div>
            <div class="summary-card-subtitle">hours logged</div>
        </div>

        <div class="summary-card green">
            <div class="summary-card-title"><i class="fa fa-calendar-check"></i> This Month</div>
            <div class="summary-card-value" id="totalHoursMonth">0.0</div>
            <div class="summary-card-subtitle">hours logged</div>
        </div>

        <div class="summary-card purple">
            <div class="summary-card-title"><i class="fa fa-users"></i> Active Employees</div>
            <div class="summary-card-value" id="activeEmployees">0</div>
            <div class="summary-card-subtitle">tracking time</div>
        </div>

        <div class="summary-card orange">
            <div class="summary-card-title"><i class="fa fa-tasks"></i> Active Tasks</div>
            <div class="summary-card-value" id="activeTasks">0</div>
            <div class="summary-card-subtitle">in progress</div>
        </div>
    </div>
</div>

<div class="white_card_body" id="tableWrapper">
    <div class="QA_table" id="timeslipsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/timeslips/footer.php'); ?>
<script>

    let columnsTitle = ['Id', 'Week No.', 'Task', 'Employee', 'Start Date', 'Start Time'];
    let columnsMachineName = ['id', 'week_no', 'taskName', 'employeeName', 'slip_start_date', 'slip_timer_started'];
    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "timeslips",
            apiPath: "api/v2/timeslips",
            selector: "timeslipsTable"
        }
    );

    $("#list_week").on('change', function () {
        let listMonth = $("#list_monthpicker2").val();
        let listYear = $("#list_yearpicker2").val();
        $("#timeslipsTable").remove("");
        $("#tableWrapper").html('<div class="QA_table" id="timeslipsTable"></div>');
        window.sessionStorage.setItem("listWeek", $(this).val());
        setTimeout(() => {
            initializeGridTable(
                {
                    columnsTitle,
                    columnsMachineName,
                    tableName: "timeslips",
                    apiPath: "api/v2/timeslips",
                    selector: "timeslipsTable",
                    listWeek: $(this).val(),
                    listMonth: listMonth,
                    listYear: listYear
                }
            );
        }, 600);
    });

    $("#list_monthpicker2").on('change', function () {
        let listWeek = $("#list_week").val() || "";
        let listYear = $("#list_yearpicker2").val();
        $("#timeslipsTable").remove("");
        $("#tableWrapper").html('<div class="QA_table" id="timeslipsTable"></div>');
        window.sessionStorage.setItem("listMonth", $(this).val());
        setTimeout(() => {
            initializeGridTable(
                {
                    columnsTitle,
                    columnsMachineName,
                    tableName: "timeslips",
                    apiPath: "api/v2/timeslips",
                    selector: "timeslipsTable",
                    listWeek: listWeek,
                    listMonth: $(this).val(),
                    listYear: listYear
                }
            );
        }, 600);
    });

    $("#list_yearpicker2").on('change', function () {
        let listWeek = $("#list_week").val() || "";
        let listMonth = $("#list_monthpicker2").val();
        $("#timeslipsTable").remove("");
        $("#tableWrapper").html('<div class="QA_table" id="timeslipsTable"></div>');
        window.sessionStorage.setItem("listYear", $(this).val());
        setTimeout(() => {
            initializeGridTable(
                {
                    columnsTitle,
                    columnsMachineName,
                    tableName: "timeslips",
                    apiPath: "api/v2/timeslips",
                    selector: "timeslipsTable",
                    listWeek: listWeek,
                    listMonth: listMonth,
                    listYear: $(this).val()
                }
            );
        }, 600);
    });
    let listMonth = window.sessionStorage.getItem("listMonth");
    let listYear = window.sessionStorage.getItem("listYear");
    console.log({listMonth});
    $('#list_monthpicker2 option[value="'+ listMonth +'"]').attr("selected",true);
    $('#list_yearpicker2 option[value="'+ listYear +'"]').attr("selected",true);

    // Update summary cards with timeslip metrics
    function updateTimeslipSummaryCards() {
        const businessUuid = '<?php echo session('uuid_business'); ?>';
        const token = '<?php echo session('token') ?? ''; ?>';

        // Fetch timeslips data and calculate summaries
        fetch('/api/v2/timeslips?uuid_business_id=' + businessUuid, {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result && result.data) {
                    calculateTimeslipMetrics(result.data);
                }
            })
            .catch(error => {
                console.error('Error fetching timeslip summary data:', error);
            });
    }

    function calculateTimeslipMetrics(timeslips) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Calculate week start (Monday)
        const dayOfWeek = today.getDay();
        const diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
        const weekStart = new Date(today.setDate(diff));
        weekStart.setHours(0, 0, 0, 0);

        // Calculate month start
        const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
        monthStart.setHours(0, 0, 0, 0);

        let totalHoursWeek = 0;
        let totalHoursMonth = 0;
        const uniqueEmployees = new Set();
        const uniqueTasks = new Set();

        timeslips.forEach(function(slip) {
            const slipDate = slip.slip_start_date ? new Date(slip.slip_start_date) : null;

            if (!slipDate) return;

            slipDate.setHours(0, 0, 0, 0);

            // Calculate hours (assuming duration is in minutes or hours)
            let hours = 0;
            if (slip.total_hours) {
                hours = parseFloat(slip.total_hours);
            } else if (slip.duration) {
                // If duration is in minutes, convert to hours
                hours = parseFloat(slip.duration) / 60;
            }

            // This week
            if (slipDate >= weekStart) {
                totalHoursWeek += hours;
            }

            // This month
            if (slipDate >= monthStart) {
                totalHoursMonth += hours;
            }

            // Track unique employees and tasks
            if (slip.employeeName || slip.employee_name) {
                uniqueEmployees.add(slip.employeeName || slip.employee_name);
            }
            if (slip.taskName || slip.task_name) {
                uniqueTasks.add(slip.taskName || slip.task_name);
            }
        });

        // Update summary cards
        $('#totalHoursWeek').text(totalHoursWeek.toFixed(1));
        $('#totalHoursMonth').text(totalHoursMonth.toFixed(1));
        $('#activeEmployees').text(uniqueEmployees.size);
        $('#activeTasks').text(uniqueTasks.size);

        console.log('Timeslip metrics updated:', {
            totalHoursWeek: totalHoursWeek,
            totalHoursMonth: totalHoursMonth,
            activeEmployees: uniqueEmployees.size,
            activeTasks: uniqueTasks.size
        });
    }

    // Update summary cards on page load
    $(document).ready(function() {
        setTimeout(function() {
            updateTimeslipSummaryCards();
        }, 1000);
    });
</script>
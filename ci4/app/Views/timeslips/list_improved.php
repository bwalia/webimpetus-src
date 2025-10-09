<?php require_once (APPPATH . 'Views/timeslips/list-title.php'); ?>
<style>
    .column-wrap {
        width: 1%;
        white-space: nowrap;
    }

    .timeslips-summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        color: white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    .summary-card.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .summary-card.orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .summary-card.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .summary-card-title {
        font-size: 0.875rem;
        font-weight: 500;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .summary-card-value {
        font-size: 1.875rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
    }

    .summary-card-subtitle {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-top: 5px;
    }

    .quick-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .quick-action-btn.primary {
        background-color: #667eea;
        color: white;
    }

    .quick-action-btn.success {
        background-color: #10b981;
        color: white;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-sla {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-chargeable {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-billed {
        background-color: #fef3c7;
        color: #92400e;
    }
</style>

<!-- Summary Cards -->
<div class="timeslips-summary-cards">
    <div class="summary-card">
        <div class="summary-card-title"><i class="fa fa-calendar-week"></i> This Week</div>
        <div class="summary-card-value" id="weekHours">0.00</div>
        <div class="summary-card-subtitle">hours logged</div>
    </div>

    <div class="summary-card green">
        <div class="summary-card-title"><i class="fa fa-calendar"></i> This Month</div>
        <div class="summary-card-value" id="monthHours">0.00</div>
        <div class="summary-card-subtitle">hours logged</div>
    </div>

    <div class="summary-card orange">
        <div class="summary-card-title"><i class="fa fa-money-bill-wave"></i> Billable</div>
        <div class="summary-card-value" id="billableHours">0.00</div>
        <div class="summary-card-subtitle">hours to bill</div>
    </div>

    <div class="summary-card blue">
        <div class="summary-card-title"><i class="fa fa-clock"></i> Today</div>
        <div class="summary-card-value" id="todayHours">0.00</div>
        <div class="summary-card-subtitle">hours logged</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="/timeslips/edit" class="quick-action-btn success">
        <i class="fa fa-plus"></i> New Timeslip
    </a>
    <button class="quick-action-btn primary" onclick="window.location.reload()">
        <i class="fa fa-sync"></i> Refresh
    </button>
</div>

<div class="white_card_body" id="tableWrapper">
    <div class="QA_table" id="timeslipsTable"></div>
</div>

<?php require_once (APPPATH . 'Views/timeslips/footer.php'); ?>
<script>

    let columnsTitle = ['ID', 'Week', 'Task', 'Employee', 'Date', 'Start Time', 'Hours', 'Status'];
    let columnsMachineName = ['id', 'week_no', 'taskName', 'employeeName', 'slip_start_date', 'slip_timer_started', 'slip_hours', 'billing_status'];

    // Custom column renderer for better display
    const columnRenderers = {
        slip_hours: function(data, type, row) {
            return '<span style="font-weight: 600; color: #374151;">' + (data || '0.00') + ' hrs</span>';
        },
        billing_status: function(data, type, row) {
            let badgeClass = 'status-sla';
            if (data === 'chargeable') badgeClass = 'status-chargeable';
            if (data === 'Billed') badgeClass = 'status-billed';
            return '<span class="status-badge ' + badgeClass + '">' + (data || 'SLA') + '</span>';
        },
        slip_start_date: function(data, type, row) {
            if (!data) return '';
            // Format date nicely
            const date = new Date(data);
            const options = { month: 'short', day: 'numeric', year: 'numeric' };
            return date.toLocaleDateString('en-GB', options);
        }
    };

    initializeGridTable(
        {
            columnsTitle,
            columnsMachineName,
            tableName: "timeslips",
            apiPath: "api/v2/timeslips",
            selector: "timeslipsTable",
            columnRenderers: columnRenderers
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
                    listYear: listYear,
                    columnRenderers: columnRenderers
                }
            );
            updateSummaryCards();
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
                    listYear: listYear,
                    columnRenderers: columnRenderers
                }
            );
            updateSummaryCards();
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
                    listYear: $(this).val(),
                    columnRenderers: columnRenderers
                }
            );
            updateSummaryCards();
        }, 600);
    });

    let listMonth = window.sessionStorage.getItem("listMonth");
    let listYear = window.sessionStorage.getItem("listYear");
    console.log({listMonth});
    $('#list_monthpicker2 option[value="'+ listMonth +'"]').attr("selected",true);
    $('#list_yearpicker2 option[value="'+ listYear +'"]').attr("selected",true);

    // Load summary data
    function updateSummaryCards() {
        // You can implement an API call here to fetch summary data
        // For now, we'll use placeholder values
        const businessUuid = '<?php echo $uuid_business; ?>';
        const token = '<?php echo $token; ?>';

        // Fetch timeslips data and calculate summaries
        fetch('/api/v2/timeslips?uuid_business_id=' + businessUuid, {
            headers: {
                'Authorization': 'Bearer ' + token
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.data) {
                calculateSummaries(data.data);
            }
        })
        .catch(error => console.error('Error fetching summary data:', error));
    }

    function calculateSummaries(timeslips) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const weekStart = new Date(today);
        weekStart.setDate(today.getDate() - today.getDay());
        const monthStart = new Date(now.getFullYear(), now.getMonth(), 1);

        let todayHours = 0;
        let weekHours = 0;
        let monthHours = 0;
        let billableHours = 0;

        timeslips.forEach(slip => {
            const hours = parseFloat(slip.slip_hours) || 0;
            const slipDate = new Date(slip.slip_start_date * 1000);

            if (slipDate >= today) {
                todayHours += hours;
            }
            if (slipDate >= weekStart) {
                weekHours += hours;
            }
            if (slipDate >= monthStart) {
                monthHours += hours;
            }
            if (slip.billing_status === 'chargeable') {
                billableHours += hours;
            }
        });

        $('#todayHours').text(todayHours.toFixed(2));
        $('#weekHours').text(weekHours.toFixed(2));
        $('#monthHours').text(monthHours.toFixed(2));
        $('#billableHours').text(billableHours.toFixed(2));
    }

    // Initial load
    setTimeout(updateSummaryCards, 1000);
</script>

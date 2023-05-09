    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
      tr:nth-child(even) {
        background-color: #f2f2f2;
      }
    </style>
    <hr class="line-title">
    <table border="0" cellpadding="5" style="font-family: Arial; font-size: 13px; width:100%;">
      <thead></thead>
      <tbody>
        <tr>
          <td>
            <h1><b>MONTHLY TIMESHEET </b> </h1>
          </td>
          <td></td>
          <td></td>
          <td></td>

        </tr>

        <tr>
          <td> Project name</td>
          <td>HD4D</td>
          <td>Timeslip date</td>
          <td><?php echo date("d-m-Y", time()); ?></td>

        </tr>
        <tr>
          <td> Consultant Name</td>
          <td><?php echo $employeeData->first_name . " " . $employeeData->surname; ?></td>
          <td>Timeslip period</td>
          <td><?php echo date("d-m-Y", strtotime("+30 days")); ?></td>

        </tr>
        <tr>
          <td>Consultant Email </td>
          <td><?php echo $employeeData->email; ?></td>
          <td> </td>
          <td> </td>
        </tr>
        <tr>

        </tr>

      </tbody>

    </table>
    <hr class="line-title">
    <table style="font-family: Arial; font-size: 13px; width:100%;" class="table table-striped ">
      <thead>
        <tr style="background-color: #c0c0c0;">
          <th align="left" width="5%"> Week </th>
          <th align="left" width="10%"> Task Name </th>
          <th align="left" width="20%"> Employee Name </th>
          <th align="left" width="10%"> Date</th>
          <th align="left" width="10%"> Start</th>
          <th align="left" width="10%"> End</th>
          <th align="left" width="30%"> Description</th>
          <th align="left" width="5%"> Hours</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $total_hours = 0;
        foreach ($timeslips as $timeslip) {
          $total_hours += $timeslip["slip_hours"];
        ?>

          <tr>
            <td>
              <?= $timeslip["week_no"];  ?>
            </td>

            <td>
              <?= $timeslip["name_of_task"]; ?>
            </td>

            <td>
              <?= $timeslip["employee_first_name"] . " " . $timeslip["employee_surname"]; ?>
            </td>

            <td>
              <?= date("d-m-Y", $timeslip["slip_start_date"]); ?>
            </td>
            <td>
              <?= $timeslip["slip_timer_started"]; ?>
            </td>

            <td>
              <?= $timeslip["slip_timer_end"]; ?>
            </td>

            <td>
              <?= $timeslip["slip_description"]; ?>
            </td>

            <td>
              <?= $timeslip["slip_hours"]; ?>
            </td>
          </tr>
        <?php } ?>

        <tr>
          <td class="blanktotal" colspan="6" rowspan="7"></td>
          <td class="totals" style="font-weight: bold;">Total Hours</td>
          <td class="totals" style="font-weight: bold;"><?= number_format($total_hours, 2) ?></td>
        </tr>
        <tr>
          <td class="totals" style="font-weight: bold;">Total Days</td>
          <td class="totals" style="font-weight: bold;"><?= number_format(($total_hours / 8),2) ?></td>
        </tr>
      </tbody>

    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
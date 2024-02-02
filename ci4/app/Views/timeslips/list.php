<?php require_once(APPPATH . 'Views/timeslips/list-title.php'); ?>
<div class="white_card_body ">
    <div class="QA_table ">
        <div class="page_title_right" style="float:right;">Total Records: <span id="total">0</span> </div>
        <!-- table-responsive -->
        <table id="myTable" class="table table-listing-items tableDocument table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col" width="30"></th>
                    <th scope="col">Week No.</th>
                    <th scope="col">Task</th>
                    <th scope="col">Employee</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">Start Time</th>
                    <?php /* foreach ($fields as $field) { ?>
              <th scope="col"><?php echo lang('Timeslips.'.$field); ?></th>
          <?php } */?>
                    <th scope="col" width="50">
                        <?php echo lang('Common.action'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php /* foreach (${$tableName} as $row) { ?>
          <tr data-link="/<?php echo $tableName; ?>/edit/<?= $row[$identifierKey]; ?>">
              <td class="f_s_12 f_w_400"><input type="checkbox" value="<?= $row['uuid'] ?>" class="check_all" onclick="setExportItem(this);"></td>
              <?php foreach ($fields as $field) { ?>
                  <td class="f_s_12 f_w_400"><?= $row[$field]; ?></td>
              <?php } ?>
              <td class="f_s_12 f_w_400 text-right">
                  <div class="header_more_tool">
                      <div class="dropdown">
                          <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                              <i class="ti-more-alt"></i>
                          </span>
                          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

                              <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');" href="/<?php echo $tableName; ?>/delete/<?= $row[$identifierKey]; ?>"> <i class="ti-trash"></i> Delete</a>
                              <a class="dropdown-item" href="/<?php echo $tableName; ?>/edit/<?= $row[$identifierKey]; ?>"> <i class="fas fa-edit"></i> Edit</a>
                              <a class="dropdown-item" href="/<?php echo $tableName; ?>/clone/<?= $row[$identifierKey]; ?>"> <i class="fas fa-copy"></i> Clone</a>
                          </div>
                      </div>
                  </div>
              </td>
          </tr>
      <?php } */?>

            </tbody>
        </table>

        <nav aria-label="Page navigation">
            <ul class="pagination" id="myPager"></ul>
        </nav>
    </div>
</div>

<?php require_once(APPPATH . 'Views/timeslips/footer.php'); ?>
<?php require_once(APPPATH . 'Views/common/pagination.php'); ?>
<script>

    function delay(callback, ms) {
        var timer = 0;
        return function () {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }


    let timer;

    const waitTime = 2000;

    const filterInput = document.getElementById('filter');

    filterInput.addEventListener('keyup', event => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            if (event.target.value == "") {
                const url = new URL(window.location.href);
                url.searchParams.delete('filter');
                window.history.replaceState(null, null, url);
                document.getElementById('filter').value = "";
            }
            window.searchTimeslips();
        }, waitTime);
    });


    // $('#filter').on('input', handleInput);;


    var executed = false;
    window.searchTimeslips = function () {
        executed = false;
        $('#myPager').html('');
        window.createPagination(0, true);

    }
    // window.clearFilters = function(){
    //     const url = new URL(window.location.href);
    //     url.searchParams.delete('filter');
    //     url.searchParams.delete('list_week');
    //     url.searchParams.delete('list_month');
    //     url.searchParams.delete('list_year');
    //     window.history.replaceState(null, null, url); // or pushState

    // }
    window.createPagination = function (pageNum, relaod) {
        var filter = document.getElementById('filter').value;
        var list_week = document.getElementById('list_week').value;
        var list_month = document.getElementById('list_monthpicker2').value;
        var list_year = document.getElementById('list_yearpicker2').value;
        const url = new URL(window.location.href);
        filter && url.searchParams.set('filter', filter);
        list_week && url.searchParams.set('list_week', list_week);
        list_month && url.searchParams.set('list_month', list_month);
        list_year && url.searchParams.set('list_year', list_year);
        window.history.replaceState(null, null, url); // or pushState
        const listWeek = list_week == "none" ? "" : list_week;
        const listMonth = list_month == "none" ? "" : list_month;
        const listYear = list_year == "none" ? "" : list_year;
        pageNum = pageNum + 1
        $.ajax({
            url: '<?= base_url() ?>/api/<?= $tableName ?>/<?= $uuid_business ?>/?page=' + pageNum + '&filter=' + filter + '&list_week=' + listWeek + '&list_month=' + listMonth + '&list_year=' + listYear,
            headers: {
                'Authorization': 'Basic <?= !empty($token) ? $token : '' ?>',
                //'X-CSRF-TOKEN':'xxxxxxxxxxxxxxxxxxxx',
                'Content-Type': 'application/json'
            },
            method: 'POST',
            dataType: 'json',
            success: function (responseData) {
                //$('#pagination').html(responseData.pagination);  
                if (!executed) {
                    $('#myTable').pageMe({
                        pagerSelector: '#myPager',
                        showPrevNext: true,
                        hidePageNumbers: false,
                        perPage: 10,
                        total: responseData.total
                    });
                    $("#total").html(responseData.total);
                    executed = true;
                }
                window.paginationData(responseData.data);
                if (relaod) window.location.reload();
            }
        });
    }

    window.paginationData = function (data) {
        //console.log(data)
        $('#myTable tbody').empty();
        for (emp in data) {
            var d = new Date(data[emp].slip_start_date * 1000);
            var dateString = ('0' + (d.getMonth() + 1)).slice(-2) + '/' + ('0' + (d.getDate())).slice(-2) + '/' + d.getFullYear();
            var empRow = "<tr data-link='/<?= $tableName . "/edit/" ?>" + data[emp].uuid + "'>";
            empRow += "<td class='f_s_12 f_w_400'><input type='checkbox' value=" + data[emp].uuid + " class='check_all' onclick='setExportItem(this);'></td><td>" + data[emp].week_no + "</td>";
            empRow += "<td>" + data[emp].taskName.substr(0, 20) + (data[emp].taskName.length > 20 ? '...' : '') + "</td>";
            empRow += "<td>" + data[emp].employeeName.substr(0, 20) + (data[emp].employeeName.length > 20 ? '...' : '') + "</td>"
            empRow += "<td>" + dateString + "</td>"
            empRow += "<td>" + data[emp].slip_timer_started + "</td>"
            empRow += '<td class="f_s_12 f_w_400 text-right"><div class="header_more_tool"> <div class="dropdown"> <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">  <i class="ti-more-alt"></i></span> <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"><a class="dropdown-item" onclick="return confirm(\'Are you sure want to delete?\');" href="/<?= $tableName ?>/delete/' + data[emp].uuid + '"> <i class="ti-trash"></i> Delete</a><a class="dropdown-item" href="/<?= $tableName ?>/edit/' + data[emp].uuid + '"> <i class="fas fa-edit"></i> Edit</a><a class="dropdown-item" href="/<?php echo $tableName; ?>/clone/' + data[emp].uuid + '"> <i class="fas fa-copy"></i> Clone</a> </div> </div></div></td>'
            //empRow += "<td>"+ data[emp].designation +"</td>"
            //empRow += "<td>"+ data[emp].address +"</td>";
            empRow += "</tr>";
            console.log($('#myTable tbody').length)
            $('#myTable tbody').append(empRow);
        }
        if (data.length == 0) {
            $('#myTable tbody').empty();
            var empRow = "<tr ><td colspan=7>No data found!!</td></tr>";
            $('#myTable tbody').append(empRow);
        }
    }


    $(document).ready(function () {
        window.createPagination(0, false);
    });
</script>
<script>
    $('.table-listing-items  tr  td').on('click', function (e) {
        var dataClickable = $(this).parent().attr('data-link');
        if ($(this).is(':last-child') || $(this).is(':first-child')) { } else {
            if (dataClickable && dataClickable.length > 0) {
                window.location = dataClickable;
            }
        }
    });

    var exportIds = [];

    function setExportItem(this_element) {
        if (this_element.checked) {
            exportIds.push(this_element.value);
        } else {
            var index = exportIds.indexOf(this_element.value);
            if (index !== -1) {
                exportIds.splice(index, 1);
            }
        }
        $('[name="exportIds"]').val(JSON.stringify(exportIds));

        if (exportIds.length) {
            $(".time-picker").hide();
        } else {
            $(".time-picker").show();
        }
    }

    $(document).on('click', ".table-listing-items  tr  td", function () {
        console.log($(this).html())
        if ($(this).html().indexOf('<input') < 0) {
            var dataClickable = $(this).parent().attr('data-link');
            if ($(this).is(':last-child')) {
            } else {
                if (dataClickable && dataClickable.length > 0) {

                    window.location = dataClickable;
                }
            }
        }

    });
</script>
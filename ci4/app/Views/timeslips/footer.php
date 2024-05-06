                      </div>
                      </div>

                      </div>
                      </div>
                      </div>

                      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLabel"><?php echo lang('Purchase_invoice.export_timeslip_pdf'); ?></h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <form action="<?php echo base_url("timeslips/exportPDF"); ?>" method="post">
                                      <div class="modal-body">
                                          <div class="form-group">
                                              <label for="exampleFormControlSelect1"><?php echo lang('Common.employee'); ?></label>
                                              <select name="employee" class="form-control" id="exampleFormControlSelect1">
                                                  <option value="-1" selected>All</option>
                                                  <?php foreach (@$employees as $employee) { ?>
                                                      <option value="<?php echo $employee["id"]; ?>"><?php echo $employee["name"]; ?></option>
                                                  <?php } ?>
                                              </select>
                                          </div>

                                          <input type="hidden" name="exportIds">
<?php 
$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
?>
                                          <div class="row time-picker">
                                              <div class="col-lg-6">
                                                  <div class="form-group">
                                                      <label for="monthpicker22"><?php echo lang('Common.month'); ?></label>
                                                      <select class="form-control" id="monthpicker22" name="monthpicker">
                                                      <?php foreach ($months as $key=>$month) { ?>
                            <option value="<?php echo $key+1 ?>"><?php echo $month; ?></option>
                        <?php } ?>
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="col-lg-6">
                                                  <div class="form-group">
                                                      <label for="monthpicker"><?php echo lang('Common.year'); ?></label>
                                                      <select class="form-control" id="yearpicker" name="yearpicker">
                                                      </select>
                                                  </div>
                                              </div>
                                          </div>

                                          <div class="form-group">
                                              <label><?php echo lang('Purchase_invoice.choose_template'); ?></label>
                                              <select name="template_id" class="form-control">
                                                  <?php
                                                  if (empty($templates)) {
                                                      echo '<option value="">' . lang('Common.no_data') . '</option>';
                                                  } else {
                                                  foreach (@$templates as $template) { ?>
                                                      <option value="<?php echo $template["id"]; ?>" <?php $template["is_default"] == '1' ? 'selected' : '' ?>><?php echo $template["code"]; ?></option>
                                                  <?php }
                                                  }
                                                  ?>
                                              </select>
                                          </div>

                                          <div class="form-group">
                                              <input type="checkbox" value="1" name="order_by" />
                                              <label><?php echo lang('Purchase_invoice.order_by_latest'); ?></label>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('Common.close'); ?></button>
                                          <button type="submit" class="btn btn-primary"><?php echo lang('Common.export_pdf'); ?></button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>

                      <?php require_once(APPPATH . 'Views/users/scripts.php'); ?>
                      <!-- footer part -->
                      <?php require_once(APPPATH . 'Views/common/footer_copyright.php'); ?>

                      </section>
                      <script type="text/javascript">

                        function initializeGridTable({ ...params }) {
                            const { columnsTitle, tableName, apiPath, selector, columnsMachineName, listWeek } = params;
                            console.log({listWeek});
                            let allColumns = ['uuid'].concat(columnsMachineName);
                            allColumns = allColumns.concat([null]);
                            let token = "<?php echo session("jwt_token"); ?>";
                            let businessUUID = "<?php echo session("uuid_business"); ?>";

                            const grid = new gridjs.Grid({
                                columns: [
                                    {
                                        name: "uuid",
                                        hidden: true
                                    },
                                    ...columnsTitle,
                                    {
                                        name: 'Actions',
                                        sort: false,
                                        formatter: (cell, row) => {
                                            return gridjs.html(
                                                `<div class="header_more_tool">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown">
                                                        <i class="ti-more-alt"></i>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" onclick="return confirm('Are you sure want to delete?');"
                                                            href="/${tableName}/deleterow/${row.cells[0].data}"> <i class="ti-trash"></i>
                                                            Delete
                                                        </a>
                                                        <a class="dropdown-item" href="/${tableName}/edit/${row.cells[0].data}"> <i
                                                            class="fas fa-edit"></i>
                                                            Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>`
                                            );
                                        }
                                    },
                                ],
                                pagination: {
                                    limit: 20,
                                    server: {
                                        url: (prev, page, limit, rest) => `${prev}${prev.includes("?") ? "&" : "?"}limit=${limit}&offset=${page * limit}`
                                    }
                                },
                                className: {
                                    table: 'table table-striped'
                                },
                                search: {
                                    server: {
                                        url: (prev, keyword) => `${prev}${prev.includes("?") ? "&" : "?"}query=${keyword}`
                                    }
                                },
                                sort: {
                                    multiColumn: false,
                                    server: {
                                        url: (prev, columns) => {
                                            if (!columns.length) return prev;
                                            const col = columns[0];
                                            const dir = col.direction === 1 ? 'asc' : 'desc';
                                            let colNames = columnsMachineName;
                                            let colName = colNames[col.index];

                                            return `${prev}${prev.includes("?") ? "&" : "?"}order=${colName}&dir=${dir}`;
                                        }
                                    }
                                },
                                server: {
                                    url: `${apiPath}?uuid_business_id=${businessUUID}`,
                                    headers: { Authorization: `Bearer ${token}` },
                                    then: data => data.data.map(customer =>
                                        allColumns.map((fields, idx) => [
                                            fields === "status" ?
                                                (customer[fields] == 1 ? "Active" : "Inactive") :
                                                fields === "allow_web_access" ?
                                                    (customer[fields] == 1 ? "Allowed" : "Not Allowed")
                                                    : customer[fields]
                                        ])
                                    ),
                                    total: data => data.recordsTotal
                                }
                            }).render(document.getElementById(selector));
                            grid.on('cellClick',
                                (...args) =>
                                    args[2].id !== "actions" &&
                                    (window.location.href = `/${tableName}/edit/${args[3]._cells[0].data}`)
                            );
                        }


                          let startYear = 2020;
                          let endYear = new Date().getFullYear();
                          for (i = endYear; i > startYear; i--) {
                              $('#yearpicker').append($('<option />').val(i).html(i));
                              $('#list_yearpicker').append($('<option />').val(i).html(i));
                          }

                          let startMonth = 1;
                          let endMonth = 12;
                          for (i = startMonth; i <= endMonth; i++) {
                              j = ('0' + i).slice(-2);
                              $('#monthpicker').append($('<option />').val(j).html(toMonthName(j)));
                              $('#list_monthpicker').append($('<option />').val(j).html(toMonthName(j)));
                          }


                          function toMonthName(monthNumber) {
                              const date = new Date();
                              date.setMonth(monthNumber - 1);
                              return date.toLocaleString('en-US', {
                                  month: 'long',
                              });
                          }

                          let currentMonth = new Date().getMonth() + 1;
                          $('#monthpicker').val(currentMonth);
                      </script>
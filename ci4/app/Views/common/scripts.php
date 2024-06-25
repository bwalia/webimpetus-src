<!-- footer  -->
<script src="/assets/js/jquery-3.4.1.min.js"></script>
<script src="/assets/js/jquery-ui.min.js"></script>
<script src="/assets/js/popper.min.js"></script>

<!-- bootstarp js -->
<script src="/assets/js/bootstrap.min.js"></script>
<!-- sidebar menu  -->
<script src="/assets/js/metisMenu.js"></script>
<script src="/assets/js/select2.min.js"></script>
<script src="/assets/js/notify.js"></script>


<script src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js" type="text/javascript"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js" type="text/javascript"></script> -->


<script src="/assets/js/custom.js"></script>
<script src="/assets/js/list.js"></script>
<script src="/assets/js/edit.js"></script>
<script src="/assets/vendors/chartlist/Chart.min.js"></script>
<!-- <script src="js/chart.min.js"></script> -->
<script src="/assets/vendors/chartjs/roundedBar.min.js"></script>
<script src="/assets/vendors/am_chart/amcharts.js"></script>
<!-- apex chrat  -->
<script src="/assets/vendors/apex_chart/apex-chart2.js"></script>
<script src="/assets/vendors/apex_chart/apex_dashboard.js"></script>
<script src="/assets/vendors/chart_am/core.js"></script>
<script src="/assets/vendors/chart_am/charts.js"></script>
<script src="/assets/vendors/chart_am/animated.js"></script>
<script src="/assets/vendors/chart_am/kelly.js"></script>
<script src="/assets/vendors/chart_am/chart-custom.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
<script src="/assets/ckeditor/ckeditor.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<script type="text/javascript"
	src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script src="/assets/js/gridjs.js"></script>


<script type="text/javascript">
	var baseUrl = "<?php echo base_url(); ?>";
	var class_name = "<?php echo @$tableName; ?>";
	var ajaxBaseUrl = baseUrl + class_name;
	var moduleName = '<?php echo @$tableName; ?>';
	$(document).ready(function () {

		if ($(".js-example-basic-multiple").length > 0) {
			$('.js-example-basic-multiple').select2();
		}

		$('.select2').select2({
			placeholder: "Select an Option",
			allowClear: true,
			tags: true,
		});

		$('.datepicker').datepicker({
			autoclose: true,
			clearBtn: true,
			todayHighlight: true,
		});

		$('.datetimepicker').datetimepicker();

		$('.timepicker').datetimepicker({
			format: 'hh:mm:ss a',
		});

	});

	$(document).ready(function () {
		if ($("#example").length > 0) {
			$('#example').DataTable({
				columnDefs: [{
					targets: 1,
					className: "truncate"
				},
				],
				createdRow: function (row) {
					var td = $(row).find(".truncate");
					td.attr("title", td.html());
				},
				scrollX: false,
				order: [
					[0, 'desc']
				],
				stateSave: true,
				select: 'single'
			});
		}

		if ($(".checkb").length > 0) {
			$('body .checkb').change(function () {
				if (this.checked) {
					//alert($(this).data('url'));
					var status = 1;
				} else var status = 0;
				var formData = {
					'id': $(this).val(),
					'status': status //for get email 
				};
				$.ajax({
					url: $(this).data('url'),
					type: "post",
					data: formData,
					success: function (d) {
						alert('status changed successfully!!');
					}
				});

			});
		}
		// $('table tbody tr td').not(":last-child").click(function(){
		// 	if($('table tbody tr').data('href').indexOf("enquiries") === -1)
		// 		window.location.href = $('table tbody tr').data('href')
		// })

		<?php if (empty($_SESSION['role'])) { ?>
			/*$("table a").click(function(){
				return false;
			});*/

		<?php } else { ?>
			/* $("table tr").click(function(){
	
			}); */


		<?php } ?>

		$(".dashboard-dropdown").select2({
			width: '100%'
		});
		$('#uuidBusinessIdSwitcher').change(function () {
			var bid = $(this).val();
			$.ajax({
				url: baseURL + 'home/switchbusiness',
				data: {
					bid: bid
				},
				method: 'POST',
			}).done(function (response) {

				location.reload();
			}).fail(function (x, y, z) {

				console.log(x, y, z);
			});
		});
	});

	setTimeout(function () {
		$('#example_length select').select2();
	}, 300)


	// Add the following code if you want the name of the file appear on select
	$(".custom-file-input").on("change", function () {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});

	function isValidTimestamp(timestamp) {
		// Check if the value is a number
		if (isNaN(timestamp)) {
			return false;
		}

		// Check if the timestamp is within a valid range
		// January 1, 2000 => 946684800
		// December 31, 3000 => 32503680000
		const minTimestamp = 946684800; 
		const maxTimestamp = 32503680000; 

		return timestamp >= minTimestamp && timestamp <= maxTimestamp;
	}

	function convertTimestamp(timestamp) {
		const date = new Date(timestamp * 1000); // Multiply by 1000 to convert to milliseconds
        const readableDate = date.toISOString().replace('T', ' ').substring(0, 19); // Format as 'YYYY-MM-DD HH:MM:SS'
        return readableDate;
	}

	function initializeGridTable({ ...params }) {
		const { columnsTitle, tableName, apiPath, selector, columnsMachineName } = params;
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
									${(
										tableName === 'companies' ||
										tableName === 'contacts' ||
										tableName === 'customers' ||
										tableName === 'blog' ||
										tableName === 'templates'
									) ? `
										<a class="dropdown-item" href="/${tableName}/clone/${row.cells[0].data}"> <i
											class="fas fa-copy"></i>
											Clone
										</a>
									` : ''}
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
					url: (prev, page, limit) => `${prev}${prev.includes("?") ? "&" : "?"}limit=${limit}&offset=${page * limit}`
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
						let colName = colNames[col.index - 1];

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
							(customer[fields] == 1 ? "Allowed" : "Not Allowed") :
						isValidTimestamp(customer[fields]) ?
							convertTimestamp(customer[fields])
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
</script>



<script>
	if ($("#addcat").length > 0) {
		$("#addcat").validate({
			ignore: [],
			rules: {
				title: {
					required: true,
				},
				content: {
					required: function (textarea) {
						CKEDITOR.instances[textarea.id].updateElement();
						var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
						return editorcontent.length === 0;
					}
				}
			},
			messages: {
				name: {
					required: "Please enter title",
				},

			},
		})
	}


	if ($("#chk_manual").length > 0) {
		$("#chk_manual").click(function () {
			if ($(this).is(':checked')) {
				$('#code').attr("readonly", false);
				$('#code').val("");
			} else {
				$('#code').attr("readonly", true);
				$('#code').val("");
			}

		});
	}

	var sr = 0;
	$(document).on('change', '.filee', function () {
		//alert($(this).val()); 
		$('#' + $(this).attr('id')).after($(this).val())

		var inc = sr++;
		$("#divfile").append('<div class="custom-file"><input type="file" name="file[]" class="custom-file-input filee" id="customFile' + inc + '"><label class="custom-file-label" for="customFile' + inc + '">Choose file</label></div>')
	});


	CKEDITOR.replace('content', {
		filebrowserBrowseUrl: '/assets/ckfinder/ckfinder.html',
		filebrowserUploadUrl: '/assets/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserWindowWidth: '900',
		filebrowserWindowHeight: '700'
	});

	function fillupBillToAddress(e) {
		var clientId = e.target.value;
		$.ajax({
			url: baseUrl + '/' + moduleName + '/loadBillToData',
			data: {
				clientId: clientId
			},
			dataType: 'JSON',
			method: 'POST',
		}).done(function (response) {

			if (response.status) {

				$('textarea[name="bill_to"]').val(response.value);
			}
		}).fail(function (x, y, z) {

			console.log(x, y, z);
			alert(y + ': ' + z);
		})
	}

	function calculateDueDate(e) {
		var term = e.target.value;
		var currentDate = $('#date.datepicker').val();
		$.ajax({
			url: baseUrl + '/' + moduleName + '/calculateDueDate',
			data: {
				term: term,
				currentDate: currentDate
			},
			dataType: 'JSON',
			method: 'POST',
		}).done(function (response) {

			if (response.status) {

				$('#due_date').datepicker('setDate', response.value);
			}
		}).fail(function (x, y, z) {

			console.log(x, y, z);
			alert(y + ': ' + z);
		})
	}
</script>


<style>
	.custom-file {
		margin: 30px;
	}
</style>
</body>

</html>
<?php require_once (APPPATH . 'Views/common/edit-title.php'); ?>


<div class="white_card_body">
	<div class="card-body">

		<form id="addcat" autocomplete="off" method="post" action="/vm/update" enctype="multipart/form-data">

			<input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$vm->id ?>" />
			<input type="hidden" name="uuid" value="<?= @$vm->uuid ?>" />
			<input type="hidden" name="uuid_business_id" value="<?= @$_SESSION['uuid_business'] ?>" />

			<div class="row">
				<div class="col-xs-12 col-md-12">
					<div class="white_card_body">
						<div class="card-body">
							<div class="form-row">
								<div class="col-md-10 m-auto">
									<div class="form-group row">
										<div class="col-sm-6">
											<label for="vm_name" class="col-form-label w-100">VM Name*</label>
											<input type="text" required class="form-control"
												value="<?= @$vm->vm_name ?>" id="vm_name" name="vm_name" placeholder="">
										</div>
										<div class="col-sm-6">
											<label for="vm_code" class="col-form-label">VM Code*</label>
											<input type="text" required class="form-control"
												value="<?= @$vm->vm_code ?>" id="vm_code" name="vm_code" placeholder="">
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label for="vm_cpu_cores" class="col-form-label w-100">VM CPU Cores</label>
											<input type="text" required class="form-control"
												value="<?= @$vm->vm_cpu_cores ?>" id="vm_cpu_cores" name="vm_cpu_cores"
												placeholder="">
										</div>
										<div class="col-sm-6">
											<label for="vm_ram_display" class="col-form-label">VM Ram</label>
											<input type="text" required class="form-control"
												value="<?= @$vm->vm_ram_display ?>" id="vm_ram_display"
												name="vm_ram_display" placeholder="">
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label for="vm_ipv4" class="col-form-label w-100">VM IPv4</label>
											<input type="text" required class="form-control"
												value="<?= @$vm->vm_ipv4 ?>" id="vm_ipv4" name="vm_ipv4" placeholder="">
										</div>
										<div class="col-sm-6">
											<label for="vm_ipv6" class="col-form-label">VM IPv6</label>
											<input type="text" required class="form-control"
												value="<?= @$vm->vm_ipv6 ?>" id="vm_ipv6" name="vm_ipv6" placeholder="">
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-6">
											<label for="inputEmail4">VM Tags</label>

											<select id="my-select2" data-select2-tags="true" name="vm_tags[]"
												multiple="multiple" class="form-control select2">
												<?php
												if (!empty($vm->vm_tags)) {
													$arr = explode(',', $vm->vm_tags);
													foreach ($arr as $row): ?>
														<option value="<?= $row; ?>" selected="selected">
															<?= $row; ?>
														</option>
													<?php endforeach; ?>
												<?php } ?>
											</select>
										</div>
										<div id="categoriesSelector" class="form-group col-md-6">
											<label for="inputEmail4">Categories</label>
											<select id="vm_categories" name="vm_categories[]" multiple="multiple"
												class="form-control dashboard-dropdown select-category-vm-ajax">
												<?php
												if (isset($categories)) {
													foreach ($categories as $row): ?>
														<option value="<?= $row['uuid']; ?>" selected="selected">
															<?= $row['name']; ?>
														</option>
													<?php endforeach;
												} ?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-12">
											<label for="vm_description" class="col-form-label w-100">Description</label>
											<textarea class="form-control required"
												name="vm_description"><?= @$vm->vm_description ?></textarea>
										</div>
									</div>
									<div class="">
										<label for="status">Status</label>
									</div>
									<div class="form-group col-md-12">
										<label for="status" class="pr_10">
											<input type="radio" value="1" class="form-control" id="status" name="status"
												<?= @$vm->status == 1 ? 'checked' : '' ?> placeholder=""> Active
										</label>
										<label for="status">
											<input type="radio" <?= @$vm->status == 0 ? 'checked' : '' ?> value="0"
												class="form-control" id="status" name="status" placeholder=""> Inactive
										</label>
									</div>
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<?php require_once (APPPATH . 'Views/common/footer.php'); ?>

<script>

	$(document).ready(function () {
		$(".select-category-vm-ajax").select2({
			ajax: {
				url: "vmCategories",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term
					};
				},
				processResults: function (data, params) {
					return {
						results: $.map(data, function (item) {
							return {
								text: item.name,
								id: item.uuid
							}
						})
					};
				},
			},
			minimumInputLength: 2
		})
	});

	$("#vm_name").on("input", function () {
		const str = $(this).val().trim();

		var result = str.replace(/[^\w\s]/gi, '-');
		result = result.replace(/\s\s+/g, ' ');
		result = result.replace(/ /g, "-");
		result = result.replace(/^\W+|\W+$/g, "");
		result = result.replace(/-+/g, '-');

		$("#vm_code").val(result.toLowerCase());
	});
</script>
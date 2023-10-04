<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
	<div class="card-body">

		<form id="addcat" method="post" action="/jobapps/update" enctype="multipart/form-data">

			<input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$content->id ?>" />

			<input type="hidden" class="form-control" name="type" placeholder="" value="2" />

			<div class="row">
				<div class="col-xs-12 col-md-12">
					<nav>
						<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
								role="tab" aria-controls="nav-home" aria-selected="true">Editor</a>
							<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
								role="tab" aria-controls="nav-profile" aria-selected="false">Search Optimisation</a>

							<a class="nav-item nav-link" id="nav-about-tab" data-toggle="tab" href="#nav-about"
								role="tab" aria-controls="nav-about" aria-selected="false">Setup</a>

						</div>
					</nav>
					<div class="tab-content py-3 px-3 px-sm-0 col-md-9" id="nav-tabContent">
						<div class="tab-pane fade show active" id="nav-home" role="tabpanel"
							aria-labelledby="nav-home-tab">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputEmail4">Title*</label>
									<input type="text" class="form-control" value="<?= @$content->title ?>" id="title"
										name="title" placeholder="">
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4">Sub Title</label>
									<input type="text" class="form-control" id="sub_title" name="sub_title"
										placeholder="" value="<?= @$content->sub_title ?>">
								</div>



								<div class="form-group col-md-12">
									<label for="inputPassword4">Body*</label>
									<textarea class="form-control" name="content"
										id="content"><?= @$content->content ?></textarea>
								</div>


								<div class="form-group col-md-12">
									<label for="inputEmail4">Status</label>
								</div>
								<div class="form-group col-md-12">

									<label for="inputEmail4"><input type="radio" value="1" class="form-control"
											id="status" name="status" <?= @$content->status == 1 ? 'checked' : '' ?>
											placeholder=""> Yes</label>

									<label for="inputEmail4"><input type="radio" <?= @$content->status == 0 ? 'checked' : '' ?> value="0" class="form-control" id="status" name="status"
											placeholder=""> No</label>
								</div>
								<?php $json = !empty($content->custom_fields) ? json_decode(@$content->custom_fields) : []; ?>
								<div class="form-group col-md-12">
									<label for="inputEmail4">Reference</label>
									<input type="text" class="form-control" id="reference" name="reference"
										placeholder="" value="<?= @$json->reference ?>">
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4">Job Type*</label>
									<select onchange="" class="form-control" name="job_type" id="job_type">
										<option value="">-- Select Job Type--</option>

										<option value="Temporary" <?= @$json->job_type == 'Temporary' ? 'selected' : '' ?>>
											Temporary</option>
										<option value="Permanent" <?= @$json->job_type == 'Permanent' ? 'selected' : '' ?>>
											Permanent</option>
										<option value="Contract" <?= @$json->job_type == 'Contract' ? 'selected' : '' ?>>
											Contract</option>
										<option value="Freelance" <?= @$json->job_type == 'Freelance' ? 'selected' : '' ?>>
											Freelance</option>
										<option value="Part time" <?= @$json->job_type == 'Part time' ? 'selected' : '' ?>>
											Part time</option>

									</select>
								</div>


								<div class="form-group col-md-12">
									<label for="inputEmail4">Salary*</label>
									<input type="text" class="form-control" id="salary" name="salary" placeholder=""
										value="<?= @$json->salary ?>">
								</div>


								<div class="form-group col-md-12">
									<label for="inputEmail4">Employer</label>
									<input type="text" class="form-control" id="employer" name="employer" placeholder=""
										value="<?= @$json->employer ?>">
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4">Location</label>
									<input type="text" class="form-control" id="location" name="location" placeholder=""
										value="<?= isset($json->location) ? $json->location : '' ?>">
								</div>


								<div class="form-group col-md-12">
									<label for="inputEmail4">Job Status*</label>
								</div>
								<div class="form-group col-md-12">

									<label for="inputEmail4"><input type="radio" value="1" class="form-control"
											id="jobstatus" name="jobstatus" <?= @$json->jobstatus == 1 ? 'checked' : '' ?>
											placeholder=""> Vacant</label>

									<label for="inputEmail4"><input type="radio" value="0" class="form-control"
											id="jobstatus" name="jobstatus" <?= @$json->jobstatus == 0 ? 'checked' : '' ?>
											placeholder=""> Filled</label>
								</div>


							</div>


						</div>
						<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
							<div class="form-row">

								<div class="form-group col-md-12">
									<label for="inputEmail4">URL Code*</label>
									<input type="text" class="form-control" id="code" name="code" placeholder=""
										readonly="readonly" value="<?= @$content->code ?>"
										onchange="format_manual_code('Code')">
									<span class="help-block">URL (SEO friendly)</span><br>

									<span class="help-block">
										<input type="checkbox" name="chk_manual" id="chk_manual">
										I want to manually enter code</span>


								</div>



								<div class="form-group col-md-12">
									<label for="inputEmail4">Meta keywords</label>
									<input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
										placeholder="" value="<?= @$content->meta_keywords ?>">
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4">Meta Title</label>
									<input type="text" class="form-control" id="meta_title" name="meta_title"
										placeholder="" value="<?= @$content->meta_title ?>">
								</div>



								<div class="form-group col-md-12">
									<label for="inputPassword4">Meta Description</label>
									<textarea class="form-control"
										name="meta_description"><?= @$content->meta_description ?></textarea>
								</div>


							</div>
						</div>

						<div class=" fade" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputState">Choose User</label>
									<select id="uuid" name="uuid" class="form-control dashboard-dropdown">
										<option value="0" selected="">--Select--</option>
										<?php foreach ($users as $row): ?>
											<option value="<?= $row['id']; ?>" <?= ($row['id'] == @$content->uuid) ? 'selected' : '' ?>>
												<?= $row['name']; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>

								<div class="form-group col-md-12">
									<label for="inputState">Choose Category</label>
									<select id="catid" name="catid[]" multiple
										class="form-control select2">
										<?php foreach ($cats as $row): ?>
											<option value="<?= $row['id']; ?>" <?= in_array($row['id'], $selected_cats) ? 'selected' : '' ?>>
												<?= $row['name']; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>


								<div class="form-group col-md-12">
									<label for="inputState">Publish Date</label>

									<input id="" class="form-control datepicker" name="publish_date" width="250"
										type="text" value="<?= render_date(@$content->publish_date); ?>" />

								</div>


							</div>
						</div>
					</div>

				</div>
			</div>

			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
	// Add the following code if you want the name of the file appear on select
	$(".custom-file-input").on("change", function () {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
</script>
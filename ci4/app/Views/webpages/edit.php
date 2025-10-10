<?php require_once(APPPATH . 'Views/common/edit-title.php');

$blocks_list = isset($webpage->uuid) ? getResultArray("blocks_list", !empty($webpage->uuid) ? ["uuid_linked_table" => @$webpage->uuid] : array()) : [];
$categories = getResultArray("categories", array());

$type["TEXT"] = "TEXT";
$type["JSON"] = "JSON";
$type["LIST"] = "LIST";
$type["WYSIWYG"] = "WYSIWYG";
$type["MARKDOWN"] = "MARKDOWN";
$type["YAML"] = "YAML";

$data_type_format["TEXT"] = "Your text goes here";
$data_type_format["JSON"] = '{ "example" : "some data in JSON format goes here"}';
$data_type_format["LIST"] = "PHP JAVA NoteJs";
$data_type_format["WYSIWYG"] = "Your text goes here";
$data_type_format["MARKDOWN"] = "#### The quarterly results look great!- Revenue was off the chart.
- Profits were higher than ever.

*Everything* is going according to **plan**.";
$data_type_format["YAML"] = "# Employee records
- martin:
    name: Martin D'vloper
    job: Developer
    skills:
      - python
      - perl
      - pascal
- tabitha:
    name: Tabitha Bitumen
    job: Developer
    skills:
      - lisp
      - fortran
      - erlang";


?>

<div class="white_card_body">
	<div class="card-body">

		<form id="addcat" method="post" action="/webpages/update" enctype="multipart/form-data">

			<input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$webpage->id ?>" />
			<input type="hidden" class="form-control" name="strategies" placeholder="" value="<?= @$menuName ?>" />
			<input type="hidden" class="form-control" name="uuid" value="<?= @$webpage->uuid ?>" />

			<div class="row">
				<div class="col-xs-12 col-md-12">
					<nav>
						<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
								role="tab" aria-controls="nav-home" aria-selected="true">Page Editor</a>
							<a class="nav-item nav-link" id="nav-custom-field-tab" data-toggle="tab"
								href="#nav-custom-field" role="tab" aria-controls="nav-custom-field"
								aria-selected="false">Custom Fields</a>
							<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
								role="tab" aria-controls="nav-profile" aria-selected="false">Search Optimisation</a>
							<a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact"
								role="tab" aria-controls="nav-contact" aria-selected="false">Pictures</a>
							<a class="nav-item nav-link" id="nav-about-tab" data-toggle="tab" href="#nav-about"
								role="tab" aria-controls="nav-about" aria-selected="false">Page Settings</a>
							<a class="nav-item nav-link" id="nav-blocks-tab" data-toggle="tab" href="#nav-blocks"
								role="tab" aria-controls="nav-blocks" aria-selected="false">Blocks</a>
						</div>
					</nav>
					<div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
						<div class="tab-pane fade show active" id="nav-home" role="tabpanel"
							aria-labelledby="nav-home-tab">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputEmail4">Title*</label>
									<input type="text" class="form-control" value="<?= @$webpage->title ?>" id="title"
										name="title" placeholder="">
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4">Sub Title</label>
									<input type="text" class="form-control" id="sub_title" name="sub_title"
										placeholder="" value="<?= @$webpage->sub_title ?>">
								</div>
								<div class="form-group col-md-12">
									<label for="inputState">Publish Date</label>
									<input id="publish_date" class="form-control datepicker" name="publish_date"
										width="250" type="text" autocomplete=""
										value="<?= render_date(@$webpage->publish_date) ?>" />

								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4">Categories</label>
									<select id="categories" name="categories[]" multiple class="form-control select2">
										<?php
										if (isset($webpage) && (!empty($webpage->categories))) {
											$arr = json_decode(@$webpage->categories);
										} else
											$arr = [];
										foreach ($categories as $row): ?>
											<option value="<?= $row['id']; ?>" <?php if ($arr)
												  echo
												  	in_array($row['id'], $arr) ? 'selected="selected"' : '' ?>>
												<?= $row['name']; ?>
											</option>
										<?php endforeach;
										?>
									</select>
								</div>

								<div class="form-group col-md-12">
									<label for="webpage_tags">
										<i class="fa fa-tags"></i> Tags
										<a href="/tags/manage" target="_blank" style="font-size: 0.85rem; margin-left: 8px;">
											<i class="fa fa-cog"></i> Manage Tags
										</a>
									</label>
									<select id="webpage_tags" name="webpage_tags[]" class="form-control select2" multiple="multiple"
											data-placeholder="Select tags for this page...">
										<!-- Populated by JavaScript -->
									</select>
									<small class="form-text text-muted">
										Select multiple tags to categorize this page.
									</small>
								</div>

								<div class="form-group col-md-12">
									<label for="inputPassword4">Body</label>
									<textarea class="form-control" name="content"
										id="content"><?= @$webpage->content ?></textarea>
								</div>

								<div class="form-group col-md-12">
									<div><label for="inputEmail4">Status</label></div>

									<label class="pr_10 "><input for="inputEmail4" type="radio" value="1"
											class="form-control " id="active" name="status" <?= @$webpage->status == 1 ? 'checked' : '' ?> placeholder=""> Active</label>

									<label class=""><input for="inputEmail4" type="radio" <?= @$webpage->status == 0 ? 'checked' : '' ?> value="0" class="form-control " id="inactive"
											name="status" placeholder=""> Inactive </label>
								</div>

							</div>
						</div>

						<div class="tab-pane fade" id="nav-custom-field" role="tabpanel"
							aria-labelledby="nav-custom-field-tab">
							<?php
							if (count($custom_fields) > 0) {
								?>
								<div class="form-row customFieldContainer" id="customFieldContainer">
									<?php
									for ($jak_i = 0; $jak_i < count($custom_fields); $jak_i++) {
										$new_id = $jak_i + 1;
										?>
										<div class="form-row col-md-12 secret-row-container"
											id="custom_fields_<?php echo $new_id; ?>">
											<div class="form-group col-md-3">
												<label for="customFieldName_<?php echo $new_id; ?>">Field Name</label>
												<input autocomplete="off" type="text" class="form-control"
													id="customFieldName_<?php echo $new_id; ?>" name="customFieldName[]"
													placeholder="" value="<?= $custom_fields[$jak_i]['field_name'] ?>">
											</div>
											<div class="form-group col-md-5">
												<label for="customFieldValue_<?php echo $new_id; ?>">Field Value</label>
												<input autocomplete="off" type="text" class="form-control"
													id="customFieldValue_<?php echo $new_id; ?>" name="customFieldValue[]"
													placeholder="" value="<?= $custom_fields[$jak_i]['field_value'] ?>">
											</div>
											<div class="form-group col-md-2 d-flex flex-column">
												<label for="customFieldType_<?php echo $new_id; ?>">Field Type</label>
												<!-- <input autocomplete="off" type="text" class="form-control"
													id="customFieldType_<?php echo $new_id; ?>" name="customFieldType[]"
													placeholder="" value="<?= $custom_fields[$jak_i]['field_type'] ?>"
												> -->
												<select name="customFieldType[]" id="customFieldType_<?php echo $new_id; ?>" class="form-control text_type">
													<option value="TEXT">TEXT</option>
													<!-- <option value="NUMBER">NUMBER</option> -->
													<!-- <option value="JSON">JSON</option>
													<option value="LIST">LIST</option>
													<option value="YAML">YAML</option>
													<option value="WYSIWYG">WYSIWYG</option>
													<option value="MARKDOWN">MARKDOWN</option>
													<option value="YAML">YAML</option> -->
												</select>
											</div>
											<input type="hidden" class="form-control" name="custom_fields_uuid[]" placeholder=""
												value="<?= @$custom_fields[$jak_i]['uuid'] ?>" />
											<?php
											if ($jak_i == 0) {
												?>
												<div class="form-group col-md-1 change d-flex">
													<button class="btn btn-primary bootstrap-touchspin-up add-field " type="button"
														style="max-height: 35px;margin-top: 28px;margin-left: 10px;">
														+
													</button>
													<button class="btn btn-info bootstrap-touchspin-up delete-field"
														data-type="custom_fields" data-id="<?= $custom_fields[$jak_i]['id'] ?>"
														id="deleteRow" type="button"
														style="max-height: 35px;margin-top: 28px;margin-left: 10px;">
														-
													</button>
												</div>
												<?php
											} else {
												?>
												<div class="form-group col-md-1 change">
													<button class="btn btn-info bootstrap-touchspin-up deleteaddress"
														data-type="custom_fields" data-id="<?= $custom_fields[$jak_i]['id'] ?>"
														id="deleteRow" type="button"
														style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>
												</div>
												<?php
											}
											?>
											<div class="form-group col-md-1 change">
												<button class="btn btn-info bootstrap-touchspin-up clone-row"
													data-type="custom_fields" data-uuid="<?= $custom_fields[$jak_i]['uuid'] ?>"
													data-id="office_address_<?php echo $new_id; ?>"
													data-key="<?php echo $new_id; ?>" id="cloneRow" type="button"
													style="max-height: 35px;margin-top: 28px;margin-left: 10px;">
													<i class="fas fa-clone"></i>
												</button>
											</div>
										</div>
										<?php
									}
									?>
								</div>

								<input type="hidden" value="<?php echo count($custom_fields); ?>" id="total_custom_fields"
									name="total_custom_fields">

								<?php
							} else {
								?>
								<div class="customFieldContainer" id="customFieldContainer">
									<div class="form-row" id="custom_fields_1">
										<div class="form-group col-md-3">
											<label for="customFieldName_1">Field Name</label>
											<input autocomplete="off" type="text" class="form-control"
												id="customFieldName_1" name="customFieldName[]" placeholder="" value="">
										</div>
										<div class="form-group col-md-5">
											<label for="customFieldValue_1">Field Value</label>
											<input autocomplete="off" type="text" class="form-control"
												id="customFieldValue_1" name="customFieldValue[]" placeholder="" value="">
										</div>
										<div class="form-group col-md-2 d-flex flex-column">
											<label for="customFieldType_1">Field Type</label>
											<!-- <input autocomplete="off" type="text" class="form-control"
												id="customFieldType_1" name="customFieldType[]" placeholder="" value=""> -->
												<select name="customFieldType[]" id="customFieldType_1" class="form-control text_type">
													<option value="TEXT">TEXT</option>
												</select>
										</div>
										<div class="form-group col-md-1 change">
											<button class="btn btn-primary bootstrap-touchspin-up add-field" type="button"
												style="max-height: 35px;margin-top: 28px;margin-left: 10px;">+</button>
										</div>
										<div class="form-group col-md-1 change">
											<button class="btn btn-info bootstrap-touchspin-up clone-row"
												data-type="custom_fields" data-uuid="" data-id="custom_fields_1"
												data-key="1" id="cloneRow" type="button"
												style="max-height: 35px;margin-top: 28px;margin-left: 10px;">
												<i class="fas fa-clone"></i>
											</button>
										</div>
									</div>
									<input type="hidden" value="1" id="total_custom_fields" name="total_custom_fields">
								</div>
								<?php
							}
							?>
						</div>

						<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
							<div class="form-row">

								<div class="form-group col-md-12">
									<label for="inputEmail4">URL Code*</label>
									<input type="text" class="form-control" id="code" name="code" placeholder=""
										readonly="readonly" value="<?= @$webpage->code ?>"
										onchange="format_manual_code('Code')">
									<span class="help-block">URL (SEO friendly)</span><br>

									<span class="help-block">
										<input type="checkbox" name="chk_manual" id="chk_manual">
										I want to manually enter code</span>


								</div>


								<div class="form-group col-md-12">
									<label for="inputEmail4">Meta keywords</label>
									<input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
										placeholder="" value="<?= @$webpage->meta_keywords ?>">
								</div>

								<div class="form-group col-md-12">
									<label for="inputEmail4">Meta Title</label>
									<input type="text" class="form-control" id="meta_title" name="meta_title"
										placeholder="" value="<?= @$webpage->meta_title ?>">
								</div>

								<div class="form-group col-md-12">
									<label for="inputPassword4">Meta Description</label>
									<textarea class="form-control"
										name="meta_description"><?= @$webpage->meta_description ?></textarea>
								</div>


							</div>
						</div>
						<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
							<div class="form-row">

								<div class="form-group col-md-12">

									<span class="all-media-image-files">
										<?php
										$json = @$webpage->custom_assets ? json_decode(@$webpage->custom_assets) : []; ?>

										<?php foreach ($images as $image) {
											if (!empty(@$image)) { ?>
												<img class="img-rounded" src="<?= @$image['name']; ?>" width="100px">
												<a href="/webpages/rmimg/<?= @$image['id'] . '/' . @$webpage->uuid; ?>"
													onclick="return confirm('Are you sure?')" class=""><i
														class="fa fa-trash"></i></a>
												<?php
											}
										}
										?>
									</span>

									<div class="form-group col-md-12" id="divfile">
										<label for="inputAddress">Upload</label>
										<div class="uplogInrDiv" id="drop_file_doc_zone">
											<input type="file" name="file[]" class=" fileUpload" id="customFile">
											<div class="uploadBlkInr">
												<div class="uplogImg">
													<img src="/assets/img/fileupload.png" />
												</div>
												<div class="uploadFileCnt">
													<p>
														<a href="#">Upload a file </a> file chosen or drag
														and drop
													</p>
													<p>
														<span>Video, PNG, JPG, GIF up to 10MB</span>
													</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
							<div class="form-row">
								<div class="form-group col-md-12">
									<label for="inputState">Choose Owner</label>
									<select name="user_uuid" class="form-control">
										<option value="0" selected="">--Select--</option>
										<?php foreach ($users as $row): ?>
											<option value="<?= $row['uuid']; ?>" <?= ($row['uuid'] == @$webpage->user_uuid) ? 'selected' : '' ?>>
												<?= $row['name']; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group  col-md-12">
									<label for="inputEmail4">Language Code</label>
									<select name="language_code" class="form-control">
										<option value="">--Select--</option>
										<option value="en" <?= @$webpage->language_code == "en" ? "selected" : "" ?>>
											English</option>
										<option value="fr" <?= @$webpage->language_code == "fr" ? "selected" : "" ?>>French
										</option>
										<option value="hi" <?= @$webpage->language_code == "hi" ? "selected" : "" ?>>Hindi
										</option>
									</select>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="nav-blocks" role="tabpanel" aria-labelledby="nav-blocks-tab">
							<?php
							if (count($blocks_list) > 0) {
								?>
								<div class="form-row addresscontainer">
									<?php
									for ($jak_i = 0; $jak_i < count($blocks_list); $jak_i++) {
										$new_id = $jak_i + 1;
										?>
										<div class="form-row col-md-12 each-row each-block" style="margin-bottom:30px;"
											id="office_address_<?php echo $new_id; ?>">
											<div class="form-group col-md-6">
												<label for="inputEmail4">Code</label>
												<input autocomplete="off" type="text" class="form-control blocks_code"
													id="blocks_code<?php echo $new_id; ?>" name="blocks_code[]" placeholder=""
													value="<?= $blocks_list[$jak_i]['code'] ?>"><br>

												<label for="inputEmail4">Title</label>
												<input autocomplete="off" type="text" class="form-control"
													id="blocks_title<?php echo $new_id; ?>" name="blocks_title[]" placeholder=""
													value="<?= $blocks_list[$jak_i]['title'] ?>"><br>

												<label for="inputEmail4">Sort</label>
												<input autocomplete="off" type="number" class="form-control" name="sort[]"
													placeholder="" value="<?= $blocks_list[$jak_i]['sort'] ?>">

												<label for="inputEmail4">Type</label>
												<select name="type[]" id="text_type" class="form-control text_type">
													<option value="TEXT" <?php if ($blocks_list[$jak_i]['type'] == 'TEXT')
														echo "selected"; ?>>TEXT</option>
													<option value="JSON" <?php if ($blocks_list[$jak_i]['type'] == 'JSON')
														echo "selected"; ?>>JSON</option>
													<option value="LIST" <?php if ($blocks_list[$jak_i]['type'] == 'LIST')
														echo "selected"; ?>>LIST</option>
													<option value="WYSIWYG" <?php if ($blocks_list[$jak_i]['type'] == 'WYSIWYG')
														echo "selected"; ?>>WYSIWYG</option>
													<option value="MARKDOWN" <?php if ($blocks_list[$jak_i]['type'] == 'MARKDOWN')
														echo "selected"; ?>>MARKDOWN</option>
													<option value="YAML" <?php if ($blocks_list[$jak_i]['type'] == 'YAML')
														echo "selected"; ?>>YAML</option>
												</select>
											</div>

											<input type="hidden" class="hidden_type_value"
												value="<?= $blocks_list[$jak_i]['type'] ?>">
											<input type="hidden" class="hidden_blocks_text_value"
												value="<?= urlencode($blocks_list[$jak_i]['text']) ?>">

											<div class="form-group col-md-5 textarea-block">
												<label class="textarea_label" for="inputEmail4">
													<?php echo @$type[$blocks_list[$jak_i]['type']]; ?>
												</label>

												<textarea class="form-control blocks_text <?php if ($blocks_list[$jak_i]['type'] == 'WYSIWYG') {
													echo "myClassName";
												} else {
													echo "textarea-height";
												} ?>" id="blocks_text<?php echo $new_id; ?>"
													name="blocks_text[]"><?= $blocks_list[$jak_i]['text'] ?></textarea>
											</div>
											<input type="hidden" value="<?= $blocks_list[$jak_i]['id'] ?>" id="blocks_id"
												name="blocks_id[]">
											<input type="hidden" value="<?= $blocks_list[$jak_i]['uuid'] ?>" id="blocks_uuid"
												name="blocks_uuid[]">

											<div class="form-group col-md-1 change">
												<button class="btn btn-info bootstrap-touchspin-up deleteaddress" id="deleteRow"
													type="button"
													style="max-height: 35px;margin-top: 38px;margin-left: 10px;margin-bottom:10px;">-</button>
												<br>
												<a href="#" class="tooltip-class" style="margin-left: 23px;"
													data-toggle="tooltip"
													title="<?= @$data_type_format[$blocks_list[$jak_i]['type']]; ?>"><i
														class="fa fa-info-circle"></i></a>
											</div>
										</div>
										<?php
									}
									?>
								</div>

								<input type="hidden" value="<?php echo count($blocks_list); ?>" id="total_blocks"
									name="total_blocks" />

								<?php
							} else {
								?>
								<div class="form-row each-block" style="margin-bottom:30px;" id="office_address_1">
									<div class="form-group col-md-6">
										<label for="inputEmail4">Code</label>
										<input autocomplete="off" type="text" class="form-control blocks_code"
											id="first_name_1" name="blocks_code[]" placeholder="" value="">

										<label for="inputEmail4">Title</label>
										<input autocomplete="off" type="text" class="form-control" id="surname"
											name="blocks_title[]" placeholder="" value="">
										<label for="inputEmail4">Sort</label>
										<input autocomplete="off" type="number" class="form-control" name="sort[]"
											placeholder="" value="">

										<label for="inputEmail4">Type</label>
										<select name="type[]" id="text_type" class="form-control text_type">
											<option value="TEXT">TEXT</option>
											<option value="JSON">JSON</option>
											<option value="LIST">LIST</option>
											<option value="YAML">YAML</option>
											<option value="WYSIWYG">WYSIWYG</option>
											<option value="MARKDOWN">MARKDOWN</option>
											<option value="YAML">YAML</option>
										</select>
									</div>
									<div class="form-group col-md-5 textarea-block">
										<label class="textarea_label" for="inputEmail4">Text</label>
										<textarea class="form-control textarea-height blocks_text" id="ck-content"
											name="blocks_text[]"></textarea>
									</div>
								</div>
								<input type="hidden" value="0" id="contact_id" name="contact_id">
								<div class="form-row addresscontainer">
								</div>
								<input type="hidden" value="1" id="total_blocks" name="total_blocks">
								<?php
							}
							?>

							<div class="form-group">
								<button class="btn btn-primary  add" type="button"
									style="float:right;margin-right: 120px;">Add Block</button><br><br>
							</div>
						</div>
					</div>

				</div>
			</div>

			<button type="submit" class="btn btn-primary" id="save_block">Submit</button>
		</form>
	</div>
</div>
<style>
	.textarea-height {
		height: 247px !important;
	}
</style>
<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script>
	CKEDITOR.replaceAll('myClassName');

	var id = "<?= @$webpage->id ?>";

	$(document).on('drop', '#drop_file_doc_zone', function (e) {

		// $("#ajax_load").show();
		console.log(e.originalEvent.dataTransfer);
		if (e.originalEvent.dataTransfer) {
			if (e.originalEvent.dataTransfer.files.length) {
				e.preventDefault();
				e.stopPropagation();
				var i = 0;
				while (i < e.originalEvent.dataTransfer.files.length) {
					newUploadDocFiles(e.originalEvent.dataTransfer.files[i], id);
					i++;
				}
			}
		}
	});


	$(document).on("change", ".fileUpload", function () {

		for (var count = 0; count < $(this)[0].files.length; count++) {

			newUploadDocFiles($(this)[0].files[count], id);
		}

	});



	function newUploadDocFiles(fileobj, id) {

		$("#ajax_load").hide();

		var form = new FormData();

		form.append("file", fileobj);
		form.append("mainTable", class_name);
		form.append("id", id);

		$.ajax({
			url: '/webpages/uploadMediaFiles',
			type: 'post',
			dataType: 'json',
			maxNumberOfFiles: 1,
			autoUpload: false,
			success: function (result) {

				$("#ajax_load").hide();
				if (result.status == '1') {
					$(".all-media-image-files").append(result.file_path);
				} else {
					toastr.error(result.msg);
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#ajax_load").hide();
				console.log(textStatus, errorThrown);
			},
			data: form,
			cache: false,
			contentType: false,
			processData: false
		});

	}

	$("#delete_image_logo").on("click", function (e) {
		e.preventDefault();
		$(".all-media-image-files").html("");
	})


	// Add the following code if you want the name of the file appear on select
	$(".custom-file-input").on("change", function () {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});

	// Tags functionality
	const webpageId = "<?= @$webpage->uuid ?>";

	function loadWebpageTags() {
		$.ajax({
			url: '/tags/list',
			method: 'GET',
			dataType: 'json',
			success: function(response) {
				if (response.data && Array.isArray(response.data)) {
					populateWebpageTagsSelect(response.data);
				}
			}
		});
	}

	function populateWebpageTagsSelect(tags) {
		const $select = $('#webpage_tags');

		// Populate select options
		tags.forEach(function(tag) {
			const option = new Option(tag.name, tag.id, false, false);
			$(option).attr('data-color', tag.color);
			$select.append(option);
		});

		// Initialize select2 with custom template
		$select.select2({
			placeholder: 'Select tags for this page...',
			allowClear: true,
			templateResult: formatWebpageTag,
			templateSelection: formatWebpageTagSelection
		});

		// Load currently assigned tags if editing
		if (webpageId) {
			loadCurrentWebpageTags(webpageId);
		}
	}

	function loadCurrentWebpageTags(webpageId) {
		$.ajax({
			url: '/tags/getEntityTags',
			method: 'GET',
			data: {
				entity_type: 'webpage',
				entity_id: webpageId
			},
			dataType: 'json',
			success: function(response) {
				if (response.data && Array.isArray(response.data)) {
					const currentTagIds = response.data.map(tag => tag.id);
					$('#webpage_tags').val(currentTagIds).trigger('change');
				}
			}
		});
	}

	function formatWebpageTag(tag) {
		if (!tag.id) return tag.text;

		const color = $(tag.element).data('color') || '#667eea';
		const $tag = $(
			'<span style="display: flex; align-items: center; gap: 8px;">' +
				'<span style="width: 12px; height: 12px; border-radius: 50%; background-color: ' + color + ';"></span>' +
				'<span>' + tag.text + '</span>' +
			'</span>'
		);
		return $tag;
	}

	function formatWebpageTagSelection(tag) {
		if (!tag.id) return tag.text;

		const color = $(tag.element).data('color') || '#667eea';
		return $('<span style="display: flex; align-items: center; gap: 6px;">' +
			'<span style="width: 10px; height: 10px; border-radius: 50%; background-color: ' + color + ';"></span>' +
			'<span>' + tag.text + '</span>' +
			'</span>');
	}

	// Load tags on page load
	$(document).ready(function() {
		loadWebpageTags();
	});

	// Save tags before form submission
	$('#addcat').on('submit', function(e) {
		if (webpageId) {
			e.preventDefault();
			const selectedTags = $('#webpage_tags').val() || [];

			$.ajax({
				url: '/tags/attach',
				method: 'POST',
				data: {
					entity_type: 'webpage',
					entity_id: webpageId,
					tag_ids: selectedTags
				},
				dataType: 'json',
				success: function(response) {
					// Now submit the main form
					$('#addcat').off('submit').submit();
				},
				error: function() {
					// Submit anyway if tag saving fails
					$('#addcat').off('submit').submit();
				}
			});
		}
	});
</script>

<style>
	.custom-file {
		margin: 30px;
	}
</style>




<script>
	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();

		var max_fields_limit = 10; //set limit for maximum input fields
		total_blocks = parseInt($('#total_blocks').val()); //initialize counter for text box

		$('.add').click(function (e) { //click event on add more fields button having class add_more_button

			$('.addresscontainer').append('<div class="form-row col-md-12 each-block" style="margin-bottom:30px;" id="office_address_' + total_blocks + '"><div class="form-group col-md-6">' +
				'<label for="inputSecretKey">Code</label>' +
				'<input type="text" class="form-control blocks_code" id="blocks_code' + total_blocks + '" name="blocks_code[]" placeholder="" value=""><br>' +

				'<label for="inputSecretValue">Title</label>' +
				'<input type="text" class="form-control" id="blocks_title' + total_blocks + '" name="blocks_title[]" placeholder="" value=""><br>' +
				'<label for="inputEmail4">Sort</label>' +
				'<input autocomplete="off" type="number" class="form-control"  name="sort[]" placeholder="" value="">' +

				'<label for="inputEmail4">Type</label>' +
				'<select name="type[]" id="text_type" class="form-control text_type">' +
				'<option value="TEXT">TEXT</option>' +
				'<option value="JSON">JSON</option>' +
				'<option value="LIST" >LIST</option>' +
				'<option value="WYSIWYG" >WYSIWYG</option>' +
				'<option value="MARKDOWN" >MARKDOWN	</option>' +
				'<option value="YAML">YAML</option>' +
				'</select>' +

				'</div>' +
				'<div class="form-group col-md-5 textarea-block">' +
				'<label class="textarea_label" for="inputSecretValue">Text</label>' +
				'<textarea class="form-control textarea-height blocks_text" id="blocks_text' + total_blocks + '" name="blocks_text[]" placeholder="" value="" ></textarea> ' +
				'</div> <input type="hidden" value="0" id="blocks_id" name="blocks_id[]">' +
				'<div class="form-group col-md-1 change">' +
				'<button class="btn btn-info bootstrap-touchspin-up deleteaddress" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>' +
				'</div></div>'
			);

			total_blocks++;

			CKEDITOR.replaceAll('myClassName');

			$('.deleteaddress').on("click", function (e) { //user click on remove text links

				$(this).parent().parent().remove();
				total_blocks--;

			})
		});
	});

	$('.deleteaddress').on("click", function (e) { //user click on remove text links
		var current = $(this);
		var blocks_id = current.closest(".each-row").find("#blocks_id").val();
		$.ajax({
			url: baseUrl + "/webpages/deleteBlocks",
			data: {
				blocks_id: blocks_id
			},
			method: 'post',
			success: function (res) {
				console.log(res)
				current.parent().parent().remove();
			}
		})

		total_blocks--;

	});
	var max_fields_limit = 10;
	var totalCustomFields = $('#total_custom_fields').val();
	$('.add-field').click(function (e) {
		// console.log({ totalCustomFields });
		if (totalCustomFields < max_fields_limit) { //check conditions
			totalCustomFields++; //counter increment

			$('.customFieldContainer').append(`
					<div class="form-row col-md-12" id="custom_fields_${totalCustomFields}">
						<div class="form-group col-md-3">
							<label for="customFieldName_${totalCustomFields}">Field Name</label>
							<input autocomplete="off" type="text" class="form-control" id="customFieldName_${totalCustomFields}" name="customFieldName[]" placeholder="" value="">
						</div>
						<div class="form-group col-md-5">
							<label for="customFieldValue_${totalCustomFields}">Field Value</label>
							<input autocomplete="off" type="text" class="form-control" id="customFieldValue_${totalCustomFields}" name="customFieldValue[]" placeholder="" value="">
						</div>
						<div class="form-group col-md-2 d-flex flex-column">
							<label for="customFieldType_${totalCustomFields}">Field Type</label>
								<select name="customFieldType[]" id="customFieldType_${totalCustomFields}" class="form-control text_type">
									<option value="TEXT">TEXT</option>
								</select>
						</div>
						<div class="form-group col-md-1 change">
							<button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="custom_fields" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>
						</div>
						<div class="form-group col-md-1 change">
							<button 
								class="btn btn-info bootstrap-touchspin-up clone-row" 
								data-type="custom_fields" 
								data-uuid="" 
								data-id="office_address_${totalCustomFields}"
								data-key="${totalCustomFields}"
								id="cloneRow" 
								type="button" 
								style="max-height: 35px;margin-top: 28px;margin-left: 10px;"
							>
								<i class="fas fa-clone"></i>
							</button>
						</div>
					</div>`);

		}
	});


	$(document).on('click', "#save_block", function (e) {
		var errorMessageStr = "";
		var code_arr = [];
		$(".each-block").each(function () {
			var blocks_code = $(this).find(".blocks_code").val();

			if (blocks_code.length == 0) {
				// alert("Blocks Code field is mandatory in each blocks.");
				$.notify(
					"Blocks Code field is mandatory in each blocks.", 
					"error",
					{ elementPosition:"bottom center" }
				)
				e.preventDefault();
				return false;
			}
			if (code_arr.indexOf(blocks_code) > -1) {
				errorMessageStr="Blocks Duplicate code field value is not allowed.";
				$.notify(
					"Blocks Duplicate code field value is not allowed.", 
					"error",
					{ elementPosition:"bottom center" }
				)
				e.preventDefault();
				return false;
			}
			code_arr.push(blocks_code);

		})
		// alert(errorMessageStr);

	})
	// $(document).on('click', ".blocks_code", function(e){
	// 	var code = $(this).val();
	// 	$(".each-block").each(function(){
	//         var blocks_code = $(this).find(".blocks_code").val();
	//        if( blocks_code == code ){
	//             alert("Duplicate code not allowed.");
	// 			e.preventDefault()
	//        }
	//     })
	// })


	$(document).on('click', ".tooltip-class", function () {
		let this_tooltip = $(this);
		let tooltip_value = this_tooltip.attr('data-original-title');
		if (this_tooltip.closest('.each-block').find('.text_type').val() == 'WYSIWYG') {
			let textarea_id = this_tooltip.closest('.each-block').find('.blocks_text').attr('id');
			CKEDITOR.instances[textarea_id].setData(tooltip_value);
		} else {
			this_tooltip.closest('.each-block').find('.blocks_text').val(tooltip_value);
		}
	});

	$(document).on('change', ".text_type", function () {
		var current = $(this);
		var text_type = $(this).val();

		let typeVal = current.closest('.each-block').find('.hidden_type_value').val();
		let textVal = current.closest('.each-block').find('.hidden_blocks_text_value').val();
		if (typeVal != text_type) {
			textVal = "";
		}

		let textarea_id = current.closest('.each-block').find('.blocks_text').attr('id');
		let random_bumber = Math.floor((Math.random() * 999999999) + 1);

		if (text_type == 'WYSIWYG') {
			current.closest('.each-block').find('.textarea-height').addClass('myClassName');
			CKEDITOR.replaceAll('myClassName');
			CKEDITOR.instances[textarea_id].setData(textVal);
			current.closest('.each-block').find('.textarea_label').html(text_type);
		} else {
			current.closest('.each-block').find('.textarea-block').html("");
			var html = '<label class="textarea_label" for="inputEmail4">' + text_type + '</label><textarea class="form-control textarea-height blocks_text" id="blocks_text' + random_bumber + '" name="blocks_text[]" spellcheck="false">' + textVal + '</textarea>';
			current.closest('.each-block').find('.textarea-block').html(html);
		}


		if (!current.closest('.each-block').find('.blocks_text').val()) {
			if (text_type == 'TEXT') {
				current.closest('.each-block').find('.blocks_text').attr("placeholder", 'Your text goes here');
				current.closest('.each-block').find('.tooltip-class').attr("data-original-title", 'Your text goes here');
			} else if (text_type == 'YAML') {
				var html = `# Employee records
- martin:
	name: Martin D'vloper
	job: Developer
	skills:
	  - python
	  - perl
	  - pascal
- tabitha:
	name: Tabitha Bitumen
	job: Developer
	skills:
	  - lisp
	  - fortran
	  - erlang`;
				current.closest('.each-block').find('.blocks_text').attr("placeholder", html);
				current.closest('.each-block').find('.tooltip-class').attr("data-original-title", html);
			} else if (text_type == 'JSON') {
				current.closest('.each-block').find('.blocks_text').attr("placeholder", '{ "example" : "some data in JSON format goes here"}');
			} else if (text_type == 'LIST') {
				current.closest('.each-block').find('.blocks_text').attr("placeholder", 'PHP JAVA NoteJs');
				current.closest('.each-block').find('.tooltip-class').attr("data-original-title", 'PHP JAVA NoteJs');
			} else if (text_type == 'MARKDOWN') {
				var html = `#### The quarterly results look great!- Revenue was off the chart.
						- Profits were higher than ever.

						*Everything* is going according to **plan**.`;
				current.closest('.each-block').find('.blocks_text').attr("placeholder", html);
				current.closest('.each-block').find('.tooltip-class').attr("data-original-title", html);
			}
		}
	})
</script>
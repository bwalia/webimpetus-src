<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>


<div class="white_card_body">
	<div class="card-body">

		<form id="addcat" autocomplete="off" method="post" action="/products/update" enctype="multipart/form-data">

			<input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$product->id ?>" />
			<input type="hidden" name="uuid" value="<?= @$product->uuid ?>" />

			<div class="row">
				<div class="col-xs-12 col-md-12">
					<nav>
						<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav-product-detail-tab" data-toggle="tab" href="#nav-product-detail" role="tab" aria-controls="nav-product-detail" aria-selected="true">Product Details</a>
							<a class="nav-item nav-link" id="nav-product-image-tab" data-toggle="tab" href="#nav-product-image" role="tab" aria-controls="nav-product-image" aria-selected="false">Product Images(s)</a>
						</div>
					</nav>
					<div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
						<div class="tab-pane fade show active" id="nav-product-detail" role="tabpanel" aria-labelledby="nav-product-detail-tab">

							<div class="white_card_body">
								<div class="card-body">
									<div class="form-row">
										<div class="col-md-8">
											<div class="form-group row">
												<label for="name" class="col-sm-3 col-form-label">Product Name*</label>
												<div class="col-sm-6">
													<input type="text" required class="form-control" value="<?= @$product->name ?>" id="name" name="name" placeholder="">
												</div>
											</div>
											<div class="form-group row">
												<label for="code" class="col-sm-3 col-form-label">Product Code*</label>
												<div class="col-sm-6">
													<input type="text" required class="form-control" value="<?= @$product->code ?>" id="code" name="code" placeholder="">
												</div>
											</div>
											<div class="form-group row">
												<label for="description" class="col-sm-3 col-form-label">Product Description*</label>
												<div class="col-sm-8">
													<textarea class="form-control" required name="description" id="content"><?= @$product->description ?></textarea>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-3 col-form-label" for="category">Product Category*</label>
												<div class="col-sm-6">


													<select onchange="" required class="form-control dashboard-dropdown" name="category" id="category">
														<option value="">-- Select Product Category--</option>
														<?php
														foreach ($categoryList as $category) {
														?>
															<option value="<?php echo $category["uuid"] ?>" <?= @$product->uuid_category == $category["uuid"] ? 'selected' : '' ?>><?php echo $category["name"] ?></option>
														<?php
														}
														?>
													</select>
												</div>
											</div>



											<div class="white_card_body">
												<h3 class="card-title">Specification</h3>
												<div class="card-body">

													<div class="form-group row">
														<div class="col-md-4">
															Specification Name
														</div>

														<div class="col-md-4">
															Specification Value
														</div>
														<div class="col-md-4">
															Remove
														</div>
													</div>

													<div class="form-group row">
														<div id="czContainer">

															<div id="first">

																<div class="recordset">
																	<div class="row mb-2">
																		<div class="col-md-6 p-2 border">
																			<input type="text" name="spec_1_name" id="spec_1_name" class="textinput form-control" />
																		</div>
																		<div class="col-md-6 p-2 border">
																			<input class=" form-control" id="spec_1_value" name="spec_1_value" type="text" />
																		</div>
																	</div>
																</div>

															</div>



															<?php
															if (sizeof($specifications) > 0) {

																foreach ($specifications as $ind => $spec) {
															?>
																	<div class="recordset">
																		<div class="row mb-2">
																			<div class="col-md-6 p-2 border">
																				<input value="<?= $spec["key_name"] ?>" type="text" name="spec_<?= ($ind + 1) ?>_name" id="spec_<?= ($ind + 1) ?>_name" class="textinput form-control">
																			</div>
																			<div class="col-md-6 p-2 border">
																				<input value="<?= $spec["key_value"] ?>" class=" form-control" name="spec_<?= ($ind + 1) ?>_value" id="spec_<?= ($ind + 1) ?>_value" type="text">
																			</div>
																		</div>
																	</div>
															<?php
																}
															}
															?>

														</div>
													</div>
												</div>
											</div>




										</div>

										<div class="col-md-4">
											<div class="form-group row">
												<label for="sku" class="col-sm-6 col-form-label">Product SKU*</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" value="<?= @$product->sku ?>" id="sku" name="sku" placeholder="">
												</div>
											</div>
											<div class="form-group row">
												<label for="sku" class="col-sm-6 col-form-label">Publish on the website</label>
												<div class="col-sm-6">
													<input <?= (@$product->is_published == 1) ? 'checked' : '' ?> type="checkbox" class="form-control-lg" value="<?= @$product->is_published ?>" id="is_published" name="is_published">
												</div>
											</div>
											<div class="form-group row">
												<label for="stock_available" class="col-sm-6 col-form-label">Stock Available <span class="text-danger">(optional)</span></label>
												<div class="col-sm-6">
													<input type="text" class="form-control" value="<?= @$product->stock_available ?>" id="stock_available" name="stock_available" placeholder="">
												</div>
											</div>
											<div class="form-group row">
												<label for="unit_price" class="col-sm-6 col-form-label">Unit Price*</label>
												<div class="col-sm-6">
													<input type="text" required class="form-control" value="<?= @$product->unit_price ?>" id="unit_price" name="unit_price" placeholder="">
												</div>
											</div>
											<div class="form-group row">
												<label for="sort_order" class="col-sm-6 col-form-label">Sort Order</label>
												<div class="col-sm-6">
													<input type="text" class="form-control" value="<?= @$product->sort_order ?>" id="sort_order" name="sort_order" placeholder="">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="nav-product-image" role="tabpanel" aria-labelledby="nav-product-image-tab">
							<div class="form-row">
								<div class="form-group col-md-12">
									<span class="all-media-image-files">
										<?php foreach ($images as $image) {
											if (!empty(@$image)) { ?>
												<img class="img-rounded" src="<?= $image['name']; ?>" width="100px">
												<a href="/products/rmimg/<?= @$image['id'] . '/' . @$product->uuid; ?>" onclick="return confirm('Are you sure?')" class=""><i class="fa fa-trash"></i></a>
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
					</div>

				</div>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
	</div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>
<script src="/assets/js/jquery.czMore-latest.js"></script>

<style>
	.btnPlus,
	.btnMinus {
		color: #FFF;
		margin: 5px;
		position: relative;
	}

	.btnPlus {
		background-color: #00ff4e;
	}

	.btnMinus {
		background-color: #65c46f;
	}

	.btnPlus::after,
	.btnMinus::after {
		padding: 8px;
		font-weight: bold;
	}

	.btnPlus::after {
		content: "+";
	}

	.btnMinus::after {
		content: "-";
	}
</style>


<script>
	CKEDITOR.replaceAll('myClassName');

	var id = "<?= @$webpage->id ?>";

	$(document).on('drop', '#drop_file_doc_zone', function(e) {
		// $("#ajax_load").show();
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


	$(document).on("change", ".fileUpload", function() {
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
			url: '/products/uploadMediaFiles',
			type: 'post',
			dataType: 'json',
			maxNumberOfFiles: 1,
			autoUpload: false,
			success: function(result) {
				$("#ajax_load").hide();
				if (result.status == '1') {
					$(".all-media-image-files").append(result.file_path);
				} else {
					toastr.error(result.msg);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#ajax_load").hide();
				console.log(textStatus, errorThrown);
			},
			data: form,
			cache: false,
			contentType: false,
			processData: false
		});

	}

	$("#delete_image_logo").on("click", function(e) {
		e.preventDefault();
		$(".all-media-image-files").html("");
	})


	// Add the following code if you want the name of the file appear on select
	$(".custom-file-input").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});

	// $("#czContainer").czMore({
	// 	max: 5,
	// 	styleOverride: true
	// });

	$("#czContainer").czMore({
		// max: 5,
		min: 1,
		styleOverride: false,
		countFieldPrefix: '_czMore_specCount'
	});


	$("#name").on("input", function() {
		const str = $(this).val().trim();

		var result = str.replace(/[^\w\s]/gi, '-');
		result = result.replace(/\s\s+/g, ' ');
		result = result.replace(/ /g, "-");
		result = result.replace(/^\W+|\W+$/g, "");
		result = result.replace(/-+/g, '-');

		$("#code").val(result.toLowerCase());
	});
</script>
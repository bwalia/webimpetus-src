<?php require_once(APPPATH . 'Views/common/edit-title.php'); ?>

<div class="white_card_body">
	<div class="card-body">

		<form id="enqform" method="post" action="/enquiries/update" enctype="multipart/form-data">

			<input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$enquiries->id ?>" />
			<input type="hidden" class="form-control" name="uuid" placeholder="" value="<?= @$enquiries->uuid ?>" />

			<input type="hidden" class="form-control" name="type" placeholder="" value="4" />

			<div class="row">
				<div class="col-xs-12 col-md-12">

					<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
						<div class="form-row">
							<div class="form-group col-md-12 ">
								<label for="inputEmail4">Name</label>
								<input type="text" class="form-control " value="<?= @$enquiries->name ?>" id="name" name="name" placeholder="">
							</div>

							<div class="form-group col-md-12 ">
								<label for="inputEmail4">Email</label>
								<input type="text" class="form-control " id="email" name="email" placeholder="" value="<?= @$enquiries->email ?>">
							</div>

							<div class="form-group col-md-12 ">
								<label for="inputPassword4">Phone</label>
								<input class="form-control " name="phone" id="phone" value="<?= @$enquiries->phone ?>" />
							</div>

							<div class=" form-group col-md-12 ">
								<label for=" inputPassword4">Message</label>
								<textarea class="form-control " name="message" id="message"><?= @$enquiries->message ?></textarea>
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
	$(".custom-file-input").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
 <script>
   if ($("#enqform").length > 0) {

	$.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Only alphabetical characters"); 


      $("#enqform").validate({
    rules: {
      name: {
        required: true,
		minlength: 5,
		lettersonly: true 
      }, 
      email: {
        required: true,
		email: true
      }, 
      phone: {
        required: true,
		digits:true,
		minlength: 10,
		maxlength: 10

      }  
    },
    messages: {
      name: {
        required: "Please enter name",
      },
      email: {
        required: "Please enter valid email",
        email: "Please enter valid email",
        maxlength: "The email name should less than or equal to 50 characters",
        },      
     password: {
        required: "Please enter password",
      },
        
    },
  })
}
</script>
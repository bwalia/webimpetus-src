var required_field_validation_msg = "This field is required";
var email_validation_msg = "Enter valid email address";
var phone_validation_msg ='Use valid format like <strong>+31651150036</strong>';
var number_validation_msg ='Enter valid number';
var acc_validation_msg ='Account number should be number and in between 9 to 18 digits';
var common_validation_msg ='You have some form errors please check below';
var only_alphabets_msg = 'Only alphabets allowed!!';

$(":submit").click( function ( e ) {
    //call form validation function
    var response = checkFormValidation();
    if(response.status){
        return response.status;
    }else if (response.message.length == 0) {
       return true;
       }else{
        var err_main_message = "<div class='alert alert-block alert-danger m-alert m-alert--air fade show'>";
        err_main_message += "<button type='button' class='close' data-dismiss='alert'></button>";                    
        err_main_message += "<p>"+response.message+"</p>";                       
        err_main_message += "</div>";
       jQuery('div.m-content').prepend(err_main_message); 
        e.preventDefault();
       return response.status;
    }

});

function checkFormValidation(){
	
	
	var response = new Object();
	response.message = "";
	response.status = true;
	var validation = true;

    $('input').each(function(){

        var is_req = $(this).hasClass('required');
        var is_phone = $(this).hasClass('phone');
        var is_email = $(this).hasClass('email');
        var is_number = $(this).hasClass('number');
        var acc_number = $(this).hasClass('acc_number');
        var only_alpha = $(this).hasClass('only_alpha');
		
		var field_value = $(this).val();
		var field_type = $(this).attr('type');

		if ( field_type == 'file' ) {

			var is_file = true;
		}else {
			var is_file = false;
		}


        var field_value = $(this).val();
		var field_name = jQuery(this).closest(".form-group").find("label").attr('name');
		
		jQuery(this).closest(".form-group").removeClass('has-danger');
		jQuery(this).parent("div").children("div.form-control-feedback").remove();

        if (field_value.length  < 1 && is_req && is_file == false){
			jQuery(this).closest(".form-group").addClass('has-danger');
			jQuery(this).parent("div").children("div.form-control-feedback").remove();
			var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+required_field_validation_msg+"</div>";
			jQuery(this).after(error_msg);			
            validation =  false;
        }

		var regex = /^[a-zA-Z]*$/;
		if (only_alpha && !regex.test(field_value)) {
	  
			$(this).focus();
			jQuery(this).closest(".form-group").addClass('has-danger');
			jQuery(this).parent("div").children("form-control-feedback").remove();
			var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+only_alphabets_msg+"</div>";
			jQuery(this).after(error_msg);
			validation =  false;
		}
		
		if ( is_file == true && field_value.length  < 1 && is_req ) {

			var linkExists = $("#field_id_"+field_name).find("a").length;
			var imgExists = $("#field_id_"+field_name).find("img").length;
			var checkBox = $("#field_id_"+field_name).find(".delete-checkbox").prop("checked");

			if ( linkExists == '0'  && imgExists == '0' && checkBox == false) {

				jQuery(this).closest(".form-group").addClass('has-danger');
				jQuery(this).parent("div").children("div.form-control-feedback").remove();
				var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+required_field_validation_msg+"</div>";
				jQuery(this).after(error_msg);			
				validation =  false;

			}else {
				$(this).removeAttr("required");
			}
		}

		if(is_email && field_value.length >0){
			if( !isValidEmailAddress( field_value ) ) {
				$(this).focus();
				jQuery(this).closest(".form-group").addClass('has-danger');
				jQuery(this).parent("div").children("div.form-control-feedback").remove();
				var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+email_validation_msg+"</div>";
                //alert(error_msg);
				jQuery(this).after(error_msg);
				validation =  false;
			}
		}
        
			if(is_phone){
				var patt1 = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,7}$/im;
				var result = field_value.match(patt1);
				if(!result){
					$(this).focus();
					jQuery(this).closest(".form-group").addClass('has-danger');
					jQuery(this).parent("div").children("form-control-feedback").remove();
					var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+phone_validation_msg+"</div>";
					jQuery(this).after(error_msg);
					validation =  false;
				}
			}
			
			if(is_number && isNaN( field_value )){
				$(this).focus();
				jQuery(this).closest(".form-group").addClass('has-danger');
				jQuery(this).parent("div").children("div.form-control-feedback").remove();
				var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+number_validation_msg+"</div>";
				jQuery(this).after(error_msg);
				validation =  false;					
			}

			if(acc_number && (field_value.length  < 9 || field_value.length  > 18 || !isNumeric( field_value ))){
				$(this).focus();
				jQuery(this).closest(".form-group").addClass('has-danger');
				jQuery(this).parent("div").children("div.form-control-feedback").remove();
				var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+acc_validation_msg+"</div>";
				jQuery(this).after(error_msg);
				validation =  false;					
			}
			

    });

	function isNumeric(value) {
		return /^-?\d+$/.test(value);
	}

	$('textarea').each(function(){

		var is_req = $(this).hasClass('required');
		var editor = $(this).hasClass('ckeditor');
		var name = $(this).attr('name');
		var field_name = jQuery(this).closest(".form-group").find("label").attr('name');
		
		if (is_req == true){

			if(editor){
				var field_value = CKEDITOR.instances[name].getData();
			} else{
				var field_value = $(this).val();
			}
			
			if (field_value.length  < 1){
				jQuery(this).closest(".form-group").addClass('has-danger');
				jQuery(this).parent("div").children("div.form-control-feedback").remove();
				var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+required_field_validation_msg+"</div>";
				if( editor ) { jQuery(this).next().after(error_msg); } else { jQuery(this).after(error_msg); }
				validation =  false;
			}
		}
	});

	$('select').each(function(){
	
		var is_req = $(this).hasClass('required');
	
		if (is_req == true){
	
			var field_value = $(this).val();
			var field_name = jQuery(this).closest(".form-group").find("label").attr('name');
			if (  field_value == null || (field_value.length  < 1 || field_value == " ") ){						
				jQuery(this).closest(".form-group").addClass('has-danger');
				$(this).addClass('select2me');
				$(this).attr('aria-describedby','options2-error');
				$(this).attr('aria-required','true');
				$(this).attr('aria-invalid','true');
				
				jQuery(this).parent("div").children("div.form-control-feedback").remove();
				var error_msg = "<div id='"+field_name+"' class='form-control-feedback'>"+required_field_validation_msg+"</div>";
				//jQuery(this).parent("div").append(error_msg);
				var detect_select2 = jQuery(this).parent("div").children("span.select2");
				jQuery(error_msg).insertAfter(detect_select2);
				validation =  false;
			}
		}
	});
	
	if(validation){
		response.message = "";
		response.status = true;
		return response;		
	
	}else{
		response.message = common_validation_msg;
		response.status = false;
		return response;
	}
};

function isValidEmailAddress(emailAddress) {
	var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
	return pattern.test(emailAddress);
};


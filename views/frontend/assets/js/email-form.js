var EMAIL_FORM = function($FORM) {

	var MODULE = this,
		BLOCK_ID = $FORM.data("block");
	
	var VALIDATOR = new FORM_VALIDATOR($FORM, {
		
		"onValid": function(CONFIG, OPTIONS) {

			$('.contact-form-submit').attr("disabled","true");

			VALIDATOR["message"]("Sending contact form...");
			
			var FROM = CONFIG["from"];
			
			// Record Audience Builder Conversion
			if(typeof(report_form_block_conversion) == "function") { report_form_block_conversion(BLOCK_ID); }

			// Post to CMS backend
			CMS.email.process(BLOCK_ID, $FORM, { 
				name: FROM["name"], 
				email: FROM["email"]
			}, function(R) {

				if(typeof(R["status"]) == "undefined") {
					
					VALIDATOR["message"](OPTIONS["messages"]["unknown_error"], "error");
					
				} else {
					
					if(R["status"] == "success") {
						
						VALIDATOR["customSuccessMessage"]();
							
						VALIDATOR["message"](OPTIONS["messages"]["success"], R["status"]);
						
						$FORM[0].reset();

						$(".input-field").each(function () {
							if ($(this).val().length < 1) {
								$(this).prev('.floating-form-label').removeClass('focused');
							}
						});
						
					} else {
						
						VALIDATOR["message"](R["message"], "error");
						
					}
					
				}

				$('.contact-form-submit').removeAttr("disabled"); 
			
			});
			
		}
		
	});
	
	return MODULE;

}
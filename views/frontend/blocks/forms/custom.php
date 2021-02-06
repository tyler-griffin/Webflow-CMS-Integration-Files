<?

/* Move custom_form() to /controllers/exteded.php

	function custom_form() {
		 
		$BLOCK_ID = $this->input->post("block_id");  
		$FROM = $this->input->post("from");
		$SERIALIZED = $this->input->post("serialized");
		 
		if($BLOCK_ID && $FROM && $SERIALIZED) {
	 
			// BEGIN Custom Job ------------------------ //
			 
			// Contains form POST in key => value format
			$POST = serializedToArray($SERIALIZED);
			 
			// Save to database, fire off a custom email, etc...
	 
			// END Custom Job -------------------------- //
			 
			// Check if the backend block has 1 or more notification email addresses set
			$FORM_CONFIG = getFormConfig($BLOCK_ID, $SERIALIZED);
			 
			// If so, fire off the standard Cybernautic "leads" email to the client
			if($FORM_CONFIG && sizeof($FORM_CONFIG["to"]) > 0) {
			 
				$EMAIL_TEMPLATE = buildEmailTemplate($SERIALIZED);

				// Save to backend
				$this->load->model("email/email_model");
				$insert_id = $this->email_model->save($BLOCK_ID, $EMAIL_TEMPLATE["json"]);


				 
				$SEND = sendEmail(Array(
				 
					"subject" => $FORM_CONFIG["subject"],
					"to"	  => $FORM_CONFIG["to"],
	 
					// Send from cms@cybernautic.com
					"from"	=> $this->config->item('system_messages_from'),
					 
					// Set replyto to the email from the form post (optional)
					//"replyto" => $POST["email"],
					 
					"html"	=> $EMAIL_TEMPLATE["html"],
					"text"	=> $EMAIL_TEMPLATE["text"]
					 
				));
				  
				if($SEND["status"] == "success") {
				  
					echo json("success", "E-mail sent successfully!");
					die;
				  
				} else {
				  
					echo json("error", "Error sending email.");
					die;
				  
				}
			 
			} else {
				echo json("success", "Success");
				die;
			}		  
			 
		} else {
			echo json("error", "Missing Configuration.");
			die;
		}
		 
	}
*/

?>
	
	<div class="cms-contact-form block" data-id="<?= $block->id ?>">

		<form data-module="validator" class="form" data-block="<?= $block->id ?>">

			<div class="form-input third">
				<label class="floating-form-label" for="Name">Name</label>
				<input class="input-field w-input" data-name="true" id="Name" maxlength="256" name="Name" data-required type="text">
			</div>

			<div class="form-input third">
				<label class="floating-form-label" for="Email">Email</label>
				<input class="input-field w-input" id="Email" maxlength="256" name="Email" data-required type="text" data-email="true">
			</div>

			<div class="form-input third">
				<label class="floating-form-label" for="Phone">Phone</label>
				<input class="input-field w-input" id="Phone" maxlength="256" name="Phone" data-required type="text" data-mask="(999) 999-9999">
			</div>

			<div class="form-input">
				<textarea placeholder="Message" class="input-field text-area-input w-input" id="Message" maxlength="5000" name="Message"></textarea>
			</div>

			<div class="form-input">
				<div class="cms-btn contact-form-submit w-button">Submit</div>
			</div>
			
			<span class="contact-form-loading-icon"></span>
			<span class="contact-form-message"></span>	

		</form>

	</div>

	<script type="text/javascript">

		var $FORMS = $("[data-module='validator']");
		
		$FORMS.each(function() {
			 
			var $FORM = $(this);
		 
			var VALIDATOR = new FORM_VALIDATOR($FORM, {
				"onValid": function(CONFIG) {
					 
					VALIDATOR["message"]("Processing...");
					 
					$.post("/modules/extended/custom_form", CONFIG, function(R) {
						 
						if(R["status"] == "success") {
						 
							VALIDATOR["customSuccessMessage"]();
						 
						}
						 
						VALIDATOR["message"](R["message"], R["status"], 5000);
						 
					}, 'json');
					 
				}
			 
			});
			 
		});
		
	</script>

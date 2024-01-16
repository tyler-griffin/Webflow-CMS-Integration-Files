<?
​
/* Move custom_form() to /controllers/exteded.php
​
	function custom_form() {
                
        // Data posted from form-validator.js
        $BLOCK_ID = $this->input->post("block_id"); 
        $FROM = $this->input->post("from");
        $SERIALIZED = $this->input->post("serialized");
        
        // Make sure all data was posted
        if($BLOCK_ID && $FROM && $SERIALIZED) {
​
            // Clean any attempted XSS from the POST
            $BLOCK_ID = $this->security->xss_clean($BLOCK_ID);
            $FROM = $this->security->xss_clean($FROM);
            $SERIALIZED = $this->security->xss_clean($SERIALIZED);
    
            // This is where you might do something custom with the data
            // -------------------------------------------------------------
    
            // Turn $SERIALIZED into "key" => "value" array
            $POST = serializedToArray($SERIALIZED);
    
            // Below is exactly what the core "email-form" would do
            // -------------------------------------------------------------
            
            // Save to database (saves to `module_email`)
            $SAVE = saveFormMessage($BLOCK_ID, $SERIALIZED);
    
            // Check status of the save
            if($SAVE["status"] == "success") {
                
                // Check if the backend block has 1 or more notification email addresses set
                $FORM_CONFIG = getFormConfig($BLOCK_ID, $SERIALIZED);
                
                // If so, fire off the standard Cybernautic "leads" email to the client
                // (Unless it has been marked as spam)
                if(
                    !$SAVE["spam"] &&
                    $FORM_CONFIG &&
                    count($FORM_CONFIG["to"]) > 0
                ) {
                
                    // Get E-mail Template
                    $EMAIL_TEMPLATE = buildEmailTemplate($SERIALIZED);
                    
                    // Send E-Mail
                    $SEND = sendEmail([
                    
                        "subject" => $FORM_CONFIG["subject"],
                        "to"      => $FORM_CONFIG["to"],
                        "replyto" => $FORM_CONFIG["replyto"],
    
                        // Send from cms@cybernautic.com
                        "from"    => $this->config->item('system_messages_from'),
                        
                        "html"    => $EMAIL_TEMPLATE["html"],
                        "text"    => $EMAIL_TEMPLATE["text"]
                        
                    ]);
                    
                    // E-mail was sent successfully
                    if($SEND["status"] == "success") {
                    
                        echo json("success", "E-mail sent successfully!");
                        die;
                    
                    // E-mail was not sent successfully
                    } else {
                    
                        echo json("error", "Error sending email.");
                        die;
                    
                    }
                
                // Either it was flagged as spam, or there are no recipients
                // configured in the CMS block
                } else {
                    echo json("success", "Success");
                    die;
                }
    
            // Saving to the database was not successful
            } else {
    
                echo json("error", $SAVE["message"]);
                die;
    
            }     
            
        // Missing configuration
        } else {
            echo json("error", "Missing Configuration.");
            die;
        }
        
    }
*/
​
?>
	
	<div class="cms-contact-form block" data-id="<?= $block->id ?>">
​
		<form data-module="email-form" class="form" data-block="<?= $block->id ?>">
​
			<div class="form-input third">
				<label class="floating-form-label" for="Name">Name</label>
				<input class="input-field w-input" data-name="true" id="Name" maxlength="256" name="Name" data-required type="text">
			</div>
​
			<div class="form-input third">
				<label class="floating-form-label" for="Email">Email</label>
				<input class="input-field w-input" id="Email" maxlength="256" name="Email" data-required type="text" data-email="true">
			</div>
​
			<div class="form-input third">
				<label class="floating-form-label" for="Phone">Phone</label>
				<input class="input-field w-input" id="Phone" maxlength="256" name="Phone" data-required type="text" data-mask="(999) 999-9999">
			</div>
​
			<div class="form-input">
				<textarea placeholder="Message" class="input-field text-area-input w-input" id="Message" maxlength="5000" name="Message"></textarea>
			</div>
​
			<div class="form-input">
				<div class="cms-btn contact-form-submit w-button">Submit</div>
			</div>
			
			<span class="contact-form-loading-icon"></span>
			<span class="contact-form-message"></span>	
​
		</form>
​
	</div>
​
	<script type="text/javascript">
​
		var $FORMS = $("[data-module='validator']");
        
        $FORMS.each(function() {
            
            var $FORM = $(this),
                PROCESSING = false;
        
            var VALIDATOR = new FORM_VALIDATOR($FORM, {
                "onValid": function(CONFIG) {
                
                    if(PROCESSING) { return false; }
                
                    PROCESSING = true;
                    
                    VALIDATOR["message"]("Processing...");
                    
                    $.post("/modules/extended/custom_form", CONFIG, function(R) {
                    
                        PROCESSING = false;
                        
                        if(R["status"] == "success") {
                        
                            if(typeof(report_form_block_conversion) == "function") { report_form_block_conversion(CONFIG["block_id"]); }
                        
                            VALIDATOR["customSuccessMessage"]();
                        
                        } else {
        
                            if(
                                typeof(R["data"]) != "undefined" &&
                                typeof(R["data"]["fields"] != "undefined")
                            ) {
        
                                $.each(R["data"]["fields"], function(i, name) {
        
                                    $('[name="' + name + '"]', $FORM).addClass("form-invalid");
        
                                });
        
                            }
        
                        }
                        
                        VALIDATOR["message"](R["message"], R["status"], 5000);
                        
                    }, 'json');
                    
                }
            
            });
            
        });
		
	</script>
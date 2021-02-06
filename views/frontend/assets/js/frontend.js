$(document).ready(function() {

	///////////////////////////////////////////////////////////////////////
	// Contact Form
	///////////////////////////////////////////////////////////////////////

	$.getScript(CMS.assets.local + '/js/form-validator.js', function() {
		$.getScript(CMS.assets.local + '/js/email-form.js', function() {

			$("form[data-module='email-form']").each(function(i,v) { new EMAIL_FORM($(this)); });

		});
	});

});

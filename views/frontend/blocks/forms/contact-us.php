
	<div class="cms-contact-form" data-id="<?= $block->id ?>">

		<form data-module="email-form" class="form" data-block="<?= $block->id ?>">

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
				<div class="cms-btn contact-form-submit w-button"><span class="button-icon"><i class="fas fa-paper-plane"></i></span> Submit</div>
			</div>
			
			<span class="contact-form-loading-icon"></span>
			<span class="contact-form-message"></span>	

		</form>

	</div>
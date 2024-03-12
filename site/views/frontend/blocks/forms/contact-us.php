
	<div class="cms-contact-form" data-id="<?= $block->id ?>">

		<form data-module="email-form" class="form" data-block="<?= $block->id ?>">

			<div class="form-input half">
				<label class="floating-form-label" for="First_Name">First Name</label>
				<input class="input-field w-input" data-name="true" id="First_Name" maxlength="256" name="First_Name" data-required type="text">
			</div>

			<div class="form-input half">
				<label class="floating-form-label" for="Last_Name">Last Name</label>
				<input class="input-field w-input" data-name="true" id="Last_Name" maxlength="256" name="Last_Name" data-required type="text">
			</div>

			<div class="form-input half">
				<label class="floating-form-label" for="Email">Email</label>
				<input class="input-field w-input" id="Email" maxlength="256" name="Email" data-required type="text" data-email="true">
			</div>

			<div class="form-input half">
				<label class="floating-form-label" for="Phone">Phone</label>
				<input class="input-field w-input" id="Phone" maxlength="256" name="Phone" data-required type="text" data-mask="(999) 999-9999">
			</div>

			<div class="form-input">
				<textarea placeholder="Message" class="input-field text-area-input w-input" id="Message" maxlength="5000" name="Message"></textarea>
			</div>

			<div class="form-input">
				<button class="cms-btn contact-form-submit w-button"><span class="button-icon"><i class="fas fa-paper-plane"></i></span>Submit</button>
			</div>
			
			<span class="contact-form-loading-icon"></span>
			<span class="contact-form-message"></span>	

		</form>

	</div>
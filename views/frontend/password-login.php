
<? include(FRONTEND . "/header.php"); ?>

<? // Create variable for title text
if(isset($page->profile->title)) {
	$titleText = $page->profile->title;
} else if($page->settings["Title Override"]) {
	$titleText = $page->settings["Title Override"];
} else {
	$titleText = $page->title;
} ?>

<? // Create variable for subtitle text
if(isset($page->settings["Subtitle"])) {
	$subtitleText = $page->settings["Subtitle"];
}
if(isset($page->profile->sub_title)) {
	$subtitleText = $page->profile->sub_title;
}
if(isset($page->profile->publish_date)) {
	$subtitleText = date("F j, Y", strtotime($page->profile->publish_date));
} ?>

<? // Create variable for interior banner image
if(isset($page->settings["Page Banner"])) {
	$interiorBanner = json_decode($page->settings["Page Banner"]);
} ?>

<div class="title-section" <? if(isset($interiorBanner)) { ?>style="background-position: <?= $interiorBanner->config->{'background-position'} ?>; background-image: url('/image/<?= $interiorBanner->id ?>/2000');"<? } ?>>
	<div class="title-content-outer-wrapper">
		<div class="title-content-inner-wrapper">
			<h1 class="title"><?= $titleText ?></h1>
			<? if(isset($subtitleText)) { ?><h2 class="subtitle-text"><?= $subtitleText ?></h2><? } ?>
		</div>
	</div>
</div>

<div class="main-content-section">
	<div class="content-container">

		<div class="password-form" id="password-form">
			
			<? if(isset($error)) { ?>
				<div class="password-form-error" id="password-form-error"><?= $error ?></div>
			<? } else { ?>
				<div class="password-form-error" id="password-form-error">This page is password protected.</div>
			<? } ?>
		
			<form method="post" class="form">

				<div class="form-input">
					<label class="floating-form-label" for="password">Enter Password</label>
					<input class="input-field w-input" type="password" id="password" name="password" type="text">
				</div>

				<div class="form-input">
					<button class="cms-btn contact-form-submit">Login</button>
				</div>

			</form>
		
		</div>

	</div>
</div>

<? include(FRONTEND . "/footer.php"); ?>

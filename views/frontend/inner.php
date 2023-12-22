<? include(FRONTEND . "/header.php"); ?>

<? // Create variable for title text
if($page->settings["Title Override"]) {
	$titleText = $page->settings["Title Override"];
} else {
	$titleText = $page->title;
} ?>

<? // Create variable for subtitle text
if(isset($page->settings["Subtitle"])) {
	$subtitleText = $page->settings["Subtitle"];
} ?>

<? // Create variable for interior banner image
if(isset($COMMON_ITEMS["Default Page Banner"])) {
	$interiorBanner = json_decode($COMMON_ITEMS["Default Page Banner"]);
} else if(isset($page->settings["Page Banner"])) {
	$interiorBanner = json_decode($page->settings["Page Banner"]);
} ?>

<div class="title-section" <? if(isset($interiorBanner)) { ?>style="background-position: <?= $interiorBanner->config->{'background-position'} ?>; background-image: url('/image/<?= $interiorBanner->id ?>/2000');"<? } ?>>
	<div class="title-content-outer-wrapper">
		<div class="title-content-inner-wrapper">
			<h1 class="title"><?= $titleText ?></h1>
			<? if(isset($subtitleText)) { ?><div class="subtitle-text"><?= $subtitleText ?></div><? } ?>
		</div>
	</div>
</div>

<div class="main-content-section">
	<div class="content-container">

		<? // Sitemap and Page Not Found
		if($page->type == 200) { ?>

			<div class="text-block">
				<? printSiteMap(); ?>
			</div>

		<? // Search results page
		} else if($page->type == 5) {

			$this->customsearch_model->display_results();

		// Every other interior page
		} else {

			printBlocks($page);

		} ?>

	</div>
</div>

<? include(FRONTEND . "/footer.php"); ?>

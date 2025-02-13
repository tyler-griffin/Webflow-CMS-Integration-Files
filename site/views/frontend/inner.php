<? include(FRONTEND . "/header.php"); ?>

<? // Create variable for title text
if($page->settings["Banner Title"]) {
	$titleText = $page->settings["Banner Title"];
} ?>

<? // Create variable for subtitle text
if(isset($page->settings["Banner Subtitle"])) {
	$subtitleText = $page->settings["Banner Subtitle"];
} ?>

<? // Create variable for interior banner image
if(isset($page->settings["Banner Image"])) {
	$interiorBanner = json_decode($page->settings["Banner Image"]);
} else if(isset($COMMON_ITEMS["Default Page Banner Image"])) {
	$interiorBanner = json_decode($COMMON_ITEMS["Default Page Banner Image"]);
} ?>

<div class="title-section" <? if(isset($interiorBanner)) { ?>style="background-position: <?= $interiorBanner->config->{'background-position'} ?>; background-image: url('/image/<?= $interiorBanner->id ?>/2000');"<? } ?>>
	<div class="title-content-outer-wrapper">
		<div class="title-content-inner-wrapper">
			<h1 class="title"><?= $titleText ?></h1>
			<? if(isset($subtitleText)) { ?><div class="subtitle-text"><?= $subtitleText ?></div><? } ?>
			<? /* AMSD select/dropdown filters --- include(FRONTEND . "/partials/amsd-filters.php"); */ ?>
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

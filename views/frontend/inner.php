
<?

/*

	Code in the file assumes the following variables exist for each page using a custom page settings block

		$page->settings["Page Banner"]
		$page->settings["Title Override"]
		$page->settings["Subtitle"]

	Custom page settings can be set in the customPageSettingsSchema found in /helpers/pagebuilder_custom_helper.php, examples below:
	
		$items[] = [
	        "key" => "Page Banner",
	        "value" => NULL,
	        "config" => "focused_img"
	    ];

	    $items[] = [
	        "key" => "Title Override",
	        "value" => NULL,
	        "config" => "text"
	    ];

	    $items[] = [
	        "key" => "Subtitle",
	        "value" => NULL,
	        "config" => "text"
	    ];

*/

?>


<? include(FRONTEND . "/header.php"); ?>

<? // Create variable for title text
if(isset($page->profile->title)) {
	$titleText = $page->profile->title;
} else if(isset($page->profile->event_title)) {
	$titleText = $page->profile->event_title;
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


<?

// Create class for grid layout
$CLASS_GRID = "";
if(isset($block->settings["AMSD Layout"]) && $block->settings["AMSD Layout"] == "grid-list") {
	$CLASS_GRID = " grid";
}

// AMSD Profile View
// --------------------------------------------------------------------- //
if($profile) { 
	// Use $ITEM instead of $profile to match the AMSD listing loop
	$ITEM = $profile; ?>

	<div class="profile-meta-info-wrapper">
		
		<? if(isset($ITEM->focused_img)) {
			$itemImage = json_decode($ITEM->focused_img);
		} ?>
		<? if($itemImage) { ?>
			<div class="profile-image-wrapper">
				<div class="amsd-image" style="background-position: <?= $itemImage->config->{'background-position'} ?>; background-image: url('/image/<?= $itemImage->id ?>/800');"></div>
			</div>
		<? } ?>

		<div class="profile-meta-text-wrapper">
			<div class="amsd-title-text"><?= $ITEM->title ?></div>
			<? if(isset($ITEM->sub_title)) { ?>
				<p class="amsd-meta-text-profile-page"><?= $ITEM->sub_title ?></p>
			<? } ?>
			<? if(isset($ITEM->publish_date)) { ?>
				<p class="amsd-meta-text-profile-page-small"><?= date("F j, Y", strtotime($ITEM->publish_date)); ?></p>
			<? } ?>

		</div>

	</div>

	<div class="profile-text-wrapper">
		<?= $ITEM->html ?>
	</div>

	<div class="back-links-wrapper">
		<a href="<?= $back ?>" class="back-link cms-btn"><span class="button-icon"><i class="fas fa-chevron-left"></i></span> Back to <?= $page->title ?></a>
	</div>

<? 
// AMSD Listing View
// --------------------------------------------------------------------- //
} else { 
?>
	<? if(sizeof($amsd["data"]) > 0) { ?>
	
		<div class="amsd-list <?= $CLASS_GRID ?>">

			<? $ARCHIVE = newsArchive(); ?>
			<div class="amsd-select-form-wrapper">
				<form class="amsd-select-form">
					<label for="news-select" class="amsd-select-form-label">Filter by Month</label>
					<select id="news-select" class="input-field select-input category-filter w-select">
						<option value="">
						<? if($ARCHIVE["selected"]) { ?><?= $ARCHIVE["selected"] ?><? } ?><? if(!$ARCHIVE["selected"]) { ?>Recent Posts<? } ?></option>
						<? if($ARCHIVE["selected"]) { ?><option value="/<?= $page->uri ?>/">Recent Posts</option><? } ?>
						<? foreach($ARCHIVE["dates"] as $date) { ?>
							<? $MONTH = "$date->text $date->year" ?>
							<? if($ARCHIVE["selected"] != $MONTH) { ?>
				    			<option value="/<?= $page->uri ?>/archive/<?= $date->year ?>/<?= $date->month ?>"><?= $MONTH ?></option>
							<? } ?>
						<? } ?>
					</select>
					<script>
						$(function(){
							// bind change event to select
							$('#news-select').on('change', function () {
								var url = $(this).val(); // get selected value
								if (url) { // require a URL
									window.location = url; // redirect
								}
								return false;
							});
						});
				    </script>
				</form>
			</div>
			
			<? foreach($amsd["data"] as $k => $ITEM) { ?>
			
				<? // Generate the profile URL
				$link = isset($block->settings["Slug"]) && strlen(strip_tags($ITEM->html)) > 2 ? amsdProfileSlug($page, $amsd, $ITEM) : '';
				?>
			
				<div class="amsd-item <?= $CLASS_GRID ?>">

					<? $itemImage = false;
					if(isset($ITEM->focused_img)) {
						$itemImage = json_decode($ITEM->focused_img);
					} ?>
				
					<? if($itemImage) { ?>
						<a <? if($link) { ?>href="<?= $link ?>"<? } ?> class="amsd-image-link w-inline-block <?= $CLASS_GRID ?>" title="<?= $ITEM->title ?>">
							<div class="amsd-image <?= $CLASS_GRID ?>" style="background-position: <?= $itemImage->config->{'background-position'} ?>; background-image: url('/image/<?= $itemImage->id ?>/800');">
								<? if($link) { ?><div class="hover-overlay"></div><? } ?>
							</div>
						</a>
					<? } ?>

					<div class="amsd-text-wrapper <?= $CLASS_GRID ?>">

						<a <? if($link) { ?>href="<?= $link ?>"<? } ?> class="amsd-title-text-link <?= $CLASS_GRID ?>"><?= $ITEM->title ?></a>

						<? if(isset($ITEM->publish_date)) { ?>
							<p class="amsd-meta-text <?= $CLASS_GRID ?>"><?= date("F j, Y", strtotime($ITEM->publish_date)); ?></p>
						<? } ?>

						<? $itemPreviewText = false;
						if($ITEM->preview_text) {
							$itemPreviewText = nl2br($ITEM->preview_text);
						} else if(strlen(strip_tags($ITEM->html)) > 2) {
							$itemPreviewText = character_limiter(strip_tags($ITEM->html), 300);
						}
						if(isset($itemPreviewText)) { ?>
							<p class="amsd-description-text <?= $CLASS_GRID ?>"><?= $itemPreviewText ?></p>
						<? } ?>

						<? if($link) { ?>
							<? if($ITEM->preview_text || strlen(strip_tags($ITEM->html)) > 2  || $link) { ?>
								<? if(!$CLASS_GRID) { ?>
									<a href="<?= $link ?>" class="amsd-button cms-btn <?= $CLASS_GRID ?>" title="<?= $ITEM->title ?>"><? if(isset($ITEM->button_text)) { ?><?= $ITEM->button_text ?><? } else { ?>Learn More<? } ?> <span class="button-arrow"><i class="fas fa-chevron-right"></i></span></a>
								<? } ?>
							<? } ?>
						<? } ?>

					</div>
				
				</div>
			
			<? } ?>
			
		</div>

	<? } ?>
<? } ?>


<?

// Create class for staff layout
$CLASS_STAFF = "";
if(isset($block->settings["AMSD Layout"]) && $block->settings["Table"] == "amsd_staff") {
	$CLASS_STAFF = " staff";
}

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
			<div class="profile-image-wrapper <?= $CLASS_STAFF ?>">
				<div class="amsd-image <?= $CLASS_STAFF ?>" style="background-position: <?= $itemImage->config->{'background-position'} ?>; background-image: url('/image/<?= $itemImage->id ?>/600');"></div>
			</div>
		<? } ?>

		<div class="profile-meta-text-wrapper">

			<div class="amsd-title-text"><?= $ITEM->title ?></div>

			<? if(isset($ITEM->sub_title)) { ?>
				<p class="amsd-meta-text-profile-page"><?= $ITEM->sub_title ?></p>
			<? } ?>

			<? if(isset($ITEM->email) || isset($ITEM->phone)) { ?>
				<div class="amsd-meta-links-outer-wrapper profile-page">
					<? if(isset($ITEM->email)) { ?>
						<div class="amsd-meta-link-wrapper profile-page">
							<div class="amsd-meta-icon profile page"><i class="fas fa-envelope"></i></div>
							<a href="mailto:<?= $ITEM->email ?>" class="amsd-meta-link profile-page"><?= $ITEM->email ?></a>
						</div>
					<? } ?>
					<? if(isset($ITEM->phone)) { ?>
						<div class="amsd-meta-link-wrapper profile-page">
							<div class="amsd-meta-icon profile page"><i class="fas fa-phone-alt"></i></div>
							<a href="tel:+1<?= $ITEM->phone ?>" class="amsd-meta-link profile-page"><?= $ITEM->phone ?></a>
						</div>
					<? } ?>
				</div>
			<? } ?>

		</div>

	</div>

	<div class="profile-text-wrapper">
		<?= $ITEM->html ?>
	</div>


	<div class="back-links-wrapper">
		<a href="/<?= getPage($page->id)->uri ?>" class="back-link"><span class="button-icon"><i class="fas fa-chevron-left"></i></span> Back to <?= $page->title ?></a>
	</div>

<? 
// AMSD Listing View
// --------------------------------------------------------------------- //
} else {
	// AMSD with Categories
	$categoriesBlock = getBlock($block->settings["Category Table Block"]);
	$amsdWithoutCategories = $amsd;
	$amsd = amsdWithCategories($amsd);

	// Add uncategorized items back to $amsd["data"]
	$UNCATEGORIZED = new StdClass();
	$UNCATEGORIZED->items = array();

	foreach ($amsdWithoutCategories["data"] as $k => $ITEM) {
		if(!$ITEM->category) {
			$UNCATEGORIZED->items[] = $ITEM;
		}
	}
	if(sizeof($UNCATEGORIZED->items) > 0) {
		// Putting uncategorized items as a 'category' item in the begining of the $amsd["data"] array
		array_unshift($amsd["data"], $UNCATEGORIZED);
	}
	
	?>

	<? if(sizeof($categoriesBlock["data"]) > 0) { ?>

		<div class="amsd-select-form-wrapper">
			<form class="amsd-select-form">
				<label for="category-select" class="amsd-select-form-label">Filter by Category</label>
				<div class="form-input select-input-wrapper">
					<select id="category-select" name="category-select" class="input-field select-input no-floating-label category-filter w-select">
						<option value="/<?= $page->uri ?>">All Categories</option>
				        <? foreach($categoriesBlock["data"] as $k => $CATEGORY) { ?>
				        	<option value="/<?= $page->uri ?>/category/<?= $CATEGORY->slug ?>" <? if(isset($subVal) && $subVal == $CATEGORY->id) { ?>selected<? } ?>><?= $CATEGORY->title ?></option>
				        <? } ?>
					</select>
				</div>
			</form>
		</div>

		<script>
			$("#category-select").on('change', function() {
		        window.location.href = $(this).val();
		    });
	    </script>

	<? } ?>


	<? if(sizeof($amsd["data"]) > 0) { ?>

		<div class="amsd-list <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>">	

			<? foreach($amsd["data"] as $k => $CATEGORY) { ?>

				<? if(sizeof($CATEGORY->items) > 0) { ?>

					<? if($CATEGORY->title) { ?>
						<div class="heading category-title"><? if($CATEGORY->icon && $CATEGORY->icon != 'null') { ?><span class="category-icon"><i class="<?= $CATEGORY->icon ?>"></i></span> <? } ?><?= $CATEGORY->title ?></div>
					<? } ?>

					<div class="amsd-category-wrapper <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>">

						<? foreach($CATEGORY->items as $k => $ITEM) { ?>

							<? // Generate the profile URL
							$link = isset($block->settings["Slug"]) && strlen(strip_tags($ITEM->html)) > 2 ? amsdProfileSlug($page, $amsd, $ITEM) : '';
							$link = isset($ITEM->url) ? $ITEM->url : $link; ?>
						
							<div class="amsd-item <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>">

								<? // Get image URL
								$itemImageUrl = false;
								$itemImagePosition = false;
								if(isset($ITEM->focused_img)) {
									$itemImage = json_decode($ITEM->focused_img);
									$itemImagePosition = $itemImage->config->{'background-position'};
									$itemImageUrl =  '/image/' . $itemImage->id . '/600';
								} else if($block->settings["Table"] == "amsd_staff") {
									$itemImageUrl = '/assets/images/staff-placeholder.jpg';
								} ?>
							
								<? if($itemImageUrl) { ?>
									<a <? if($link) { ?>href="<?= $link ?>"<? } ?> class="amsd-image-link w-inline-block <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>" title="<?= $ITEM->title ?>">
										<div class="amsd-image <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>" style="<? if($itemImagePosition) { ?>background-position: <?= $itemImagePosition ?>;<? } ?> background-image: url('<?= $itemImageUrl ?>');">
											<? if($link) { ?><div class="hover-overlay"></div><? } ?>
										</div>
									</a>
								<? } ?>

								<div class="amsd-text-wrapper <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>">

									<a <? if($link) { ?>href="<?= $link ?>"<? } ?> class="amsd-title-text-link <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>"><?= $ITEM->title ?></a>

									<? if(isset($ITEM->sub_title)) { ?>
										<p class="amsd-meta-text <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>"><?= $ITEM->sub_title ?></p>
									<? } ?>

									<? if(isset($ITEM->email) || isset($ITEM->phone)) { ?>
										<div class="amsd-meta-links-outer-wrapper">
											<? if(isset($ITEM->email)) { ?>
												<div class="amsd-meta-link-wrapper">
													<div class="amsd-meta-icon"><i class="fas fa-envelope"></i></div>
													<a href="mailto:<?= $ITEM->email ?>" class="amsd-meta-link"><?= $ITEM->email ?></a>
												</div>
											<? } ?>
											<? if(isset($ITEM->phone)) { ?>
												<div class="amsd-meta-link-wrapper">
													<div class="amsd-meta-icon"><i class="fas fa-phone-alt"></i></div>
													<a href="tel:+1<?= $ITEM->phone ?>" class="amsd-meta-link"><?= $ITEM->phone ?></a>
												</div>
											<? } ?>
										</div>
									<? } ?>

									<? $itemPreviewText = false;
									if($block->settings["Table"] != "amsd_staff") {
										if($ITEM->preview_text) {
											$itemPreviewText = nl2br($ITEM->preview_text);
										} else if(strlen(strip_tags($ITEM->html)) > 2) {
											$itemPreviewText = character_limiter(strip_tags($ITEM->html), 180);
										}
									}
									if($itemPreviewText) { ?>
										<p class="amsd-description-text <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>"><?= $itemPreviewText ?></p>
									<? } ?>

									<? if($link) { ?>

										<? $buttonText = '';
										if(isset($ITEM->button_text)) {
											$buttonText = $ITEM->button_text;
										} else if($block->settings["Table"] == "amsd_staff") {
											$buttonText = 'View Bio';
										} else {
											$buttonText = 'Learn More';
										} ?>

										<div class="amsd-button-wrapper">
											<a href="<?= $link ?>" class="amsd-button <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>" title="<?= $ITEM->title ?>"><?= $buttonText ?> <span class="amsd-button-arrow"><i class="fas fa-chevron-right"></i></span></a>
										</div>
										
									<? } ?>

								</div>
							
							</div>

						<? } ?>

						<? if($CLASS_GRID) { ?>
							<div class="amsd-item grid remainder-items-fix"></div>
							<div class="amsd-item grid remainder-items-fix"></div>
						<? } ?>

					</div>

				<? } ?>
			
			<? } ?>

		</div>
		
	<? } ?>
<? } ?>

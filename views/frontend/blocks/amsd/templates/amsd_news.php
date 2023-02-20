
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
				<div class="amsd-image <?= $CLASS_STAFF ?>" style="background-position: <?= $itemImage->config->{'background-position'} ?>; background-image: url('/image/<?= $itemImage->id ?>/400');"></div>
			</div>
		<? } ?>

		<div class="profile-meta-text-wrapper">

			<div class="amsd-title-text"><?= $ITEM->title ?></div>
			<div class="profile-title-underline"></div>

			<? if(isset($ITEM->sub_title)) { ?>
				<p class="amsd-meta-text-profile-page"><?= $ITEM->sub_title ?></p>
			<? } ?>

			<? if(isset($ITEM->email) || isset($ITEM->phone)) { ?>
				<p class="amsd-description-text grid">
					<? if(isset($ITEM->email)) { ?>
						<a href="mailto:<?= $ITEM->email ?>" class="amsd-meta-text-link"><span class="amsd-description-text-icon"><i class="fas fa-envelope"></i></span> <?= $ITEM->email ?></a>
					<? } ?>
					<? if(isset($ITEM->email) && isset($ITEM->phone)) { ?>
						<br>
					<? } ?>
					<? if(isset($ITEM->phone)) { ?>
						<a href="tel:+1<?= $ITEM->phone ?>" class="amsd-meta-text-link"><span class="amsd-description-text-icon"><i class="fas fa-phone-alt"></i></span> <?= $ITEM->phone ?></a>
					<? } ?>
				</p>
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

	?>

	<? $ARCHIVE = newsArchive(); ?>

	<div class="amsd-select-form-wrapper">
		<form class="amsd-select-form">
			<label for="news-select" class="amsd-select-form-label">Filter by Month</label>
			<div class="form-input select-input-wrapper">
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
			</div>
		</form>

		<script>
			$(function(){
				$('#news-select').on('change', function () {
					window.location.href = $(this).val();
				});
			});
	    </script>

		<? if(sizeof($categoriesBlock["data"]) > 0) { ?>

			<div class="amsd-select-form-divider"></div>

			<form class="amsd-select-form">
				<label for="category-select" class="amsd-select-form-label">Filter by Category</label>
				<div class="form-input select-input-wrapper">
					<select id="category-select" name="category-select" class="input-field select-input category-filter w-select">
						<option value="/<?= $page->uri ?>">All Categories</option>
				        <? foreach($categoriesBlock["data"] as $k => $CATEGORY) { ?>
				        	<option value="/<?= $page->uri ?>/category/<?= $CATEGORY->slug ?>" <? if(isset($subVal) && $subVal == $CATEGORY->id) { ?>selected<? } ?>><?= $CATEGORY->title ?></option>
				        <? } ?>
					</select>
				</div>
			</form>

			<script>
				$("#category-select").on('change', function() {
			        window.location.href = $(this).val();
			    });
		    </script>

		<? } ?>

	</div>



	<? if(sizeof($amsd["data"]) > 0) { ?>

		<div class="amsd-list <?= $CLASS_GRID ?> with-categories">	

			<? foreach($amsd["data"] as $k => $ITEM) { ?>

				<? // Generate the profile URL
				$link = isset($block->settings["Slug"]) && strlen(strip_tags($ITEM->html)) > 2 ? amsdProfileSlug($page, $amsd, $ITEM) : '';
				$link = isset($ITEM->url) ? $ITEM->url : $link; ?>
			
				<div class="amsd-item <?= $CLASS_GRID ?>">

					<? // Get image URL
					$itemImageUrl = false;
					$itemImagePosition = false;
					if(isset($ITEM->focused_img)) {
						$itemImage = json_decode($ITEM->focused_img);
						$itemImagePosition = $itemImage->config->{'background-position'};
						$itemImageUrl =  '/image/' . $itemImage->id . '/400';
					} else if($block->settings["Table"] == "amsd_staff") {
						$itemImageUrl = '/assets/images/staff-placeholder.jpg';
					} ?>
				
					<? if($itemImageUrl) { ?>
						<a <? if($link) { ?>href="<?= $link ?>"<? } ?> class="amsd-image-link w-inline-block <?= $CLASS_GRID ?>" title="<?= $ITEM->title ?>">
							<div class="amsd-image <?= $CLASS_GRID ?> <?= $CLASS_STAFF ?>" style="<? if($itemImagePosition) { ?>background-position: <?= $itemImagePosition ?>;<? } ?> background-image: url(<?= $itemImageUrl ?>);">
								<? if($link) { ?><div class="hover-overlay"></div><? } ?>
							</div>
						</a>
					<? } ?>

					<div class="amsd-text-wrapper <?= $CLASS_GRID ?>">

						<p class="amsd-meta-text news-meta-text"><?= date("F j, Y", strtotime($ITEM->publish_date)); ?></p>

						<a <? if($link) { ?>href="<?= $link ?>"<? } ?> class="amsd-title-text-link <?= $CLASS_GRID ?>"><?= $ITEM->title ?></a>

						<? if(isset($ITEM->category) || isset($ITEM->author)) { ?>
							<p class="amsd-description-text grid">
								<? if(isset($ITEM->category)) { ?>
									<div class="amsd-meta-text-link"><? if($CATEGORY->icon && $CATEGORY->icon != 'null') { ?><span class="amsd-description-text-icon"><i class="<?= $CATEGORY->icon ?>"></i></span> <? } ?><?= $CATEGORY->title ?></div>
								<? } ?>
								<? if(isset($ITEM->category) && isset($ITEM->author)) { ?>
									&nbsp;<span class="amsd-meta-text-divider">&nbsp;</span>&nbsp;
								<? } ?>
								<? if(isset($ITEM->author)) { ?>
									<div class="amsd-meta-text-link"><span class="amsd-description-text-icon"><i class="fas fa-user"></i></span> <?= $ITEM->author ?></div>
								<? } ?>
							</p>
						<? } ?>

						<? if(isset($ITEM->email) || isset($ITEM->phone)) { ?>
							<p class="amsd-description-text grid">
								<? if(isset($ITEM->email)) { ?>
									<a href="mailto:<?= $ITEM->email ?>" class="amsd-meta-text-link"><span class="amsd-description-text-icon"><i class="fas fa-envelope"></i></span> <?= $ITEM->email ?></a>
								<? } ?>
								<? if(isset($ITEM->email) && isset($ITEM->phone)) { ?>
									<br>
								<? } ?>
								<? if(isset($ITEM->phone)) { ?>
									<a href="tel:+1<?= $ITEM->phone ?>" class="amsd-meta-text-link"><span class="amsd-description-text-icon"><i class="fas fa-phone-alt"></i></span> <?= $ITEM->phone ?></a>
								<? } ?>
							</p>
						<? } ?>

						<? $itemPreviewText = false;
						if($ITEM->preview_text) {
							$itemPreviewText = nl2br($ITEM->preview_text);
						} else if(strlen(strip_tags($ITEM->html)) > 2) {
							$itemPreviewText = character_limiter(strip_tags($ITEM->html), 500);
						}
						if(isset($itemPreviewText)) { ?>
							<p class="amsd-description-text <?= $CLASS_GRID ?>"><?= $itemPreviewText ?></p>
						<? } ?>


						<? if($link) { ?>

							<? $buttonText = '';
							if(isset($ITEM->button_text)) {
								$buttonText = $ITEM->button_text;
							} else {
								$buttonText = 'Read More';
							} ?>

							<div class="amsd-button-wrapper">
								<a href="<?= $link ?>" class="amsd-button <?= $CLASS_GRID ?>" title="<?= $ITEM->title ?>"><?= $buttonText ?> <span class="amsd-button-arrow"><i class="fas fa-chevron-right"></i></span></a>
							</div>
							
						<? } ?>

					</div>
				
				</div>

			<? } ?>

		</div>
	
	<? } ?>

<? } ?>

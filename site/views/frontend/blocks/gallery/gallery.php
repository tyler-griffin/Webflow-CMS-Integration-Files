<div class="block" data-id="<?= $block->id ?>">

<?

	// Use a nice clean variable name
	$ALBUM = $profile;

	// Store reference to block settings
	$SETTINGS = $block->settings;
	
	// If this is a single album, load $ALBUM variable
	// with the single album and fix the mismatched array key name
	if($SETTINGS["Template"] == "album") {
	
		$ALBUM = getBlock($block->id);
		$ALBUM["result"] = $ALBUM["data"];
	
	}

	
/* ALBUM VIEW ------------------------------------------------- */	
	
if($ALBUM) {  ?>

	<div class="album-photos">

		<? echo _titleTag($ALBUM["result"][0]->title) ?>

		<? if(isset($ALBUM["result"][0]->text) && $ALBUM["result"][0]->text != "") { ?> 
		<div class="album-text-block">
			<? echo $ALBUM["result"][0]->text ?>
		</div>
		<? } ?>

		<? foreach($ALBUM["result"][0]->photos as $i => $PHOTO) { ?> 

			<a class="w-lightbox w-inline-block gallery-image" href="#" style="background-position: <?= json_decode($PHOTO->focused_img)->config->{'background-position'} ?>; background-image: url('/image/<?= json_decode($PHOTO->focused_img)->id ?>/1000');">

				<? if(isset($PHOTO->caption) && $PHOTO->caption != '') { ?>
					<div class="album-label"><?= $PHOTO->caption ?></div>
				<? } ?>

				<div class="gallery-image-spacer"><div class="hover-overlay"></div></div>

				<script type="application/json" class="w-json">
					{
						"group": "lightbox",
						"items": [
						{
						"type": "image",
						"thumbnailUrl": "/image/<?= json_decode($PHOTO->focused_img)->id ?>/200",
						"url": "/image/<?= json_decode($PHOTO->focused_img)->id ?>/1000"<? if(isset($PHOTO->caption) && $PHOTO->caption != '') { ?>,
						"caption": "<?= $PHOTO->caption ?>"<? } ?>
						}
						]
					}
				</script>

			</a>

		<? } ?>

		<div class="gallery-image fix-remainder-items"></div>
		<div class="gallery-image fix-remainder-items"></div>
		
		<? if($ALBUM["back"]) { ?>
			<div class="back-links-wrapper">
				<a href="<?= $ALBUM["back"] ?>" class="back-link"><span class="button-icon"><i class="fas fa-chevron-left"></i></span> Back to <?= $page->title ?></a>
			</div>
		<? } ?>

	</div>

<? 

/* GALLERY VIEW ------------------------------------------------- */	

} else {

	// Pull gallery data from block
	$GALLERY = getBlock($block->id); 
	
	// Show block heading if used
	$HEADING = (isset($SETTINGS["Heading Read Only"]) && $SETTINGS["Heading Read Only"] == "true") ? false : (trim($GALLERY["heading"]) == "" ? false : $GALLERY["heading"]);
	
	if($HEADING) { echo _titleTag($HEADING); } 
	
	?>

	 <div class="gallery-albums">

	 	<? foreach($GALLERY["data"] as $k => $ALBUM) { ?>

	 		<a class="w-inline-block gallery-image" href="<?= $page->slug ?>/album/<? echo $ALBUM->slug; ?>" style="background-position: <?= json_decode($ALBUM->photos[0]->focused_img)->config->{'background-position'} ?>; background-image: url('/image/<?= json_decode($ALBUM->photos[0]->focused_img)->id ?>/1000');">
				
	 			<div class="album-label">
	                <? echo $ALBUM->title ?>
	            </div>

				<div class="gallery-image-spacer">
					<div class="hover-overlay"></div>
				</div>

	        </a>
			
	    <? } ?>

		<div class="gallery-image fix-remainder-items"></div>
		<div class="gallery-image fix-remainder-items"></div>

	</div>


<? } ?>

</div>
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
	
	// Pull "Thumbnail Width" and "Thumbnail Height" from block settings.
	// Set defaults if they don't exist.  Defaults to 4:3 ratio.
	$thumbWidth = isset($SETTINGS["Thumbnail Width"]) ? $SETTINGS["Thumbnail Width"] : 400;
	$thumbHeight = isset($SETTINGS["Thumbnail Height"]) ? $SETTINGS["Thumbnail Height"] : ceil($thumbWidth * .75);
	
	// Insert required styles into the template	
	echo '<style type="text/css">
	
	.block[data-id="' . $block->id . '"] .gallery-image-wrapper {
		overflow: hidden;
		margin: auto;
		width: ' . $thumbWidth . 'px !important; 
		height: ' . $thumbHeight . 'px !important;
	}
	
	.block[data-id="' . $block->id . '"] .gallery-image-wrapper img {
		display: block;
		opacity: 0;
		max-width: 999999999999999999px !important;
		max-height: 999999999999999999px !important;
		-webkit-transition: opacity 500ms ease-out 1s;
		-moz-transition: opacity 500ms ease-out 1s;
		-o-transition: opacity 500ms ease-out 1s;
		transition: opacity 500ms ease-out 1s;
	}
	
	.block[data-id="' . $block->id . '"] .gallery-image-wrapper img.loaded {
		opacity: 1;
	}
	
	</style>';
	
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

			<a class="w-lightbox w-inline-block gallery-image" href="#" style="background-image: url('/image/<?= $PHOTO->img ?>/1000');">

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
						"thumbnailUrl": "/image/<?= $PHOTO->img; ?>/200",
						"url": "/image/<?= $PHOTO->img; ?>/1000"<? if(isset($PHOTO->caption) && $PHOTO->caption != '') { ?>,
						"caption": "<?= $PHOTO->caption ?>"<? } ?>
						}
						]
					}
				</script>

			</a>

		<? } ?>

		<div class="back-link-block">
			<a href="<?= $ALBUM["back"] ?>" class="back-link cms-btn"><span class="button-icon"><i class="fas fa-chevron-left"></i></span> &nbsp; Back to <?= $page->title ?></a>
		</div>

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

	 		<a class="w-inline-block gallery-image" href="<?= $page->slug ?>/album/<? echo $ALBUM->slug; ?>" style="background-image: url('/image/<?= $ALBUM->photos[0]->img ?>/1000');">
				
	 			<div class="album-label">
	                <? echo $ALBUM->title ?>
	            </div>

				<div class="gallery-image-spacer">
					<div class="hover-overlay"></div>
				</div>

	        </a>
			
	    <? } ?>

	</div>


<? } ?>

</div>
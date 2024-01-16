<div class="block" data-id="<?= $block->id ?>">

<?

	// Use a nice clean variable name
	$ALBUM = $profile;
	$PHOTOS = $ALBUM->{$group};

	// Store reference to block settings
	$SETTINGS = $block->settings;

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

?>

	<div class="album-photos">

		<? foreach($PHOTOS as $i => $PHOTO) { ?> 

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
						"url": "/image/<? echo $PHOTO->img; ?>/1000"<? if(isset($PHOTO->caption) && $PHOTO->caption != '') { ?>,
						"caption": "<?= $PHOTO->caption ?>"<? } ?>
						}
						]
					}
				</script>

			</a>

		<? } ?>

		<div class="gallery-image fix-remainder-items"></div>
		<div class="gallery-image fix-remainder-items"></div>

	</div>

</div>
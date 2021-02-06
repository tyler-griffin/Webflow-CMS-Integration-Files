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

	</div>

</div>


<script type="text/javascript">

	$(document).ready(function() {
	
		// Dynamically load and position the images to be proportional
	
		var BLOCK = $(".block[data-id='<?= $block->id ?>']"),
		  WRAPPERS = $(".gallery-image-wrapper", BLOCK);
		
		WRAPPERS.each(function(i,v) {
		
			var WRAPPER = $(this);
			
			var IMAGE = $("<img />");
				IMAGE.attr("src", "/image/" + WRAPPER.data("img") + "/1000");
				
			WRAPPER.append(IMAGE);
				
			IMAGE.load(function() {
	
				// Make sure we have relative positioning
				IMAGE.css("position", "relative");

				// Check actual width of new image
				var width = IMAGE.width(),
				   height = IMAGE.height();

				// Check actual width of frame
				var photoW = WRAPPER.width(),
					photoH = WRAPPER.height();
				  
				// Resize and position image inside frame
				// to keep all thumbnails appearing to be the same size.
				  
				// If image is landscape
				if(width > height) {

					// Match height to frame
					IMAGE.height(photoH);
					
					// If resized image is wider than frame
					if(IMAGE.width() < photoW) {
					
						// Match width to frame
						IMAGE.width(photoW); 
						
						// Match height to original ratio
						IMAGE.height(Math.ceil((height / width) * photoW));
						
					}
					
					// Position image in center of frame, "cropping" the excess
					IMAGE.css("left", "-" + Math.ceil((IMAGE.width() - photoW) / 2) + "px");

				// If image is portrait
				} else {

					// Match width to frame
					IMAGE.width(photoW);
					
					// If resized image is taller than frame
					if(IMAGE.height() < photoH) {
					
						// Match height to frame
						IMAGE.height(photoH);
						
						// Match width to original ratio
						IMAGE.width(Math.ceil((width / height) * photoH));
						
					}
					
					// Position image in center of frame, "cropping" the excess
					IMAGE.css("top", "-" + Math.ceil((IMAGE.height() - photoH) / 2) + "px");

				}	
				
				IMAGE.addClass("loaded");
			
			}).each(function() {
	
				if(this.complete) $(this).load();
				
			});

		});
	
	});

</script>
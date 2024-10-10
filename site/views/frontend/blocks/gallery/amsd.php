<div class="block" data-id="<?= $block->id ?>">

<?

	// Use a nice clean variable name
	$ALBUM = $profile;
	$PHOTOS = $ALBUM->{$group};

	// Store reference to block settings
	$SETTINGS = $block->settings;

?>

	<div class="album-photos">

		<? foreach($PHOTOS as $i => $PHOTO) { ?> 

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
						"url": "/image/<? echo json_decode($PHOTO->focused_img)->id ?>/1000"<? if(isset($PHOTO->caption) && $PHOTO->caption != '') { ?>,
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
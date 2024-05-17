<div class="map-wrapper" data-id="<?= $block->id ?>">

	<div class="map">

		<?
		
			$MAP_BLOCK = getBlock($block->id);
			$address = $MAP_BLOCK["data"][0]->markers[0];
			
			if((isset($MAP_BLOCK["settings"]["Heading Read Only"]) && $MAP_BLOCK["settings"]["Heading Read Only"] == "true")) {} else { echo _titleTag($MAP_BLOCK["heading"]); }

			$DIRECTIONS_URL = "https://www.google.com/maps?daddr=" . urlencode($address->address_1 . " " . $address->city . " " . $address->state . " " . $address->zip);
			
		?>

		<? displayMap($block->id); ?>

		<? clr(); ?>
		
	</div>

	<div class="directions-button-wrapper">
		<a href="<?= $DIRECTIONS_URL ?>" class="cms-btn w-inline-block"><div class="button-icon"><i class="fa-solid fa-diamond-turn-right"></i></div><div class="button-text">Directions</div></a>
	</div>

</div>
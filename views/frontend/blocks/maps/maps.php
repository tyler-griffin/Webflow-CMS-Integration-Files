<div data-id="<?= $block->id ?>" class="map">

	<?
	
		$MAP_BLOCK = getBlock($block->id);
		$address = $MAP_BLOCK["data"][0]->markers[0];
		
		if((isset($MAP_BLOCK["settings"]["Heading Read Only"]) && $MAP_BLOCK["settings"]["Heading Read Only"] == "true")) {} else { echo _titleTag($MAP_BLOCK["heading"]); }

		$DIRECTIONS_URL = "https://www.google.com/maps?daddr=" . urlencode($address->address_1 . " " . $address->city . " " . $address->state . " " . $address->zip);
		
	?>

      <? displayMap($block->id); ?>
	  
	  <a class="button more-button" href="<?= $DIRECTIONS_URL ?>" target="_blank">Get Directions</a>

	<? clr(); ?>
	
</div>
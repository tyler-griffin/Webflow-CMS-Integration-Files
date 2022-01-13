
<? if(sizeof($amsd["data"]) > 0) { ?>
	
	<? $count = 0; foreach ($amsd["data"] as $k => $ITEM) {
		if($ITEM->active) { ?>
			<? $count++; ?>
			<? if($count > 1) { break; } ?>
			<div class="alert-bar">
				<div class="alert-text">
					<span class="alert-icon"><i class="<? if($ITEM->icon != 'null') { ?><?= $ITEM->icon ?><? } else { ?>fas fa-exclamation-triangle<? } ?>"></i></span>
					<?= $ITEM->textarea ?>
					<? if($ITEM->url) { ?>&nbsp;&nbsp; <a href="#" class="alert-learn-more-link">Learn More <span class="learn-more-arrow"><i class="fas fa-chevron-right"></i></span></a><? } ?>
				</div>
				<a href="#" class="alert-bar-close-button w-inline-block" data-ix="alert-bar-close">
					<div class="alert-bar-close-button-horizontal-line"></div>
					<div class="alert-bar-close-button-vertical-line"></div>
				</a>
				<div class="alert-bar-angle"></div>
			</div>
		<? } ?>
	<? } ?>

<? } ?>

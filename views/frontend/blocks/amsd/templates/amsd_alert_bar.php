
<? if(sizeof($amsd["data"]) > 0) { ?>
	
	<? $count = 0;
	foreach ($amsd["data"] as $k => $ITEM) {
		if($ITEM->active) {
			$count++;
			if($count > 1) { break; }
			$alertID = $ITEM->id;
			?>
			<div class="alert-bar">
				<div class="alert-text">
					<span class="alert-icon"><i class="<? if($ITEM->icon != 'null') { ?><?= $ITEM->icon ?><? } else { ?>fas fa-exclamation-triangle<? } ?>"></i></span>
					<?= $ITEM->textarea ?>
					<? if($ITEM->url) { ?>&nbsp;&nbsp; <a href="#" class="alert-learn-more-link">Learn More <span class="learn-more-arrow"><i class="fas fa-chevron-right"></i></span></a><? } ?>
				</div>
				<a href="#" class="alert-bar-close-button w-inline-block" id="alert-bar-close">
					<div class="alert-bar-close-button-horizontal-line"></div>
					<div class="alert-bar-close-button-vertical-line"></div>
				</a>
			</div>
		<? } ?>
	<? } ?>

	<script type="text/javascript">
		$(document).ready(function(){
			if(localStorage.getItem('alert<?= $alertID ?>') != 'shown') {
				$('.alert-bar').show();
			}
			$('#alert-bar-close').click(function() {
				$('.alert-bar').hide();
				localStorage.setItem('alert<?= $alertID ?>', 'shown');
			});
		});
	</script>

<? } ?>

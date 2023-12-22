
<? if(sizeof($amsd["data"]) > 0) { ?>
	
	<? $count = 0;
	foreach ($amsd["data"] as $k => $ITEM) {
		if($ITEM->active) {
			$count++;
			if($count > 1) { break; }
			$popupID = slug($ITEM->html);
			?>

			<div id="lightbox-window" class="lightbox-window">
				<div class="lightbox-scroll-wrapper">
					<div class="lightbox-content-box">
						<div data-ix="lightbox-close-button" class="close-button">
							<div class="lightbox-close-icon"><i class="fa-solid fa-xmark"></i></div>
						</div>
						<?= $ITEM->html ?>
					</div>
					<div data-ix="lightbox-close-button" class="fullscreen-close-button"></div>
				</div>
			</div>

		<? } ?>
	<? } ?>

	<script type="text/javascript">

		$(document).ready(function(){

			if(localStorage.getItem('<?= $popupID ?>') != 'shown') {
				$('#lightbox-window').addClass('visible');
			}
			$('#alert-bar-close').click(function() {
				$('#lightbox-window').removeClass('visible');
				localStorage.setItem('<?= $popupID ?>', 'shown');
			});

		});

	</script>

<? } ?>

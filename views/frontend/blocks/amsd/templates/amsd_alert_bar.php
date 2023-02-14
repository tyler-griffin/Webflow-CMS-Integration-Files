
<? if(sizeof($amsd["data"]) > 0) { ?>
	
	<? $count = 0;
	foreach ($amsd["data"] as $k => $ITEM) {
		if($ITEM->active) {
			$count++;
			if($count > 1) { break; }
			$alertID = slug($ITEM->textarea);
			?>
			<div id="alert-bar" class="alert-bar">
				<div class="alert-text">
					<?= nl2br($ITEM->textarea); ?>
					<? if(json_decode($ITEM->button)->text) { ?>
						&nbsp;
						<a class="alert-learn-more-link" href="<?= json_decode($ITEM->button)->url ?>"><?= json_decode($ITEM->button)->text ?> <span class="learn-more-arrow"><i class="fas fa-chevron-right"></i></span></a>
					<? } ?>
				</div>
				<a href="#" class="alert-bar-close-button w-inline-block" id="alert-bar-close">
					<div class="alert-bar-close-button-horizontal-line"></div>
					<div class="alert-bar-close-button-vertical-line"></div>
				</a>
			</div>
			<div id="alert-bar-spacer"></div>
		<? } ?>
	<? } ?>

	<script type="text/javascript">

		function alertBarSpacer() {
			if($('#alert-bar').is(':visible')) {
				$('#alert-bar-spacer').css('height', $('#alert-bar').outerHeight() + 'px');
			}
		}

		$(window).on('resize', function(){
			alertBarSpacer();
		});

		$(document).ready(function(){

			if(localStorage.getItem('<?= $alertID ?>') != 'shown') {
				$('#alert-bar, #alert-bar-spacer').show();
				$('#alert-bar, #alert-bar-spacer').show();
			}
			$('#alert-bar-close').click(function() {
				$('#alert-bar, #alert-bar-spacer').hide();
				localStorage.setItem('<?= $alertID ?>', 'shown');
			});

			alertBarSpacer();

		});

	</script>

<? } ?>


<? if(sizeof($amsd["data"]) > 0) { ?>
	
	<? $count = 0;
	foreach ($amsd["data"] as $k => $ITEM) {
		if($ITEM->active) {
			$count++;
			if($count > 1) { break; }
			?>
			<div id="alert-bar" class="alert-bar">
				<div id="alert-text" class="alert-text">
					<? if($ITEM->icon && $ITEM->icon != "null") { ?>
						<span class="alert-icon"><i class="<?= $ITEM->icon ?>"></i></span>
					<? } ?>
					<?= nl2br($ITEM->textarea); ?>
					<? if(json_decode($ITEM->button)->url != '') { ?>
						&nbsp;
						<a class="alert-learn-more-link" href="<?= json_decode($ITEM->button)->url ?>">
							<div class="alert-learn-more-link-text"><?= json_decode($ITEM->button)->text ?></div>
							<div class="alert-learn-more-arrow"><i class="fas fa-chevron-right"></i></div>
						</a>
					<? } ?>
				</div>
				<a href="#" class="alert-bar-close-button w-inline-block" id="alert-bar-close">
					<div class="alert-close-icon"><i class="fa-solid fa-xmark"></i></div>
				</a>
			</div>
			<div id="alert-bar-spacer"></div>
		<? } ?>
	<? } ?>

	<script type="text/javascript">

		window.addEventListener('load', function() {

			$(document).ready(function(){

				String.prototype.hashCode = function() {
					var hash = 0,
						i, chr;
					if (this.length === 0) return hash;
					for (i = 0; i < this.length; i++) {
						chr = this.charCodeAt(i);
						hash = ((hash << 5) - hash) + chr;
						hash |= 0; // Convert to 32bit integer
					}
					return hash;
				}

				var alertID = $('#alert-text').text().hashCode();

				if(localStorage.getItem(alertID) != 'shown') {
					$('#alert-bar, #alert-bar-spacer').show();
					
				}
				$('#alert-bar-close').click(function() {
					$('#alert-bar, #alert-bar-spacer').hide();
					localStorage.setItem(alertID, 'shown');
				});

			});

		})

	</script>

<? } ?>

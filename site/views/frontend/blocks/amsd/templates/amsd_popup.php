
<? if(sizeof($amsd["data"]) > 0) { ?>
	
	<? $count = 0;
	foreach ($amsd["data"] as $k => $ITEM) {
		if($ITEM->active) {
			$count++;
			if($count > 1) { break; }
			?>

			<div id="lightbox-window" class="lightbox-window">
				<div class="lightbox-scroll-wrapper">
					<div class="lightbox-content-box">
						<div data-ix="lightbox-close-button" class="close-button">
							<div class="lightbox-close-icon"><i class="fa-solid fa-xmark"></i></div>
						</div>
						<div id="modal-text">
							<?= $ITEM->html ?>
						</div>
					</div>
					<div data-ix="lightbox-close-button" class="fullscreen-close-button"></div>
				</div>
			</div>

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

				var modalID = $('#modal-text').text().hashCode();

				if(localStorage.getItem(modalID) != 'shown') {
					$('#lightbox-window').addClass('visible');
				}
				$('#alert-bar-close').click(function() {
					$('#lightbox-window').removeClass('visible');
					localStorage.setItem(modalID, 'shown');
				});

			});

		})

	</script>

<? } ?>

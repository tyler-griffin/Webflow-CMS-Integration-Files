
<? if(sizeof($amsd["data"]) > 0) { ?>
	
	<div class="amsd-list amsd-dropdown-list">

		<? foreach($amsd["data"] as $k => $ITEM) { ?>

			<div class="amsd-item dropdown-item">
				<div data-delay="0" data-hover="false" class="amsd-dropdown w-dropdown">
					<div data-ix="amsd-dropdown-arrow-rotate" class="amsd-dropdown-toggle w-dropdown-toggle">
						<div><?= $ITEM->title ?></div>
						<div class="amsd-dropdown-arrow-icon w-icon-dropdown-toggle"></div>
					</div>
					<nav class="amsd-dropdown-content w-dropdown-list">
						<p class="amsd-description-text"><?= $ITEM->html ?></p>
					</nav>
				</div>
			</div>

		<? } ?>

	</div>
	
	<? } ?>
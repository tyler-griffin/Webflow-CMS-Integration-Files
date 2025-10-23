
<? if(sizeof($amsd["data"]) > 0) { ?>

	<div class="text-block">

		<? foreach($amsd["data"] as $k => $ITEM) { ?>

			<details class="cms-accordion" data-accordion-position="right" data-accordion-size="standard" data-accordion-style="caret">
				<summary class="cms-accordion-summary"><?= $ITEM->title ?></summary>
				<div class="cms-accordion-content"><?= $ITEM->html ?></div>
			</details>

		<? } ?>

	</div>
	
<? } ?>
<div <? if(mb_strlen(strip_tags($block->html))>2) { ?>class="text-block"<? } ?> data-id="<?= $block->id ?>">

	<?= $block->html ?>

	<? clr(); ?>
	
</div>
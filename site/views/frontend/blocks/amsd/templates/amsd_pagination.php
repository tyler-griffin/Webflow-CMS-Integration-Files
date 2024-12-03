
<? // Add block setting "AMSD Pagination Per Page" block setting with the value set to an integer that is greater than zero. ?>

<? if($amsd["pagination"]["items"]) {
	$amsdItems = $amsd["pagination"]["items"];
} else {
	$amsdItems = $amsd["data"];
} ?>

<? if(sizeof($amsdItems) > 0) { ?>
	
	<div class="amsd-list">	
		
		<? foreach($amsdItems as $k => $ITEM) { ?>

			
		
		<? } ?>
		
	</div>

	<? if($amsd["pagination"]["total_pages"] > 1) { ?>

		<? if($amsd["pagination"]["current_page"] == 1) {
			$paginationPrevPage = $amsd["pagination"]["current_page"];
		} else {
			$paginationPrevPage = $amsd["pagination"]["current_page"] - 1;
		}
		if($amsd["pagination"]["current_page"] == $amsd["pagination"]["total_pages"]) {
			$paginationNextPage = $amsd["pagination"]["current_page"];
		} else {
			$paginationNextPage = $amsd["pagination"]["current_page"] + 1;
		} ?>

		<div class="amsd-pagination">

			<a title="First Page" href="<?= $amsd["pagination"]["href_base"] . '/page/1'; ?>" class="pagination-first-last-link">First</a>
			<a title="Previous Page" href="<?= $amsd["pagination"]["href_base"] . '/page/' . $paginationPrevPage; ?>" class="pagination-next-prev-link w-inline-block"><i class="fas fa-chevron-left"></i></a>

			<? for($page_num = 1; $page_num <= $amsd["pagination"]["total_pages"]; $page_num++) { ?>

				<? // Only Show 5 pages closest to current page
				if($page_num > $amsd["pagination"]["current_page"] + 2 && $page_num > 5) { continue; }
				if($page_num < $amsd["pagination"]["current_page"] - 2 && $page_num + 5 <= $amsd["pagination"]["total_pages"]) { continue; } ?>

				<a class="pagination-link <?= ($page_num == $amsd["pagination"]["current_page"] ? "selected" : "") ?>" href="<?= $amsd["pagination"]["href_base"] . '/page/' . $page_num; ?>"><?= $page_num ?></a>

			<? } ?>
			
			<a title="Next Page" href="<?= $amsd["pagination"]["href_base"] . '/page/' . $paginationNextPage; ?>" class="pagination-next-prev-link w-inline-block"><i class="fas fa-chevron-right"></i></a>
			<a title="Last Page" href="<?= $amsd["pagination"]["href_base"] . '/page/' . $amsd["pagination"]["total_pages"]; ?>" class="pagination-first-last-link">Last</a>

		</div>

	<? } ?>

<? } ?>

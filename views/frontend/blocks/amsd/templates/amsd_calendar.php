
<?

// AMSD Profile View
// --------------------------------------------------------------------- //
if($profile) { 
	// Use $ITEM instead of $profile to match the AMSD listing loop
	$ITEM = $profile;
	$FULL_ADDRESS = ($ITEM->address_1 ? $ITEM->address_1 : '') . ($ITEM->address_2 ? ', ' . $ITEM->address_2 : '') . ($ITEM->city ? ', ' . $ITEM->city : '') . ($ITEM->state ? ', ' . $ITEM->state : '') . ($ITEM->zip ? ' ' . $ITEM->zip : '');
	$DIRECTIONS_URL = "https://www.google.com/maps?daddr=" . urlencode($ITEM->address_1 . " " . $ITEM->city . " " . $ITEM->state . " " . $ITEM->zip);
	if($ITEM->start_time == $ITEM->end_time && $ITEM->start_date == $ITEM->end_date) {
		$ITEM->end_time = '';
	} ?>

	<div class="profile-meta-info-wrapper">
		
		<? if(isset($ITEM->focused_img)) {
			$itemImage = json_decode($ITEM->focused_img);
		} ?>
		<? if($itemImage) { ?>
			<div class="profile-image-wrapper <?= $CLASS_STAFF ?>">
				<div class="amsd-image <?= $CLASS_STAFF ?>" style="background-position: <?= $itemImage->config->{'background-position'} ?>; background-image: url('/image/<?= $itemImage->id ?>/400');"></div>
			</div>
		<? } ?>

		<div class="profile-meta-text-wrapper">

			<div class="amsd-title-text"><?= $ITEM->event_title ?></div>
			<div class="profile-title-underline"></div>

			
			<p class="amsd-meta-text-profile-page"><?= date("M j, Y", strtotime($ITEM->start_date)); ?><? if($ITEM->start_time) { ?>  <?= date("g:ia", strtotime($ITEM->start_time)); ?><? } ?><? if($ITEM->end_date && $ITEM->start_date != $ITEM->end_date) { ?> – <?= date("M j, Y", strtotime($ITEM->end_date)); ?><? } else { if($ITEM->end_time) { ?> –<? } ?><? } ?><? if($ITEM->end_time) { ?>  <?= date("g:ia", strtotime($ITEM->end_time)); ?><? } ?></p>

		</div>

	</div>

	<div class="profile-text-wrapper">
		<?= $ITEM->html ?>
	</div>


	<div class="back-links-wrapper">
		<a href="/<?= getPage($page->id)->uri ?>" class="back-link"><span class="button-icon"><i class="fas fa-chevron-left"></i></span> Back to <?= $page->title ?></a>
	</div>
<? 
// AMSD Listing View
// --------------------------------------------------------------------- //
} else {

	$calendarView = true;
	$listView = true;
	$showAllEvents = true;
	$listDefault = true;
	if($_GET['view'] == 'calendar') {
		$listDefault = false;
	}
	if($upcomingEventsToShow) {
		if(!in_array('calendar-view', $upcomingEventsToShow)) {
			$calendarView = false;
			if(in_array('list-view', $upcomingEventsToShow)) {
				$listDefault = true;
			}
		}
		if(!in_array('list-view', $upcomingEventsToShow)) {
			$listView = false;
		}
		if(!in_array('all', $upcomingEventsToShow)) {
			$showAllEvents = false;
		}
		if(in_array('both-views-calendar-default', $upcomingEventsToShow)) {
			$calendarView = true;
			$listView = true;
			$listDefault = false;
		}
		if(in_array('both-views-list-default', $upcomingEventsToShow)) {
			$calendarView = true;
			$listView = true;
			$listDefault = true;
		}
	}

	$categories = getBlock($block->settings["Category Table Block"]);
	$calendar = amsdCalendar($page, $amsd, [
		"headings_format" => "D",
		"month_select_format" => "F Y",
		"month_select_years_to_show" => 3

	]); ?>

	<div class="clr"></div>

	<div class="tabs-amsd-block">

		<div class="tabs-amsd-category-select-form w-form">
			<form class="form">
				<label for="month-select" class="tabs-amsd-category-select-form-label">Filter Events</label>
				<select id="month-select" class="input-field select-input tabs-amsd-category-filter w-select">
					<? $currentMonth = new DateTime(date("M").' '.date("Y")); ?>
					<? foreach ($calendar['month-select']['years'] as $y => $YEAR) { ?>
						<? foreach ($calendar['month-select']['months'] as $m => $MONTH) { ?>
							<? $monthCompare = new DateTime($MONTH.' '.$YEAR); ?>
							<? if($monthCompare >= $currentMonth) { ?>
								<option <? if($YEAR.$m == $calendar['state']['current']['year'].$calendar['state']['current']['month']) { ?>selected<? } ?> value="<?= base_url() ?><?= $page->uri ?>/archive/<?= $YEAR ?>/<?= $m ?>"><?= date("F", strtotime($MONTH)); ?> <?= $YEAR ?></option>
							<? } ?>
						<?	} ?>
					<?	} ?>
				</select>
			</form>
			<script>
		    $("#month-select").on('change', function() {
		        window.location.href = $(this).val();
		    });
		    </script>
		</div>

		<div data-duration-in="300" data-duration-out="100" class="tabs-amsd-tabs-wrapper w-tabs">
			
				<div class="tabs-amsd-tab-menu w-tab-menu <? if(!$calendarView || !$listView) { ?>empty<? } ?>">
					<? if($calendarView && $listView) { ?>
						<a data-w-tab="Tab 1" class="tabs-amsd-tab-link w-inline-block w-tab-link <? if(!$listDefault) { ?>w--current<? } ?>">
							<div><i class="fas fa-calendar-alt"></i></div>
						</a>
						<a data-w-tab="Tab 2" class="tabs-amsd-tab-link w-inline-block w-tab-link <? if($listDefault) { ?>w--current<? } ?>">
							<div><i class="fas fa-list"></i></div>
						</a>
					<? } ?>
				</div>
			
			<div class="tabs-amsd-content w-tab-content">

				<? if($calendarView) { ?>
					<div data-w-tab="Tab 1" class="calendar-tab-pane w-tab-pane <? if(!$listDefault) { ?>w--tab-active<? } ?>">

						<? $currentMonth = new DateTime(date("M").' '.date("Y")); ?>

						<div class="calendar-title-wrapper">

							<a href="<?= base_url() ?><?= $page->uri ?>/archive/<?= $calendar['state']['prev']['year'] ?>/<?= $calendar['state']['prev']['month'] ?>?view=calendar" class="calendar-title-arrow"><i class="fas fa-chevron-left"></i></a>

							<div class="heading calendar-title"><?= date("F", strtotime($calendar['state']['current']['year'].'-'.$calendar['state']['current']['month'])); ?> <?= $calendar['state']['current']['year'] ?></div>

							<a href="<?= base_url() ?><?= $page->uri ?>/archive/<?= $calendar['state']['next']['year'] ?>/<?= $calendar['state']['next']['month'] ?>?view=calendar" class="calendar-title-arrow"><i class="fas fa-chevron-right"></i></a>

						</div>

						<div class="calendar">

							<div class="calendar-left-cover"></div>
							<div class="calendar-row labels">
								<? foreach ($calendar['headings'] as $k => $ITEM) { ?>
									<div class="day label">
										<p class="calendar-day-label"><?= $ITEM ?></p>
									</div>
								<? } ?>
							</div>
							<? foreach ($calendar['weeks'] as $w => $WEEK) { ?>
								<div class="calendar-row">
									<? foreach ($WEEK as $d => $DAY) { ?>
										<div class="day">
											<? if($DAY['day'] && $DAY['day'] != 'blank') { ?>
												<p class="calendar-number-label <? if($DAY['current']) { ?>today<? } ?>"><?= $DAY['day'] ?></p>
											<? } ?>
											<? if($DAY['events'][0]) { ?>
												<? foreach($DAY['events'] as $k => $ITEM) { ?>
													<? if(!$showAllEvents) {
														$showEvent = false;
														foreach(explode(',', $ITEM->event_category) as $x => $EVENT_CATEGORY) {
															if(in_array($EVENT_CATEGORY, $upcomingEventsToShow)) {
																$showEvent = true;
															}
														}
														if(!$showEvent) { continue; }
													} ?>
													<? $link = amsdProfileSlug(getPage(CALENDAR_PAGE_ID), $amsd, $ITEM); ?>
													<? $FULL_ADDRESS = ($ITEM->address_1 ? $ITEM->address_1 : '') . ($ITEM->address_2 ? ', ' . $ITEM->address_2 : '') . ($ITEM->city ? ', ' . $ITEM->city : '') . ($ITEM->state ? ', ' . $ITEM->state : '') . ($ITEM->zip ? ' ' . $ITEM->zip : '');
													if($ITEM->start_time == $ITEM->end_time && $ITEM->start_date == $ITEM->end_date) {
														$ITEM->end_time = '';
													}?>
													<div class="calendar-view-event-details-wrapper" data-event-shown="true">
														<a href="<?= $link ?>" class="event-link"><?= $ITEM->event_title ?></a>
													</div>
												<? } ?>
											<? } ?>
										</div>
									<? } ?>
								</div>
							<? } ?>
						</div>
					</div>
				<? } ?>

				<div data-w-tab="Tab 2" class="calendar-tab-pane list-view w-tab-pane <? if($listDefault) { ?>w--tab-active<? } ?>">

					<div class="no-events-to-show"></div>

					<div class="amsd-list">
						<? $alreadyShownEvents = array(); ?>
						<? foreach ($calendar['weeks'] as $w => $WEEK) { ?>
							<? foreach ($WEEK as $d => $DAY) { ?>
								<? if($DAY['events'][0]) { ?>
									<? foreach($DAY['events'] as $k => $ITEM) { ?>
										<? if(!$showAllEvents) {
											$showEvent = false;
											foreach(explode(',', $ITEM->event_category) as $x => $EVENT_CATEGORY) {
												if(in_array($EVENT_CATEGORY, $upcomingEventsToShow)) {
													$showEvent = true;
												}
											}
											if(!$showEvent) { continue; }
										} ?>
										<? if(in_array($ITEM->id, $alreadyShownEvents)) { continue; } ?>
										<? $alreadyShownEvents[] = $ITEM->id; ?>
										<? $link = amsdProfileSlug(getPage(CALENDAR_PAGE_ID), $amsd, $ITEM); ?>
										<? $FULL_ADDRESS = ($ITEM->address_1 ? $ITEM->address_1 : '') . ($ITEM->address_2 ? ', ' . $ITEM->address_2 : '') . ($ITEM->city ? ', ' . $ITEM->city : '') . ($ITEM->state ? ', ' . $ITEM->state : '') . ($ITEM->zip ? ' ' . $ITEM->zip : '');
										if($ITEM->start_time == $ITEM->end_time && $ITEM->start_date == $ITEM->end_date) {
											$ITEM->end_time = '';
										} ?>
										<div class="amsd-item" data-event-shown="true">
											<? $itemImage = false;
											if(isset($ITEM->focused_img)) {
												$itemImage = json_decode($ITEM->focused_img);
											}
											if($itemImage) { ?>
												<a href="<?= $link ?>" class="amsd-image-link w-inline-block">
													<div class="amsd-image" style="background-position: <?= $itemImage->config->{'background-position'} ?>; background-image: url('/image/<?= $itemImage->id ?>/400');">
														<div class="image-hover-overlay"></div>
													</div>
												</a>
											<? } ?>
											<div class="amsd-text-wrapper">
												<a href="<?= $link ?>" class="amsd-title-text-link small"><?= $ITEM->event_title ?></a>
												<p class="amsd-meta-text"><?= date("M j, Y", strtotime($ITEM->start_date)); ?><? if($ITEM->start_time) { ?>  <?= date("g:ia", strtotime($ITEM->start_time)); ?><? } ?><? if($ITEM->end_date && $ITEM->start_date != $ITEM->end_date) { ?> – <?= date("M j, Y", strtotime($ITEM->end_date)); ?><? } else { if($ITEM->end_time) { ?> –<? } ?><? } ?><? if($ITEM->end_time) { ?>  <?= date("g:ia", strtotime($ITEM->end_time)); ?><? } ?><br></p>
												<p class="amsd-description-text"><?= character_limiter(strip_tags($ITEM->html), 500); ?></p>
												<div class="amsd-button-wrapper">
													<a href="<?= $link ?>" class="amsd-button" title="<?= $ITEM->event_title ?>">Learn More <span class="button-icon right"><i class="fas fa-chevron-right"></i></span></a>
												</div>
											</div>
										</div>
									<? } ?>
								<? } ?>
							<? } ?>
						<? } ?>
					</div>
				</div>

			</div>
		</div>
		

	</div>

	<script type="text/javascript">

		$(document).ready(function () {

			if($("[data-event-shown]").length < 1) {
				$('.no-events-to-show').show();
				$('.no-events-to-show').html('<div class="text-block"><p>There are no events to display.</div></div>');
			}

		});

	</script>

<? } ?>
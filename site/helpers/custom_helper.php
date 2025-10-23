<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
-------------------------------------------------------------------
 CUSTOM HELPER FUNCTIONS
-------------------------------------------------------------------

   These functions belong to the site, not the CMS.
   Only put functions that are custom to this site here.

*/

	/*
	-------------------------------------------------------------------
	  customAutoLoad()
	-------------------------------------------------------------------

	   Custom function that is loaded on every page load

	*/

	function customAutoLoad($page) {

		$ci=& get_instance();

	}

	/*
	-------------------------------------------------------------------
	  customWildcards()
	-------------------------------------------------------------------

	   Define custom wildcard rules

	*/

	function customWildcards($page) {

		$wildcards = Array();

		return $wildcards;

	}

	/*
	-------------------------------------------------------------------
	  customOnEachUpdate()
	-------------------------------------------------------------------

	   Fires whenever the cache gets cleared by the CMS

	*/

	function customOnEachUpdate() { 

		$ci=& get_instance();

	}

	/*
	-------------------------------------------------------------------
	  customAPI()
	-------------------------------------------------------------------

	   Define custom API commands

	*/

	function customAPI($CONFIG) {

		$ci=& get_instance();

		$_POST = $CONFIG["post"];
		$command = $CONFIG["command"];
		$param = $CONFIG["param"];

		$output = Array("status" => "error", "message" => "Invalid Command");

		return $output;

	}

	/*
	-------------------------------------------------------------------
	  _titleTag()
	-------------------------------------------------------------------

	   Prints correct title tag according to CSS template

	*/

	function _titleTag($title, $sub_title = false) {

		return '';


	}

	function printWebflowMenu() {

		$ci=& get_instance();
		$CACHED_VARS = $ci->load->{"_ci_cached_vars"};

		$C = Array(
			"NAV" => $CACHED_VARS["nav"],
			"ROOT" => $CACHED_VARS["root"],
			"PAGE" => $CACHED_VARS["page"]
		);

		function recursiveSubs($R = Array(), $C = Array()) {

			$ci=& get_instance();

			// Set Defaults
			if(!isset($R["LEVEL"])) { $R["LEVEL"] = 1; }
			if(!isset($R["URI"])) { $R["URI"] = $R["PARENT"]->slug; }

			// Get parent's submenu items
			$SUBS = getSubMenus($R["PARENT"]->id);

			echo '<nav class="w-dropdown-list ' . ($R["LEVEL"] > 1 ? 'nested-' : '') . 'dropdown-list">';

				echo '<div class="dropdown-links-wrapper">';

				foreach($SUBS as $k => $SUB) {

					// Set first class for dropdown links
					$CLASS_FIRST_DROPDOWN_LINK = $SUB === reset($SUBS) ? " first" : "";

					// Set last class for dropdown links
					$CLASS_LAST_DROPDOWN_LINK = $SUB === end($SUBS) ? " last" : "";

					// Set active class for root items
					$CLASS_ACTIVE = $SUB->id == $C["PAGE"]->id ? " w--current current" : "";

					// Add beginning slash to inner page links and recursive URI
					$SUB_LINK = $SUB->type == 2 ? $SUB->slug : '/' . $R["URI"] . '/' . $SUB->slug;

					// Item is a sideout
					if($SUB->children > 0) {

						echo '<div class="w-dropdown dropdown nested" data-delay="0"' . (M ? '' : ' data-hover="true" ') . '>

							<div class="w-dropdown-toggle dropdown-link' . $CLASS_ACTIVE . $CLASS_FIRST_DROPDOWN_LINK . $CLASS_LAST_DROPDOWN_LINK . '">
								<div class="nested-dropdown-link-text">' . $SUB->title . '</div>
							</div>';

							// So meta...
							recursiveSubs(Array(
								"PARENT" => $SUB,
								"LEVEL" => $R["LEVEL"] + 1,
								"URI" => $R["URI"] . '/' . $SUB->slug
							), $C);

						echo '</div>';

					// Item is a single link
					} else {

						echo '<a class="w-dropdown-link dropdown-link' . $CLASS_ACTIVE . $CLASS_FIRST_DROPDOWN_LINK . $CLASS_LAST_DROPDOWN_LINK . '" href="' . $SUB_LINK . '">' . $SUB->title . '</a>';

					}

				}

				echo '</div>';

			echo '</nav>';

		}

		function mainMenu($C) {

			$COMMON_ITEMS = strings(COMMON_ITEMS_BLOCK_ID);

			// Loop over main nav items
			foreach ($C["NAV"] as $k => $ROOT) {

				/*
				// Logo spacer for when the logo is centered on the navbar
				if(($k+1) == ((sizeof($C["NAV"])/2) + 1)) {
					echo '<div class="logo-spacer"></div>';
				}
				*/

				//Skip secondary nav items
				if($ROOT->secondary_nav) { continue; }

				// Nav Link Icon
				$NAV_LINK_ICON = getPage($ROOT->id)->settings["Nav Icon"];
				if($NAV_LINK_ICON && $NAV_LINK_ICON != 'null'){
					$NAV_LINK_ICON = '<div class="nav-link-icon"><i class="' . $NAV_LINK_ICON . '"></i></div>';
				} else {
					$NAV_LINK_ICON = '';
				}

				// Set active class for root items
				$CLASS_ACTIVE = $ROOT->id == $C["ROOT"]->id ? " w--current current" : "";

				// Set last class for root items
				$CLASS_LAST = $ROOT === end($C["NAV"]) ? " last" : "";

				// Set icon for last root items
				if($ROOT === end($C["NAV"]) && $COMMON_ITEMS['Last Nav Item Icon']) { 
					$NAV_LINK_ICON = '<div class="last-nav-item-icon"><i class="' . $COMMON_ITEMS["Last Nav Item Icon"] . '"></i></div>';
				}

				// Add beginning slash to inner page links
				$ROOT_LINK = $ROOT->type == 2 ? $ROOT->slug : '/' . $ROOT->slug;

				// Item is a dropdown
				if($ROOT->children > 0) {

					echo '<div class="w-dropdown dropdown" data-delay="0"' . (M ? '' : ' data-hover="true" ') . '>

							<div class="w-dropdown-toggle nav-link dropdown-nav-link' . $CLASS_ACTIVE . $CLASS_LAST . '">
								' . $NAV_LINK_ICON . '<div class="nav-link-text">' . $ROOT->title . '</div>
								<div class="dropdown-nav-link-arrow w-icon-dropdown-toggle"></div>
							</div>';

							// Print submenus recursively
							recursiveSubs(Array(
								"PARENT" => $ROOT
							), $C);

					echo '</div>';


				// Item is a single link
				} else {

					echo '<a class="w-nav-link nav-link' . $CLASS_ACTIVE . $CLASS_LAST . '" href="' . $ROOT_LINK . '">' . $NAV_LINK_ICON . '<div class="nav-link-text">' . $ROOT->title . '</div></a>';

				}

			}

		}

		echo '<nav class="w-nav-menu nav-menu" role="navigation">';

			echo '<div class="nav-items-wrapper">';

				mainMenu($C);

				$COMMON_ITEMS = strings(COMMON_ITEMS_BLOCK_ID);

				if($COMMON_ITEMS['Secondary Nav Links']) { ?>

					<div class="secondary-nav-outer-wrapper">
						<div class="secondary-nav-inner-wrapper">
							<? if($COMMON_ITEMS['Social Media Links']) { ?>
								<div class="secondary-nav-links social">
									<? foreach (json_decode($COMMON_ITEMS['Social Media Links']) as $k => $ITEM) { ?>
										<a href="<?= $ITEM->url ?>" class="seconary-nav-social-link w-inline-block">
											<div class="social-link-icon"><i class="<?= $ITEM->icon ?>"></i></div>
										</a>
									<? } ?>
								</div>	
							<? } ?>
							<div class="secondary-nav-links">
								<? foreach (json_decode($COMMON_ITEMS['Secondary Nav Links']) as $k => $ITEM) { ?>
									<a href="<?= $ITEM->url ?>" class="secondary-nav-link w-inline-block">
										<? if($ITEM->icon) { ?><div class="secondary-nav-link-icon"><i class="<?= $ITEM->icon ?>"></i></div><? } ?>
										<div class="secondary-nav-link-text"><?= $ITEM->title ?></div>
									</a>
									<? if($ITEM != end(json_decode($COMMON_ITEMS['Secondary Nav Links']))) { ?>
										<div class="secondary-nav-link-divider"></div>
									<? } ?>
								<? } ?>
							</div>
						</div>
					</div>

				<? }

			echo '</div>';

		echo '</nav>';

	}

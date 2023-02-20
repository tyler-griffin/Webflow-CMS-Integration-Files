<?php

/*
------------------------------------------------------------------
  Global Constants
------------------------------------------------------------------

Define constants here that will be available anywhere in the
application.  (Controllers, Models, Helpers, Libraries, Views)

This file is loaded before any CodeIgniter Framework code, so
you cannot use library functions or custom functions here.

Use this file to define static paths and variables, but for
dynamic variables, use "/site/config/globals.php"

*/

// This is used in the "bustCache()" function to version assets
define("ASSETS_VERSION", "1.0.0");

define(DEV_CONFIG_BLOCK_ID, 1);
define(COMMON_ITEMS_BLOCK_ID, 2);
define(ALERT_BAR_BLOCK_ID, 3);
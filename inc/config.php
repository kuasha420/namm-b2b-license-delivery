<?php
/**
	* Nane: Configuration File
	* Location: inc/config.php
	* Description: Stores all credintials and configs.
	* Author: Arafat Zahan
	* Date: 19-9-2016
	* Since: 0.1.0
**/

////////////////////
// Access Control
////////////////////

if (count(get_included_files()) == 1) {
	exit("Direct access not permitted.");
}

////////////////////
// Constants
////////////////////

// Database Credintials
define("DB_NAME", "");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_HOST", "localhost");
define("DB_REMOTE", "192.168.1.100");

// b2b Credintials
define("SW_NAME", "admin");
define("SW_PASS", "admin");

// Admin Credintials
define("BK_NAME", "admin");
define("BK_PASS", "admin");

// Support Contact on Silent Errors
define("SUPPORT_HINT", "Error! Contact Support with the following info-");
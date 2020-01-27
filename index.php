<?php
/**
	* Nane: License Delivery System
	* Description: NAMM B2B Complaint Electronic License Delivery System
	* Author: Arafat Zahan
	* Version: 0.3.1
	* Date: 18-9-2016
	* Updated: 10-8-2019
	* Requirements: PHP5 -> PHP7, MySQL5
**/


// error reporting

// error_reporting(E_ALL);
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors',1);


////////////////////
// Configuration
////////////////////

require_once("inc/config.php");

////////////////////
// Functions
////////////////////

require_once("inc/functions.php");

////////////////////
// Sub-Routines
////////////////////

if ($_GET["node"] === "backend") {
	include("inc/backend.php");
} elseif ($_GET["node"] === "ajax") {
	include("inc/ajax.php");
} elseif ($_GET["node"] === "export") {
	include("inc/export.php");
} elseif ($_GET["node"] === "report") {
	include("inc/report.php");
} elseif ($_GET["node"] === "test") {
	include("inc/test.php");
} elseif ($_GET["node"] === "login") {
	include("inc/login.php");
} elseif ($_GET["node"] === "logout") {
	session_start();
	session_destroy();
	$redirect = 'Location: '.$_SERVER['SCRIPT_NAME'].'?node=login&action=logout';
	header($redirect);
	exit;
} else {
	include("inc/license.php");
}
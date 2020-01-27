<?php
/**
	* Nane: Automated Report Cron Job
	* Location: inc/report.php
	* Description: Handles the ajax part of backend.
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
// Authenticate
////////////////////

session_start();
if (!$_SESSION['signed_in']) {
	$json = [
		'status' => 'error',
		'message' => 'Authentication failed'
	];
	send_header("jsonfail", "Not Logged In");
	echo json_encode($json, JSON_PRETTY_PRINT);
	exit; // IMPORTANT: Be sure to exit here!
}

////////////////////
// Include PHPMailer
////////////////////

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'lib/PHPMailer/src/Exception.php';
require 'lib/PHPMailer/src/PHPMailer.php';
require 'lib/PHPMailer/src/SMTP.php';

////////////////////
// Cron Routine. 
////////////////////



////////////////////
// DB Connection
////////////////////

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

////////////////////
// Add New Keys
////////////////////

if (isset($_REQUEST['item'])) {

	if ($_REQUEST['item'] == "deliverylog") {

		$sql = "SELECT * FROM usedKeys ORDER BY addedOn ASC WHERE YEAR(addedOn) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
		AND MONTH(addedOn) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
		$result = $conn->query($sql);

		$data = [ [ "ID", "Key", "Product ID", "Order ID", "Date", "Price" ] ];

		while($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$data[] = $row;
		}
	} else {

			$data = [ 
						[ "ID", "Key", "Product ID", "Order ID", "Date", "Price" ],
						[ "0", "Error", "Invalid Item", "n/a", "n/a" ]
					];

	}

} else {
	
	$data = [ 
				[ "ID", "Key", "Product ID", "Order ID", "Date", "Price" ],
				[ "0", "Error", "Invalid Item", "n/a", "n/a" ]
			];

}

$filename = "deliverylog";

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename={$filename}.csv");
header("Pragma: no-cache");
header("Expires: 0");

outputCSV($data);
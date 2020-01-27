<?php
/**
	* Nane: Ajax Handler
	* Location: inc/ajax.php
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

if (isset($_REQUEST['modal'])) {
	
	if (isset($_REQUEST['viewKeys'])) {

		$tableName = $_REQUEST['viewKeys'];
		$query = "SELECT * FROM {$tableName}";

		$result = $conn->query($query);

		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
            	$json[] = $row;
    		}
    		send_header("json", "Keys Retrived");
			echo json_encode($json, JSON_PRETTY_PRINT);
		} else {
			$json[] = [
				'id' => 'n/a',
				'licenseKey' => 'No Keys Available on '.$tableName.'.',
				'addedOn' => 'n/a'
			];
			send_header("json", "No Keys Available");
			echo json_encode($json, JSON_PRETTY_PRINT);
		}

	} elseif (isset($_REQUEST['deleteKey']) && isset($_REQUEST['fromTable'])) {

		$keyId = $_REQUEST['deleteKey'];
		$fromTable = $_REQUEST['fromTable'];
		
		$query = "DELETE FROM {$fromTable} WHERE id = {$keyId}";

		$result = $conn->query($query);

		$json = [
			'status' => 'success',
			'message' => 'Key Deleted'
		];
		send_header("json", "Deleted");
		echo json_encode($json, JSON_PRETTY_PRINT);
	} elseif (isset($_REQUEST['deleteTable'])) {

		$tableName = $_REQUEST['deleteTable'];
		$dbName = DB_NAME;

		$query = "DROP TABLE {$dbMame} . {$tableName}";

		$result = $conn->query($query);

		$json = [
			'status' => 'success',
			'message' => 'Product Deleted'
		];
		send_header("json", "Deleted");
		echo json_encode($json, JSON_PRETTY_PRINT);		
	}

} elseif (isset($_REQUEST['productid']) && isset($_REQUEST['productcodes'])) {

	$tableName = $_REQUEST['productid'];
	$query = "SELECT id FROM {$tableName}";

	$result = $conn->query($query);

	if (empty($result)) {
		$query = "CREATE TABLE {$tableName} (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		licenseKey VARCHAR(55) NOT NULL,
		addedOn TIMESTAMP
		);";

		$result = $conn->query($query);
	}

	$productCodes = str_replace(' ', '', $_REQUEST['productcodes']);
	$licenseKeyArray = array_filter(explode(',', $productCodes));

	// prepare and bind
	$stmt = $conn->prepare("INSERT INTO {$tableName} (licenseKey) VALUES (?)");
	$stmt->bind_param("s", $licenseKey);

	foreach ($licenseKeyArray as $licenseKey) {
		$stmt->execute();
		$stmt->store_result();
	}

	$totalKeys = count($licenseKeyArray);

	$json = [
		'status' => 'success',
		'message' => 'Success! '.$totalKeys.' Keys Inserted Into Table "'.$tableName.'"'
	];
	send_header("json", "New keys added");
	echo json_encode($json, JSON_PRETTY_PRINT);


} else {
	$json = [
		'status' => 'error',
		'message' => 'Invalid Request'
	];
	send_header("json", "Invalid Request");
	echo json_encode($json, JSON_PRETTY_PRINT);
}

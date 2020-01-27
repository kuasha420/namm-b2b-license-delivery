<?php

////////////////////
// Access Control
////////////////////

if (count(get_included_files()) == 1) {
	exit("Direct access not permitted.");
}

////////////////////
// Functions
////////////////////

// For debugging
function pre($data) {
    print '<pre>' . print_r($data, true) . '</pre>';
}

// Function to get a key from given file. 
function get_key($productID, $orderID, $productPrice, $count = 1) {
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	// Check connection
	if ($conn->connect_error) {
	    for ($i=0; $i < $count; $i++) { 
	    	// $output[] = "CONTACT-SUPPORT-{$productID}-{$orderID}-ERR001";
	    	$output[] = SUPPORT_HINT+"Product ID:{$productID} & Order ID:{$orderID}.";
	    }
	} else {
		$sql = "SELECT licenseKey FROM {$productID} ORDER BY id ASC LIMIT {$count}";
		$result = $conn->query($sql);

		if ($result->num_rows === $count) {
		    // output data of each row
		    while($row = $result->fetch_assoc()) {
		        $output[] = $row['licenseKey'];
		    }

		    // prepare and bind
			$stmt = $conn->prepare("INSERT INTO usedKeys (licenseKey, tableName, orderId, price) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("ssid", $licenseKey, $productID, $orderID, $productPrice);

			foreach ($output as $licenseKey) {
				$stmt->execute();
			}

			$stmt->close();

			$sql = "DELETE FROM {$productID} ORDER BY id ASC LIMIT {$count}";
			$result = $conn->query($sql);

		} else {
		    for ($i=0; $i < $count; $i++) { 
	    		$output[] = SUPPORT_HINT;
	    	}
		}
	}
	return $output;
	
}

// Sends header accordingly with proper mime types and other info.
function send_header($node, $debug = "Production") {
	if ($node === "xml") {
		header('Content-type: application/xml');
		header("Cache-Control: no-cache, must-revalidate");
		header("X-DEBUG: {$debug}.");
	} elseif ($node === "json") {
		header('Content-type: application/json');
		header("Cache-Control: no-cache, must-revalidate");
		header("X-DEBUG: {$debug}.");
	} elseif ($node === "html") {
		header('Content-type: text/html');
		header("Cache-Control: no-cache, must-revalidate");
		header("X-DEBUG: {$debug}.");
	} elseif ($node === "xmlfail") {
		header('Content-type: application/xml');
		header('HTTP/1.0 401 Unauthorized');
		header("Cache-Control: no-cache, must-revalidate");
		header("X-DEBUG: {$debug}.");
	} elseif ($node === "jsonfail") {
		header('Content-type: application/json');
		header('HTTP/1.0 401 Unauthorized');
		header("Cache-Control: no-cache, must-revalidate");
		header("X-DEBUG: {$debug}.");
	} elseif ($node === "htmlfail") {
		header('Content-type: text/html');
		header('HTTP/1.0 401 Unauthorized');
		header("Cache-Control: no-cache, must-revalidate");
		header("X-DEBUG: {$debug}.");
	}
}

// 2factor authentication. Returns true 
function authenticate($node, $open = false) {
	if ($open) {
		return true;
	} else {
		if ($node === 'swater') {
			if (isset($_SERVER['HTTP_X_SWEETWATER_SECURITY_USERID']) 
				&& isset($_SERVER['HTTP_X_SWEETWATER_SECURITY_PASSWORD'])) {
				if ($_SERVER['HTTP_X_SWEETWATER_SECURITY_USERID'] ==  SW_NAME
					&& $_SERVER['HTTP_X_SWEETWATER_SECURITY_PASSWORD'] == SW_PASS) {
					return true;
				} else {
					return false;
				}
			} else {
				return false; 
			}
		} elseif ($node === 'backend') {
			if (isset($_POST['username']) && isset($_POST['password'])) {
				if ($_POST['username'] === BK_NAME && $_POST['password'] === BK_PASS) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
	}
}

// Array to CSV
function outputCSV($data) {
    $outputBuffer = fopen("php://output", 'w');
    foreach($data as $val) {
        fputcsv($outputBuffer, $val);
    }
    fclose($outputBuffer);
}
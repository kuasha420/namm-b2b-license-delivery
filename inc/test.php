<?php
/**
	* Nane: Various Tests
	* Location: inc/test.php
	* Description: Temporary test file.
	* Author: Arafat Zahan
	* Date: 19-9-2016
	* Since: 0.1.0
**/

// No need now
exit();
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
	$redirect = 'Location: '.$_SERVER['SCRIPT_NAME'].'?node=login';
	header($redirect);
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

/**
 * create a table for backing up used keys!

$query = "SELECT id FROM 'usedKeys'";

$result = $conn->query($query);

if (empty($result)) {
	$query = "CREATE TABLE usedKeys (
	id INT(7) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	licenseKey VARCHAR(55) NOT NULL,
	tableName VARCHAR(64) NOT NULL,
	addedOn TIMESTAMP
	);";

	$result = $conn->query($query);
}
**/

send_header("html", "Test Page");
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Standard Meta -->
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

	<!-- Site Properties -->
	<title>Test Page</title>

</head>
<body>


  <?php
	$query = "SHOW TABLE STATUS LIKE '%'";
	$result = $conn->query($query);
	/* associative array */

	while($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$rows[] = $row;
	}

	pre($rows);

	/*
	foreach($rows as $row) {
		$trClass = (intval($row['Rows']) > 5) ? "class='positive'" : "class='negative'";
		echo "
		<tr {$trClass}>
	      <td id='{$row['Name']}' class='collapsing left aligned product'>
	        <i class='folder icon'></i> {$row['Name']}
	      </td>
	      <td>{$row['Rows']}</td>
	      <td class='collapsing'>{$row['Update_time']}</td>
	    </tr>";
	}
	*/

  ?>


</body>

</html>
<?php

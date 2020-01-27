<?php
////////////////////
// Access Control
////////////////////

if (count(get_included_files()) == 1) {
	exit("Direct access not permitted.");
}

// authenticate
if (!authenticate("swater")) {
	send_header("xmlfail", "failed at authentication");
	echo "
		<XML>
			<ERROR>
				<MSG>Authentication failed!</MSG>
			</ERROR>
		</XML>
	";
	exit();
}

// Receive Request xml
// Turns off build in SimpleXML error handling. 
libxml_use_internal_errors(true);
// Define  empty variable for global scope
$poXML = "";
// search through uploaded files and post data for valid xml 
if (!empty($_FILES)) {
	foreach($_FILES as $file) {
		if (empty($poXML)) {
			// Tries and loads xml to SimpleXML Object. 
			$poXML = simplexml_load_file($file['tmp_name']);
		} else {
			// breaks out of loop when valid xml found. 
			break;
		}
	}
} elseif (!empty($_POST)) {
	foreach ($_POST as $postXML) {
		if (empty($poXML)) {
			// Tries and loads xml to SimpleXML Object. 
			$poXML = simplexml_load_string($postXML);
		} else {
			// breaks out of loop when valid xml found. 
			break;
		}
	}
} else { // For debugging purpose 
	$XMLString = " <NAMM_PO version='2009.2' xmlns='http://namm.com/PO/2009.2'>
			<POHeader>
				<BuyerOrderId>694482</BuyerOrderId>
			</POHeader>
			<PODetail>
				<Items>
					<Item>
        				<POLineNbr>1</POLineNbr>
        				<Qty>3</Qty>
        				<UCValue>49</UCValue>
        				<UCCurrencyCode>USD</UCCurrencyCode>
        				<SupplierItemId>newtest4</SupplierItemId>
     		 		</Item>
					<Item>
        				<POLineNbr>2</POLineNbr>
        				<Qty>2</Qty>
        				<UCValue>99</UCValue>
        				<UCCurrencyCode>USD</UCCurrencyCode>
        				<SupplierItemId>newtest3</SupplierItemId>
     		 		</Item>
   				</Items>
  			</PODetail>
  		</NAMM_PO> ";
  	$poXML = simplexml_load_string($XMLString);
}
// Get all items informations from parsed xml 
// Gets a messed up mixture of arrays and objects. 
$OrderId = $poXML->POHeader->BuyerOrderId;
$Items = $poXML->PODetail->Items->Item; 
// Generates a consistant array of items. 
$ItemsArray = array_map('get_object_vars', iterator_to_array($Items, FALSE));

// Start constructing FuncAck XML
// Set timezone to surpass related errors. 
date_default_timezone_set('America/New_York');
//c Create Timestamp
$date = date_create();
$timestamp = date_format($date, 'Y-m-d\TH:i:s');
// Start constuction of XML with new SimPleXML Object. 
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><InstantElectronicDelivery version="2009.1"/>');
// Add Timestamp 
$xml->addChild('Timestamp', $timestamp);
// Add StatusCode
$xml->addChild('StatusCode', 'C01');
// Add StatusMessage
$xml->addChild('StatusMessage', 'Received');
// Add Items Parent Node
$itemsNode = $xml->addChild('Items');

//Add Product Informations and deliver license within for each ITEM. 
foreach ($ItemsArray as $Item) {
	// Define variables
	$SupplierItemId = $Item["SupplierItemId"];
	$Qty = $Item["Qty"];
	$UCValue = $Item["UCValue"];
	$ProductPrice = (double) $UCValue;
	$UCCurrencyCode = $Item["UCCurrencyCode"];
	$LicenseCount = (int) $Qty;
	$Counter = 0;
	// Add Item Node
	$itemNode = $itemsNode->addChild('Item');
	// Add Basic Product Information inside Item Node
	$itemNode->addChild('SupplierItemId', $SupplierItemId);
	$itemNode->addChild('Qty', $Qty);
	$itemNode->addChild('UCValue', $UCValue);
	$itemNode->addChild('UCCurrencyCode', $UCCurrencyCode);
	// Add Licenses Node
	$licensesNode = $itemNode->addChild('Licenses');
	// Add Licemse inside Licenses Node according to the Product Quantity. 
	// Get License Key(s).
	$LicenseKeys = get_key($SupplierItemId, $OrderId, $ProductPrice, $LicenseCount);
	foreach ($LicenseKeys as $License) {
		// Add license to Licenses Node
		$licensesNode->addChild('License', $License);
	}
}
// Construction of XML complete. Get the whole thing in a variable
$getXML = $xml->asXML();

// Prettify XML with DOMDocument. 
$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($getXML);

// semds header 
send_header("xml", "license file created successfully");
// echoes the entire XML. 
echo $dom->saveXML();
// We're done, yeyy!! 
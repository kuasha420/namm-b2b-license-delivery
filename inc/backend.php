<?php
/**
	* Nane: Admin Panel
	* Location: inc/backend.php
	* Description: Veiew/Edit/Add License Keys to the system.
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

send_header("html", "Admin Panel");
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Standard Meta -->
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

	<!-- Site Properties -->
	<title>Admin Panel</title>
	<link rel="stylesheet" type="text/css" href="lib/semantic/semantic.min.css">

	<script src="lib/jquery/jquery.min.js"></script>
	<script src="lib/jquery/jquery.serialize-object.min.js"></script>
	<script src="lib/jquery/jquery.tablesort.min.js"></script>
	<script src="lib/semantic/semantic.min.js"></script>

	<style type="text/css">
	body {
		background-color: #DADADA;
	}

	.column {
		background-color: white;
	}

	.product {
		cursor: pointer;
	}
	</style>
  <script>
  var ajaxurl = <?php echo "'".$_SERVER['SCRIPT_NAME']."?node=ajax'"; ?>;
  $(document)
    .ready(function() {
      $('.ui.form')
        .form({
          fields: {
            id: {
              identifier  : 'productid',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please Enter Product ID!'
                },
				{
				  type   : 'doesntContain[ ]',
				  prompt : 'Product ID can not contain Space!'
				},
				{
				  type   : 'minLength[6]',
				  prompt : 'Product ID can not be less than {ruleValue} characters long!'
				},
				{
				  type   : 'maxLength[63]',
				  prompt : 'Product ID can not be more than {ruleValue} characters long!'
				}
              ]
            },
            code: {
              identifier  : 'productcodes',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please enter at least one License Code!'
                },
				{
				  type   : 'doesntContain[ ]',
				  prompt : 'Product ID can not contain Space!'
				}
              ]
            }
          }
        }).api({
		    url: ajaxurl,
		    method : 'POST',
		    serializeForm: true,
		    beforeSend: function(settings) {
		    	console.log(settings.data);
            	return settings;
		    },
		    onSuccess: function(data) {
		    	console.log(data);
		    	$('.ui.form').form('set value', 'productcodes', data.message);
		    }
		});
        $('.product').click(function(){

	    var productId = $(this).attr("id");

	    $.ajax
		    ({ 
		        url: ajaxurl,
		        data: {"modal": "modal", "viewKeys": productId},
		        type: 'post',
		        success: function(data)
		        {
		            var html = "";
					$.each(data, function(key, val)
					{
					    html += "<tr><td>" +val.id+ "</td><td>" +val.licenseKey+ "</td><td>" +val.addedOn+ "</td><td id='"+productId+'-'+val.id+"' data-table='"+productId+"' data-id='"+val.id+"' class='ui deleteKey negative button'>Delete Key</td></tr>";
					});
					$('#table-name').html(productId);
					$('#table-delete').data("delete", productId);
					$('#modal-content').html(html);
					$('.ui.modal').modal('show');
					$('#table-delete').click(function(){

					    var tableName = $(this).data("delete");

						if(confirm("Are you sure you want to delete "+tableName+"?")){
					        $.ajax
						    ({ 
						        url: ajaxurl,
						        data: {"modal": "modal", "deleteTable": tableName},
						        type: 'post',
						        success: function(data)
						        {
									$('#table-delete').removeClass('negative').addClass('positive').html(data.message);
						        }
						    });
					    }
					    else{
					        return false;
					    }
				    });
					$('.deleteKey').click(function(){

				    var keyId = $(this).data("id");
				    var fromTable = $(this).data("table");
				    var elementID = '#' + $(this).attr("id");

				    $.ajax
					    ({ 
					        url: ajaxurl,
					        data: {"modal": "modal", "deleteKey": keyId, "fromTable": fromTable},
					        type: 'post',
					        success: function(data)
					        {
								$(elementID).removeClass('ui negative button').addClass('positive').html(data.message);
					        }
					    });
				    });
		        }
		    });
	    });
        $('.ui.refresh.button').click(function() {
          window.location.reload();
        });
        $('.ui.logout.button').click(function() {
          window.location.href = <?php echo "'".$_SERVER['SCRIPT_NAME']."?node=logout'"; ?>;
        });
        $('table.sortable').tablesort().data('tablesort').sort($("th.default-sort"));
    })
  ;
  </script>
</head>
<body>

	<div class="ui container">
		<div class="ui padded stackable grid">
			<div class="row">
				<div class="center aligned column">
					<h2 class="ui icon header">
						<i class="settings icon"></i>
						<div class="content">
							License Manager
							<div class="sub header">Manage License Codes.</div>
						</div>
					</h2>
					<button class="ui right floated logout button">logout</button>
					<button class="ui right floated refresh button">refresh</button>
				</div>
			</div>
			<div class="row">
				<div class="nine wide column">
					<h3 class="ui header">
						<i class="database icon"></i>
						<div class="content">
							Add New License
							<div class="sub header">Add new license codes to Database</div>
						</div>

						<div class="ui divider"></div>

					</h3>

					<form class="ui large form" method="post">
					  <div class="ui stacked segment">
					  	  <div class="field">
						    <label>Product ID</label>
						    <input type="text" name="productid" placeholder="Product ID">
						  </div>
						  <div class="field">
						    <label>License Codes</label>
						    <textarea name="productcodes" placeholder="Comma-separated list of License Codes"></textarea>
						  </div>
					    <div class="ui fluid large submit button">Submit</div>
					  </div>

					  <div class="ui error message"></div>

					</form>

					<div class="ui divider"></div>

					<h3 class="ui header">
						<i class="barcode icon"></i>
						<div class="content">
							Recently Delivered
							<div class="sub header">License Codes recently served from the system</div>
						</div>
					</h3>

					<div class="ui divider"></div>

					<table class="ui celled striped single line right aligned table">
					  <thead>
					    <tr>
						    <th class="left aligned">
						      Product ID
						    </th>						    
						    <th class="left aligned">
						      License Key
						    </th>
						    <th>
						      Order ID
						    </th>						    
						    <th>
						      Served On
						    </th>
					  	</tr>
					  </thead>
					  <tbody>
					  <?php
						$sql = "SELECT * FROM usedKeys ORDER BY addedOn DESC LIMIT 20";
						$result = $conn->query($sql);
						/* associative array */

						while($row = $result->fetch_array(MYSQLI_ASSOC)) {
							$rowsx[] = $row;
						}

						foreach($rowsx as $row) {
							echo "
							<tr>
						      <td class='left aligned'>{$row['tableName']}</td>
						      <td class='left aligned'>{$row['licenseKey']}</td>
						      <td>{$row['orderId']}</td>
						      <td >{$row['addedOn']}</td>
						    </tr>";
						}

					  ?>
					  </tbody>
					</table>

					<div class="ui divider"></div>

					<a href=<?php echo '"'.$_SERVER['SCRIPT_NAME'].'?node=export&item=deliverylog"' ?>>Download Delivery Log</a>


				</div>
				<div class="six wide right floated column">
					<h3 class="ui header">
						<i class="barcode icon"></i>
						<div class="content">
							Statistics
							<div class="sub header">Current Stock</div>
						</div>
					</h3>

					<div class="ui divider"></div>

					<table class="ui celled striped single line right aligned sortable table">
					  <thead>
					    <tr>
						    <th class="left aligned">
						      Product ID
						    </th>						    
						    <th class="default-sort">
						      Remaining
						    </th>						    
						    <th>
						      Last Accessed
						    </th>
					  	</tr>
					  </thead>
					  <tbody>
					  <?php
						$query = "SHOW TABLE STATUS LIKE '%'";
						$result = $conn->query($query);
						/* associative array */

						while($row = $result->fetch_array(MYSQLI_ASSOC)) {
							$rows[] = $row;
						}

						foreach($rows as $row) {
							$intRow = intval($row['Rows']);
							$trClass = ($intRow > 5) ? "class='positive'" : "class='negative'";
							$tdSortValue = str_pad($intRow, 5, "0", STR_PAD_LEFT);
							echo "
							<tr {$trClass}>
						      <td id='{$row['Name']}' class='collapsing left aligned product'>
						        <i class='folder icon'></i> {$row['Name']}
						      </td>
						      <td data-sort-value='{$tdSortValue}'>{$row['Rows']}</td>
						      <td class='collapsing'>{$row['Update_time']}</td>
						    </tr>";
						}

					  ?>
					  </tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
	<div class="ui modal">
		<div class="header"><span id="table-name"></span><button id="table-delete" data-delete="" class="ui right floated negative button">Delete Product</button></div>
		<div class="content">
			<table class="ui celled striped single line table">
				<thead>
					<tr>
						<th>ID</th>						    
						<th>Code</th>						    
						<th>Added On</th>						    
						<th>Delete</th>
					</tr>
				</thead>
				<tbody id="modal-content">
				</tbody>
			</table>
		</div>
	</div>

</body>

</html>
<?php

<?php
/**
	* Nane: Admin Login Page
	* Location: inc/backend.php
	* Description: Login Page for Admin action. 
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
// Login Control
////////////////////

session_start();
if ($_SESSION['signed_in']) {
	$redirect = 'Location: '.$_SERVER['SCRIPT_NAME'].'?node=backend';
	header($redirect);
	exit; // IMPORTANT: Be sure to exit here!
} elseif (isset($_POST['username']) && isset($_POST['password'])) {
	// Get Username and Password
	$authentication = authenticate('backend');

	// clear out any existing session that may exist
	session_destroy();
	session_start();

	if ($authentication) {
		$_SESSION['signed_in'] = true;
		$_SESSION['username'] = $_POST['username'];
		$redirect = 'Location: '.$_SERVER['SCRIPT_NAME'].'?node=backend';
		header($redirect);
		exit();
	} else {
		$redirect = 'Location: '.$_SERVER['SCRIPT_NAME'].'?node=login&action=attempt';
		header($redirect);
		exit();
	}
} else { ?>
<!DOCTYPE html>
<html>
<head>
  <!-- Standard Meta -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

  <!-- Site Properties -->
  <title>Admin Panel Login</title>
  <link rel="stylesheet" type="text/css" href="lib/semantic/semantic.min.css">

  <script src="lib/jquery/jquery.min.js"></script>
  <script src="lib/semantic/semantic.min.js"></script>

  <style type="text/css">
    body {
      background-color: #DADADA;
    }
    body > .grid {
      height: 100%;
    }
    .column {
      max-width: 450px;
    }
  </style>
  <script>
  $(document)
    .ready(function() {
      $('.ui.form')
        .form({
          fields: {
            name: {
              identifier  : 'username',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please Enter Your User Name'
                }
              ]
            },
            password: {
              identifier  : 'password',
              rules: [
                {
                  type   : 'empty',
                  prompt : 'Please enter your password'
                }
              ]
            }
          }
        })
      ;
    })
  ;
  </script>
</head>
<body>

	<div class="ui middle aligned center aligned grid">
	  <div class="column">
	<h2 class="ui icon teal header">
	  <i class="sign in icon"></i>
	  <div class="content">
	    Log In
	    <div class="sub teal header">Login with supplied credentials.</div>
	  </div>
	</h2>

	<?php
	if ($_REQUEST['action'] === 'attempt') { ?>
	  <div class="ui error message">
	    Login failed, please enter correct info.
	  </div>
    <?php
  } 
	if ($_REQUEST['action'] === 'logout') { ?>
	  <div class="ui info message">
	    You have been logged out successfully.
	  </div>
    <?php
  } 
  ?>

    <form class="ui large form" method="post">
      <div class="ui stacked segment">
        <div class="field">
          <div class="ui left icon input">
            <i class="user icon"></i>
            <input type="text" name="username" placeholder="User Name">
          </div>
        </div>
        <div class="field">
          <div class="ui left icon input">
            <i class="lock icon"></i>
            <input type="password" name="password" placeholder="Password">
          </div>
        </div>
        <div class="ui fluid large teal submit button">Login</div>
      </div>

      <div class="ui error message"></div>

    </form>

  </div>
</div>

</body>

</html>
<?php
}
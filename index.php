<?
	include "includes/functions.inc.php";
        include "includes/config.inc.php";
	include "includes/defaultHeader.inc.php";

        if (strcmp($operation, "login") == 0){
		$ip_from = $REMOTE_ADDR;
		$token = authenticate($username, $password, $ip_from);

                $datetime = date("Y-m-d H:i:s");
                $ip_from = $REMOTE_ADDR;

		// update the "login" table to record login success/failure
	        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

		$str_sql = "INSERT INTO $db_tablename_login (who, ip, datetime, success) VALUES ('$username', '$ip_from', '$datetime', '$success')";

		// print "Query: *$str_sql*<br>\n";

	        $result = mysql_db_query($db_name, $str_sql, $id_link);
	        if (! $result){
        	        print "Failed to submit!<br>\n";
                	include "$footer";
	                exit;
	        }

		if ($token){

			$access_level = access_level($username);

			// Access Code: 
			// -1 => No Access (Outsiders)
			// 0 => Admin Access (Systems)
			// 1 => Read-Only Acces (Support)

			if ( ($access_level == $ADMIN) || ($access_level == $READ) ){
				HEADER("Location: main.php?username=$username&token=$token&refresh_rate=$default_refresh_rate");

			}

			else{
				HEADER("Location: index.php?badlogin=1");
				exit;
			}

		}

		// not authenticated
		else {
//			print $token;
//			print $str_sql;
			HEADER("Location: index.php?badlogin=1");
			exit;

		}

	}

// here, first time the form is loaded, print out the login screen
	else{

?>

<div class="container">
  <div class="row text-center">

<?
	if ($badlogin == 1){
?>
<div class="container-fluid">
<div class="col-md-6 col-md-offset-3">
<div class="alert alert-danger alert-dismissible fade in" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button><strong>Error!</strong> Username or password invalid. Please try again.
</div></div></div>
<?
	} else if ($q == 'sessionTimeout') {
?>
<div class="container-fluid">
<div class="col-md-6 col-md-offset-3">
<div class="alert alert-warning alert-dismissible fade in" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button><strong>Warning!</strong> Your session has timed out for security. Please log in again.
</div></div></div>
<?
	}
?>

    <div class="container-fluid">
    <div class="col-md-4 col-md-offset-4">
      <form class="form-signin" id="loginForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="panel panel-default">
	<div class="panel-heading">
        <h3 class="form-signin-heading">DHCP Manager<br /><small>Sign in to console</small></h3>
	</div>
	<div class="panel-body">
	<fieldset class="form-group"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
        <input type="text" name="username" class="form-control" placeholder="GSB Account ID" required autofocus></div>
	</fieldset>
        <fieldset class="form-group"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
	<input type="password" name="password" class="form-control" placeholder="Password" required></div>
	</fieldset>
	<input type="hidden" name="operation" value="login">
	</div>
	<div class="panel-footer">
	<div class="btn-group btn-group-justified" role="group">
	<p><button class="btn btn-primary btn-block" type="submit">Login</button>
	<p><button class="btn btn-secondary btn-block" type="clear">Clear</button>
	</div>
	</form>
	</div>
</div></div></div>
<script>
$(document).ready(function() {
    $('#loginForm').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            username: {
                validators: {
                    stringLength: {
			min: 3,
                        max: 25,
                        message: 'The username is not valid'
                    },
                    notEmpty: {
                        message: 'A username is required to login'
                    }
                }
            },
            password: {
                validators: {
                    stringLength: {
                        min: 3,
                        message: 'The password does not meet minimum requirements'
                    },
                    notEmpty: {
                        message: 'A password is required to login'
                    }
                }
            }
        }
    });
});
</script>
<?
        include "$footer";

        }
?>

<?php

/**
 * Login page for DHCP Management Console
 * JS requested but not required - using it for form validation
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Zachary Moffitt <zac@gsb.columbia.edu>
 * @copyright 2016 Columbia Business School
 */

    /*                  
     * Configure information about the page
     */                 
                        
    $pageTitle = "Login Required";

/*
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */
    require "includes/functions.inc.php";
    require "includes/config.inc.php";
    require "includes/header.inc.php";
    $q = $_GET['q'];

/*
 * Do the login operation via include 'authenticate' function
 * Token is returned and compared to the database to verify
 */ 
    if (strcmp($operation, "login") == 0) {
        $ip_from = $REMOTE_ADDR;
        $token = authenticate($username, $password, $ip_from);
        $datetime = date("Y-m-d H:i:s");

        // update the "login" table to record login success/failure
        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
        $str_sql = "INSERT INTO $db_tablename_login (who, ip, datetime, success) VALUES ('$username', '$ip_from', '$datetime', '$success')";
        $result = mysql_db_query($db_name, $str_sql, $id_link);
            if (! $result) {
	        die ("Error occurred during submission to DB!");
            }
     
            // check for a valid token and send them to the right place or give them a valid warning           
            if ($token) {
                $access_level = access_level($username);
                if ($access_level == $ADMIN || $access_level == $READ) {
                    header("Location: stats.php?username=$username&token=$token&refresh_rate=$default_refresh_rate");
                } else {
                    exit(header("Location: index.php?q=notAuthorized"));
                }
            } else { // Unable to authenexit(header("Location: index.php?q=authFail"));
                    exit(header("Location: index.php?q=authFail"));
            }
    }
	
    if ($q == 'logout') {
        logout($username, $token, $ip_from);
	HEADER("Location: index.php?q=logoutSuccess");
    }

/*
 * Switch to Alternative Syntax to allow HTML definitions based on error codes
 * There are three (3) types of errors: authFailure, notAuthorized, sessionTimeout
 */ 

?>
<!-- Start Body -->
<div class="container">
    <div class="row text-center">
         <br /><br />

      <? if ($q == 'authFail'): ?>
      <!-- AuthFailure Block -->
      <div class="container-fluid">
        <div class="col-md-6 col-md-offset-3">
          <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong>Error!</strong> Username or password invalid. Please try again.
          </div>
        </div>
      </div>

      <? elseif ($q == 'notAuthorized'): ?>
      <!-- NotAuthorized Block --> 
      <div class="container-fluid">
        <div class="col-md-6 col-md-offset-3">
          <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong>Error!</strong> Your account is not permitted to use this service.
          </div>
        </div>
      </div>

      <? elseif ($q == 'sessionTimeout'): ?>
      <!-- Timeout Block -->
      <div class="container-fluid">
        <div class="col-md-6 col-md-offset-3">
          <div class="alert alert-warning alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong>Warning!</strong> Your session has timed out for security. Please log in again.
          </div>
        </div>

      <? elseif ($q == 'logoutSuccess'): ?>
      <!-- Logout Success Block -->
      <div class="container-fluid">
        <div class="col-md-6 col-md-offset-3">
          <div class="alert alert-success alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> <strong>Logout Successful!</strong> You have been logged off successfully.
          </div>
        </div>

<? endif; ?>
    <!-- Login Form --> 
<div class="container-fluid">
  <div class="col-md-4 col-md-offset-4">
    <form class="form-signin" id="loginForm" method="post" action="/dhcp/index.php">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="form-signin-heading">DHCP Manager<br /><small>Sign in to console</small></h3>
        </div>
        <div class="panel-body">
          <fieldset class="form-group">
            <div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
              <input type="text" name="username" class="form-control" placeholder="GSB Account ID" required autofocus>
            </div>
          </fieldset>
          <fieldset class="form-group">
            <div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
              <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
          </fieldset>
          <input type="hidden" name="operation" value="login">
        </div>
        <div class="panel-footer">
          <div class="btn-group btn-group-justified" role="group">
            <p><button class="btn btn-primary btn-block" type="submit">Login</button></p>
            <p><button class="btn btn-secondary btn-block" type="clear">Clear</button></p>
          </div>
        </div>
    </div>
    </form>
    </div>
  </div>
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
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9\s]+$/,
                        message: 'Only alphanumeric characters are permitted'
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

<? include $footer ?>

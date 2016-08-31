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

        $pageTitle = "Global Configuration";


/*
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */

        include "includes/authenticate.inc.php";
        include "includes/config.inc.php";
	require "includes/header.inc.php";
        $access_level = access_level($username);
        $who = $username;
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h3 class="text-center">DHCP Manager<br /><small class="text-muted">Global Configuration</small></h3>
    </div>
  </div>
  <hr>
<? 

	if (strcmp($action, "modify_global") == 0){

                include "admin_check.inc.php";

		if (! $dns_1 || ! $dns_2 || ! $wins_1){
			print "<center><font color=ff0000><b>\n";
			print "Domain name, NTP Server, DNS Server 1, DNS Server 2, WINS Server 1, and WINS Server 2 can NOT be empty!<br>\n";
			print "</b></font></center><br>\n";
			include "$footer";
			exit;
		}

	        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
       		$str_sql = "UPDATE $db_tablename_global set ntp_server='$ntp_server', dns_1='$dns_1', dns_2='$dns_2', dns_3='$dns_3', dns_4='$dns_4', dns_5='$dns_5', wins_1='$wins_1', wins_2='$wins_2' WHERE id=1";


	        $result = mysql_db_query($db_name, $str_sql, $id_link);

       		if (! $result){
                	print "Failed to submit!<br>\n";
	       	        include "$footer";
        	       	exit;
	        }

                $datetime = date("Y-m-d H:i:s");
                $ip_from = $REMOTE_ADDR;

                if (strcmp("$ntp_server", "$old_ntp") != 0){
                        $changes .= "NTP: $old_ntp => $ntp_server. ";
                        $changes .= "<br>\n";
                }

                if (strcmp("$dns_1", "$old_dns_1") != 0){
                        $changes .= "DNS 1: $old_dns_1 => $dns_1. ";
			$changes .= "<br>\n";
                }

                if (strcmp("$dns_2", "$old_dns_2") != 0){
                        $changes .= "DNS 2: $old_dns_2 => $dns_2. ";
			$changes .= "<br>\n";
                }
                if (strcmp("$dns_3", "$old_dns_3") != 0){
                        $changes .= "DNS 3: $old_dns_3 => $dns_3. ";
			$changes .= "<br>\n";
                }
                if (strcmp("$dns_4", "$old_dns_4") != 0){
                        $changes .= "DNS 4: $old_dns_4 => $dns_4. ";
			$changes .= "<br>\n";
                }
                if (strcmp("$dns_5", "$old_dns_5") != 0){
                        $changes .= "DNS 5: $old_dns_5 => $dns_5. ";
			$changes .= "<br>\n";
                }

                if (strcmp("$wins_1", "$old_wins_1") != 0){
                        $changes .= "WINS 1: $old_wins_1 => $wins_1. ";
			$changes .= "<br>\n";
                }

                if (strcmp("$wins_2", "$old_wins_2") != 0){
                        $changes .= "WINS 2: $old_wins_2 => $wins_2. ";
			$changes .= "<br>\n";
                }

                if ($changes){
                        $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'global', '$changes', '$datetime')";


	                $result = mysql_db_query($db_name, $str_sql, $id_link);

        	        if (! $result){
                	        print "Failed to submit log!<br>\n";
                                include "$footer";
                               	exit;
                        }

                }

		print "<center><font color=ff0000>\n";
		print "<b>Changes have been applied to the Global Options.</b>\n";
		print "<br><b><i><small>(Will Take Effect In About 1 Minute)</small></i></b>\n";
		print "</font></center>\n";
		mark_update("localhost");

	}


        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
       	$str_sql = "SELECT * FROM $db_tablename_global";

        $result = mysql_db_query($db_name, $str_sql, $id_link);

       	if (! $result){
                print "Failed to submit!<br>\n";
       	        include "$footer";
               	exit;
        }

	$row = mysql_fetch_object($result);
	$domain = $row->domain;
	$ntp_server = $row->ntp_server;
	$dns_1 = $row->dns_1;
	$dns_2 = $row->dns_2;
	$dns_3 = $row->dns_3;
	$dns_4 = $row->dns_4;
	$dns_5 = $row->dns_5;
	$wins_1 = $row->wins_1;
	$wins_2 = $row->wins_2;

	print "<form data-async method=\"post\" action=\"modify_global.php\" role=\"form\">\n";
	print "<input type=hidden name=action value=modify_global>\n";
	print "<input type=hidden name=old_ntp value=\"$ntp_server\">\n";
	print "<input type=hidden name=old_dns_1 value=\"$dns_1\">\n";
	print "<input type=hidden name=old_dns_2 value=\"$dns_2\">\n";
	print "<input type=hidden name=old_dns_3 value=\"$dns_3\">\n";
	print "<input type=hidden name=old_dns_4 value=\"$dns_4\">\n";
	print "<input type=hidden name=old_dns_5 value=\"$dns_5\">\n";
	print "<input type=hidden name=old_wins_1 value=\"$wins_1\">\n";
	print "<input type=hidden name=old_wins_2 value=\"$wins_2\">\n";
        print "<input type=hidden name=username value='$username'>\n";
        print "<input type=hidden name=token value='$token'>\n";
?>
    <div class="container-fluid">
    <div id="row"><div class="col-xs-6 col-xs-offset-3">
	<div class="panel panel-default">
	<div class="panel-heading"><h4 class="panel-title strong">Global Configuration</div>

        <? if ((strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN)): ?>
        <? $disabled = "" ?>
        <? else: ?>
        <? $disabled = "disabled" ?>
	<? endif; ?>

	<div class="panel-body">
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">Domain:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="domain" value="<? echo $domain ?>" disabled /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">NTP:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="ntp_server" value="<? echo $ntp_server ?>" <? echo $disabled ?> /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">DNS #1:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="dns_1" value="<? echo $dns_1 ?>" <? echo $disabled ?> /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">DNS #2:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="dns_2" value="<? echo $dns_2 ?>" <? echo $disabled ?> /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">DNS #3:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="dns_3" value="<? echo $dns_3 ?>" <? echo $disabled ?> /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">DNS #4:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="dns_4" value="<? echo $dns_4 ?>" <? echo $disabled ?> /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">DNS #5:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="dns_5" value="<? echo $dns_5 ?>" <? echo $disabled ?> /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">WINS #1:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="wins_1" value="<? echo $wins_1 ?>" <? echo $disabled ?> /></div></div></fieldset>
        <fieldset class="form-group"><div class="form-group row"><label class="col-md-2 form-control-label">WINS #2:</label>
        <div class="col-md-10"><input type="text" class="form-control" name="wins_2" value="<? echo $wins_2 ?>" <? echo $disabled ?> /></div></div></fieldset>
	</div>

	<? if ((strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN)): ?>
	<div class="panel-footer">
	<div class="row text-center">
	<button type="submit" class="btn btn-primary">Modify Options</button>
	</div></div>
	<? endif; ?>

	</form>
	</div>
	</div></div></div>
</script>

<!-- Include Javascript for window handling --> 
<script type="text/javascript">
$(document).ready(function() {
jQuery(function() {
    $('form[data-async]').on('submit', function() {
    var op = $(this).attr('data-op');
    var id = $(this).attr('data-id');
    var username = $(this).attr('data-username');
    var token = $(this).attr('data-token');
    var grp = $(this).attr('data-grp');
    var title = $(this).attr('data-title');
    var ip = $(this).attr('data-ip');
    var $form = $(this);

        $.ajax ({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
        })
        
        .success (function(response) {
            bootbox.dialog({
                title:  title,
                message: $(response),
                show: false, // We will show it manually later
                onEscape: function() { console.log("Escape!"); },
                backdrop: true,
                callback: function() { $('.bootbox.modal').modal('hide'); }
            })
                
            .on('shown.bs.modal', function() {
                $('#modalBody').show()
            })

            .on('hide.bs.modal', function(e) { parent.location.reload(true); })

            .modal('show');
        });
    });
});
});
</script>
<? include "$footer"; ?>


<?php

/**
 * IP information management page for DHCP Management Console
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
        
    $pageTitle = "Modify IP: $ip ";
        
        
    /*
     * initialize the includes for functions and generate the header
     * use this in all front-end pages to ensure uniformity
     */ 

	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	include "includes/lease2name.inc.php";
	include "includes/header.inc.php";
    include_once "includes/functions.inc.php";

    /* Push and pull vars between pages */
        
    $_GET['username'] = $username;
	$access_level = access_level($username);
	$who = $username;
	$management_of = "ip";

    /* Use the body include to centralize formatting */
    include "includes/body.inc.php"; 
            
    /* init a db connection for upcoming operations */
    $connection = db_connect($db_hostname,$db_username,$db_password,$db_name);
    $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

    if (strcmp($action, "modify_ip") == 0) 
    {

        include "includes/admin_check.inc.php";

        // check if the IP address is static/reserved, or is in use.
        // only check check when $ip is different from $old_ip
        if (strcmp($ip, $old_ip) != 0) { ip_in_use($ip); }

        // check mac for right format ONLY if ip_type is 'reserved'
        if (strcmp($ip_type, "reserved") == 0) { check_mac_format($mac); }

        // check computername for right format ONLY if ip_type is 'reserved' or 'static'.  Also, make sure computername & mac are NOT empty
        if ((strcmp($ip_type, "reserved") == 0) || (strcmp($ip_type, "static") == 0)) { check_computername_format($user); if (!$user || !$mac) { exit (print ($top . "Computer Name and MAC are required for Reserved and Static IPs!\n")); } }

    	// replace \n or \r with space. otherwise, some comments will become invalid statements in /etc/dhcpd.conf
    	$notes = ereg_replace("[\n\r]", " ", $notes);

    	// update the ip record
    	if (strcmp($ip_type, "dynamic") == 0) { $str_sql = "UPDATE $db_tablename_ip set username='', ip_type='$ip_type', lease='0', clientname='', mac='', notes='' WHERE ip='$ip'"; }
        else { $str_sql = "UPDATE $db_tablename_ip set username='$user', ip_type='$ip_type', lease='$lease', clientname='$clientname', mac='$mac', notes='$notes', lastUpdated='$username' WHERE ip='$ip'"; }

    	$result = mysql_db_query($db_name, $str_sql, $id_link);
/*
    	if (! $result) { exit(print "Failed to save to DB!<br>\n"); }

    	$datetime = date("Y-m-d H:i:s");
    	$ip_from = $REMOTE_ADDR;

    	if (strcmp("$user", "$old_username") != 0) { $changes .= ("Computer Name: " . $old_username . " => " . $user . "<br>\n"); }

    	if (strcmp("$ip_type", "$old_ip_type") != 0) { $changes .= "Type: " . $old_ip_type . " => " . $ip_type . "<br>\n "; }

    	if (strcmp("$lease", "$old_lease") != 0) { $changes .= "Lease:" $old_lease . " => " . $lease . "<br>\n"; }

    	if (strcmp("$clientname", "$old_clientname") != 0) { $changes .= "Client Name: " $old_clientname . " => " . $clientname . "<br>\n"; }

    	if (strcmp("$mac", "$old_mac") != 0) { $changes .= "MAC: " . $old_mac . " => " . $mac . "<br>\n"; }

	    if (strcmp(trim("$notes"), trim("$old_notes")) != 0){
		    $changes .= "Notes: $old_notes => $notes. ";
    		$changes .= "<br>\n";
	    }

    	// if there is a IP switch
    	if (strcmp("$ip", "$old_ip") != 0){
    		$changes = "<b>IP: $old_ip => $ip</b><br>\n" . $changes;
    	}

    	// update the change log
    	if ($changes){

    		if (strcmp("$ip", "$old_ip") == 0){
    			$changes = "<b>IP: $ip</b><br>\n" . $changes;
    		}

	    	$str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'ip', '$changes', '$datetime')";
    		$result = mysql_db_query($db_name, $str_sql, $id_link);

	    	if (! $result){
		    	exit(print "Failed to submit log!\n");
    		}

    	}
*/
	// change the old IP record if IP changes
        if (strcmp("$ip", "$old_ip") != 0) 
        {
	    	$str_sql = "UPDATE $db_tablename_ip set username='', ip_type='$default_ip_type', lease='0', clientname='', mac='', notes='' WHERE ip='$old_ip'";
    		$result = mysql_db_query($db_name, $str_sql, $id_link);
    		if (! $result) { exit (print "Failed to save to the DB!<br />\n"); }
    	}

    	$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip='$ip'";
    	$result = mysql_db_query($db_name, $str_sql, $id_link);
    	$total_rows = mysql_num_rows($result);

    	if ($isDone != 'yes' || $total_rows == 0) 
        {
            print "<h3 class=\"text-center text-danger\"><strong>Failed to update</strong> <span class=\"label label-danger\">$ip</span></h3>\n";
            print "<hr />\n";
    	} 
    
        else 
        {
            print "<h3 class=\"text-center text-success\"><strong>Successfully updated</strong> <span class=\"label label-success\">$ip</span></h3>\n";
            print "<hr />\n";
            mark_update("localhost");
        }

    }

?>

<?

    /*  
     * Create the update form
     */


    $str_sql = "SELECT * FROM $db_tablename_ip WHERE ip='$ip'";
    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
	    print "Failed to submit!<br>\n";
    	include "$footer";
    	exit;
    }

    $total_rows = mysql_num_rows($result);

    if ($total_rows == 0){
	    print "<h4 class=\"text-center text-danger\">Error: Not a valid IP on this subnet!</h4><br>\n";
        print "<fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";
        print "<a href=\"\" class=\"btn btn-primary\" onclick=\"window.top.window.$.fancybox.close();\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Closing window...\">Close Window</a></fieldset>\n";
    	exit;
    }

    print "<div class=\"container-fluid\"><div class=\"row\">\n";
    print "<div class=\"col-md-12\" id=\"modifyIPBody\">\n";
    print "<form data-async method=\"post\" action=\"modify_ip.php\"  class=\"form-horizontal modify\" role=\"form\" id=\"modify\">\n";
    print "<input type=\"hidden\" name=\"action\" value=\"modify_ip\">\n";

    $beginForm = "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">";
    $row = mysql_fetch_object($result);
    $ip = $row->ip;
    $ip_type = $row->ip_type;
    $user = $row->username;
    $clientname = $row->clientname;
    $mac = $row->mac;
    $lease = $row->lease;
    $notes = $row->notes;

    if (strcmp($ip_type, "dynamic") == 0){

	    // only get the MAC from the "dynamic" table and pre-populate
    	// the MAC field when the form is not yet submitted.
    	if (strcmp($action, "modify_ip") != 0){

	    	$str_sql = "SELECT * FROM $db_tablename_dynamic WHERE ip = '$ip'";
    		$result = mysql_db_query($db_name, $str_sql, $id_link);

	    	while ($row = mysql_fetch_object($result)) {

			// $user contains computer name in table 'dynamic' 
			$user = $row->clientname;
			$mac = $row->mac;

		    }

        }

    }

    print "<input type=hidden name=isDone value=\"yes\">\n";	
    print "<input type=hidden name=old_username value=\"$user\">\n";
    print "<input type=hidden name=old_ip_type value=\"$ip_type\">\n";
    print "<input type=hidden name=old_lease value=\"$lease\">\n";
    print "<input type=hidden name=old_clientname value=\"$clientname\">\n";
    print "<input type=hidden name=old_mac value=\"$mac\">\n";	
    print "<input type=hidden name=old_notes value=\"$notes\">\n";	
    print "<input type=hidden name=username value=\"$username\">\n";
    print "<input type=hidden name=token value=\"$token\">\n";
    print "<input type=hidden name=old_ip value=\"$ip\">\n";

    if ($switch_ip == 1) 
    {
	    print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">IP Address</label><div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"ip\" value=\"$ip\" /></div></div></fieldset>\n";
    }

    else 
    {
	    print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">IP Address</label><div class=\"col-xs-8\"><div class=\"input-group\"><input type=\"text\" class=\"form-control\" name=\"ip\" placeholder=\"$ip\" readonly/>";
    	print "<input type=hidden name=ip value=$ip>\n";
	    if ((strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN))
        {
		    print "<span class=\"input-group-btn\"><a data-url=\"modify_ip.php?q=mini&ip=$ip&username=$username&token=$token&switch_ip=1\" class=\"btn btn-primary ajax\" role=\"button\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Unlocking...\">Edit</a></span>\n";
    	} 
        else 
        {
		    print "<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-saved\"></span></span>\n";
	    }

	    print "</div></div></div></fieldset>\n";
    }

    if ((strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN)) 
    {
	    print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-xs-3 form-control-label\">Machine Name</label><div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"user\" value=\"$user\" /></div></div></fieldset>\n";
    }

    else 
    {
    	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-xs-3 form-control-label\">Machine Name</label><div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"user\" placeholder=\"$user\" readonly/></div></div></fieldset>";
    }

    print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">Type of Reservation</label>\n";

    if ((strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN))
    {
	    print "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"ip_type\">\n";
    	$tmp = "\$ip_type_$ip_type = SELECTED;";
    	eval("$tmp");
    	include "includes/ip_type.inc.php";
    	print "</select></div></div></fieldset>\n";
    }

    else { print "<div class=\"col-xs-8\"><input class=\"form-control\" name=\"ip_type\" placeholder=\"$ip_type\" readonly></div></div></fieldset>\n"; }

    print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">Lease Time</label>\n";

    if ((strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN)) 
    {
	    print "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"lease\">\n";
    	$tmp = "\$lease_$lease = SELECTED;";
    	eval("$tmp");
    	include "includes/lease.inc.php";
    	print "</select></div></div></fieldset>\n";
    }
    else{
	print "<div class=\"col-xs-8\"><input class=\"form-control\" name=\"lease\" placeholder=\"$lease2name[$lease]\" readonly></div></div></fieldset>\n";
}

print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">Client Name</label>\n";

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	print "<div class=\"col-xs-8\"><input class=\"form-control\" type=text name=clientname value=\"$clientname\"></div></div></fieldset>\n";
}

else{
	print "<div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"clientname\" placeholder=\"$clientname\" readonly></div></div></fieldset>\n";
}

print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">MAC Address</label>\n";

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	print "<div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"mac\" value=\"$mac\" />";
	print "</div></div></fieldset>\n";
}

else{
	print "<div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"mac\" placeholder=\"$mac\" readonly></div></div></fieldset>\n";
}

print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">Notes</label>\n";

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	$date = date("Y-m-d");
	$date = $date . ' at ' . date("H:i");
	$notechop = array_shift(explode('   Last edit on', $notes));
	print "<div class=\"col-xs-8\"><textarea class=\"form-control\" name=notes rows=4>$notechop \n\nLast edit on $date - $username</textarea></div></div></fieldset>\n";
}

else{
	print "<div class=\"col-xs-8\"><textarea class=\"form-control\" name=\"notes\" placeholder=\"$notes\" rows=4 readonly></textarea></div></div></fieldset>\n";
}

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	print "<fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";
	print "<button name=\"modify\" type=\"submit\" class=\"btn btn-primary\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Saving...\">Modify IP Settings</button></div></fieldset></form>\n";
}

if (strcmp($action, "modify_ip") == 0){

	print "<fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";
	print "<a href=\"\" class=\"btn btn-primary\" onclick=\"window.top.window.$.fancybox.close();\" ata-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Closing window...\">Close Window</a></fieldset>\n";
}


?>

<script>
$(document).ready(function() {
  $('#modify').formValidation({
    framework: 'bootstrap',
    icon: {
      valid: 'glyphicon glyphicon-ok',
      invalid: 'glyphicon glyphicon-remove',
      validating: 'glyphicon glyphicon-refresh'
    },
    fields: {
      ip: {
        validators: {
          notEmpty: {
            message: 'The IP address is required'
          },
          ip: {
            message: 'Please enter a valid IP address'
          }
        }
      },
      mac: {
        enabled: false,
        validators: {
          notEmpty: {
            message: 'The MAC address is required'
          },
          mac: {
            message: 'Please enter a valid MAC Address'
          }
        }
      }
    }
  });
jQuery(function() {
    $('form[data-async]').on('submit', function(event) {
        event.preventDefault()
        var $form = $(this);

    if ( $(this).data('requestRunning') ) {
        return;
    }

    $(this).data('requestRunning', true);

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),

            success: function(data, status) {
          $(".bootbox-body").html(data);
    },
        complete: function() {
            $(this).data('requestRunning', false);
        }
        });

        event.preventDefault();
    });
});
});

$('.btn').on('click', function() {
    var $this = $(this);
  $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
   }, 1000);
});

$(document).ready(function() {
    jQuery(function() {
        $('.ajax').on('click', function(event) {
        event.preventDefault()
         if ( $(this).data('requestRunning') ) { return; }

        $(this).data('requestRunning', true);
        $.ajax({
            type: 'GET',
            url: $(this).attr('data-url'),
            data: $(this).serialize(),
            success: function(data, status) 
            {
                $('.bootbox-body').html(data);
            },
            complete: function() 
            {
                $(this).data('requestRunning', false);
            }
        });

        event.preventDefault();
        });
    });
});

</script>

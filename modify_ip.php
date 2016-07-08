<?
include "includes/authenticate.inc.php";
include "includes/config.inc.php";
$access_level = access_level($username);
$who = $username;
$management_of = "ip";
?>
<?php include "includes/header.inc.php"; ?>
<script type="text/javascript">
    $('.btn').on('click', function() {
        var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
            $this.button('reset');
        }, 1000);
    });

    $(document).ready(function() {
        $(".modify").submit(function() {
            $.ajax({
                data: $(this).serialize(), // get the form data
                type: $(this).attr('method'), // GET or POST
                url: $(this).attr('action'), // the file to call
                success: function(response) { // on success..
                    $.fancybox({
                        content: response,
                        width: 800,
                        height: 650,
                        fitToView: true,
                        autoSize: false,
                        autoDimensions: false,
                        type: 'ajax',
                        autoSize: false,
                        closeClick: false,
                        openEffect: 'none',
                        closeEffect: 'none',
                        closeBtn: false,
                    'afterClose': function () { parent.location.reload(true)},
                        helpers: {
                            overlay: {
                                css: {
                                    'background': 'rgba(255, 255, 255, 0)'
                                }
                            }
                        }
                    });
                },
            });
            return false; // stop default submit event propagation
        });
	$(".fancybox").fancybox({
                        width: 800,
                        height: 650,
                        fitToView: true,
                        autoSize: false,
                        autoDimensions: false,
                        autoSize: false,
                        closeClick: false,
                        openEffect: 'none',
                        closeEffect: 'none',
                        closeBtn: false,
                    'afterClose': function () { parent.location.reload(true)},
                        helpers: {
                            overlay: {
                                css: {
                                    'background': 'rgba(255, 255, 255, 0)'
                                }
                            }
                        }
                    });
            return false; // stop default submit event propagation
    });
</script>
<div class="container-fluid">
<div class="row">
</div>
<? 

include "includes/config.inc.php";
include "includes/lease2name.inc.php";

$id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

if (strcmp($action, "modify_ip") == 0){

	// make sure it's an administrator
	include "includes/admin_check.inc.php";

	// check if the IP address is static/reserved, or is in use.
	// only check check when $ip is different from $old_ip
	if (strcmp($ip, $old_ip) != 0){
		ip_in_use($ip);
	}

	// check mac for right format ONLY if ip_type is 'reserved'
	if (strcmp($ip_type, "reserved") == 0){
		check_mac_format($mac);
	}

	// check computername for right format ONLY if ip_type is 'reserved' or 'static'.  Also, make sure computername & mac are NOT empty

	if ( (strcmp($ip_type, "reserved") == 0) || (strcmp($ip_type, "static") == 0) ){
		check_computername_format($user);

		if (! $user || ! $mac){
			print "<center><font color=ff0000>\n";
			print "<b>Computer Name and MAC are required for Reserved and Static IPs!</b>\n";
			print "</font></center>\n";
			include "$footer";
			exit;
		}			
	}	

	// replace \n or \r with space. otherwise, some comments will become invalid statements in /etc/dhcpd.conf
	$notes = ereg_replace("[\n\r]", " ", $notes);

	// update the ip record
	if (strcmp($ip_type, "dynamic") == 0){
		$str_sql = "UPDATE $db_tablename_ip set username='', ip_type='$ip_type', lease='0', clientname='', mac='', notes='' WHERE ip='$ip'";
	}

	else{
		$str_sql = "UPDATE $db_tablename_ip set username='$user', ip_type='$ip_type', lease='$lease', clientname='$clientname', mac='$mac', notes='$notes', lastUpdated='$username' WHERE ip='$ip'";
	}

	$result = mysql_db_query($db_name, $str_sql, $id_link);

	if (! $result){
		print "Failed to submit!<br>\n";
		include "$footer";
		exit;
	}

	$datetime = date("Y-m-d H:i:s");
	$ip_from = $REMOTE_ADDR;

	if (strcmp("$user", "$old_username") != 0){
		$changes .= "Computer Name: $old_username => $user. ";
		$changes .= "<br>\n";
	}

	if (strcmp("$ip_type", "$old_ip_type") != 0){
		$changes .= "Type: $old_ip_type => $ip_type. ";
		$changes .= "<br>\n";
	}

	if (strcmp("$lease", "$old_lease") != 0){
		$changes .= "Lease: $old_lease => $lease. ";
		$changes .= "<br>\n";
	}

	if (strcmp("$clientname", "$old_clientname") != 0){
		$changes .= "Client Name: $old_clientname => $clientname. ";
		$changes .= "<br>\n";
	}

	if (strcmp("$mac", "$old_mac") != 0){
		$changes .= "MAC: $old_mac => $mac. ";
		$changes .= "<br>\n";
	}

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
			print "Failed to submit log!<br>\n";
			include "$footer";
			exit;
		}

	}

	// change the old IP record if IP changes
	if (strcmp("$ip", "$old_ip") != 0){

		$str_sql = "UPDATE $db_tablename_ip set username='', ip_type='$default_ip_type', lease='0', clientname='', mac='', notes='' WHERE ip='$old_ip'";

		$result = mysql_db_query($db_name, $str_sql, $id_link);

		if (! $result){
			print "Failed to submit!<br>\n";
			include "$footer";
			exit;
		}

	}

        print "<h3 class=\"text-center text-success\"><strong>Successfully updated</strong> <span class=\"label label-success\">$ip</span></h3>\n";
	print "<hr />\n";
	mark_update("localhost");

}

// Here, the form as NOT been submitted yet

$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip='$ip'";

$result = mysql_db_query($db_name, $str_sql, $id_link);

if (! $result){
	print "Failed to submit!<br>\n";
	include "$footer";
	exit;
}

$total_rows = mysql_num_rows($result);

if ($total_rows == 0){
	print "<center>\n";
	print "<font color=ff0000>\n";
	print "<b>Not a valid IP on this subnet!</b><br>\n";
	print "</font>\n";
	print "</center>\n";
	include "$footer";
	exit;
}

if ($isDone != 'yes') {
	print "<h3 class=\"text-center\">Editing configuration for <span class=\"label label-default\">$ip</span></h3>\n";
	print "<hr />\n";
} 
print "<div class=\"container-fluid\"><div class=\"row\">\n";
print "<div class=\"col-md-12\">\n";
print "<form method=\"post\" action=\"modify_ip.php\" target=\"modify\" method=\"post\" class=\"form-horizontal modify\" role=\"form\" id=\"modify\">\n";
print "<input type=\"hidden\" name=\"action\" value=\"modify_ip\">\n";

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

		while ($row = mysql_fetch_object($result)){

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

if ($switch_ip == 1){
	print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">IP Address</label><div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"ip\" value=\"$ip\" /></div></div></fieldset>\n";
}

else {
	print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">IP Address</label><div class=\"col-xs-8\"><div class=\"input-group\"><input type=\"text\" class=\"form-control\" name=\"ip\" placeholder=\"$ip\" disabled />";
	print "<input type=hidden name=ip value=$ip>\n";
	if (strcmp($action, "modify_ip") != 0){
		print "<span class=\"input-group-btn\"><a href=\"modify_ip.php?ip=$ip&username=$username&token=$token&switch_ip=1\" class=\"btn btn-primary fancybox fancybox.ajax\" role=\"submit\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Unlocking...\">Edit</a></span>\n";
	} else {
		print "<span class=\"input-group-addon\"><span class=\"glyphicon glyphicon-saved\"></span></span>\n";
	}

	print "</div></div></div></fieldset>\n";
}

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-xs-3 form-control-label\">Machine Name</label><div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"user\" value=\"$user\" /></div></div></fieldset>\n";
}

else{
	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-xs-3 form-control-label\">Machine Name</label><div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"user\" placeholder=\"$user\" readonly/></div></div></fieldset>";
}

print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">Type of Reservation</label>\n";

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	print "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"ip_type\">\n";
	$tmp = "\$ip_type_$ip_type = SELECTED;";
	eval("$tmp");
	include "ip_type.inc.php";
	print "</select></div></div></fieldset>\n";
}

else{
	print "<div class=\"col-xs-8\"><input class=\"form-control\" name=\"ip_type\" placeholder=\"$ip_type\" readonly></div></div></fieldset>\n";
}

print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-xs-3 form-control-label\">Lease Time</label>\n";

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	print "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"lease\">\n";

	$tmp = "\$lease_$lease = SELECTED;";
	eval("$tmp");
	include "lease.inc.php";
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
	print "<div class=\"col-xs-8\"><textarea class=\"form-control\" name=notes rows=4>$notes</textarea></div></div></fieldset>\n";
}

else{
	print "<div class=\"col-xs-8\"><textarea class=\"form-control\" name=\"notes\" placeholder=\"$notes\" rows=4 readonly></textarea></div></div></fieldset>\n";
}

if ( (strcmp($action, "modify_ip") != 0) && ($access_level == $ADMIN) ){
	print "<fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";
	print "<button name=\"modify\" id=\"load\" target=\"_parent\" type=\"submit\" class=\"btn btn-primary\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Saving...\">Modify IP Settings</button></div></fieldset></form>\n";
}

if (strcmp($action, "modify_ip") == 0){

	print "<fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";
	print "<a href=\"\" class=\"btn btn-primary\" onclick=\"window.top.window.$.fancybox.close();\" ata-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Closing window...\">Close Window</a></fieldset>\n";
}


?>
<script>
$(document).ready(function() {
		$('#updateForm').formValidation({
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
});
</script>
<?
        include "includes/authenticate.inc.php";
	include "includes/config.inc.php";

        $access_level = access_level($username);
        $who = $username;
        $management_of = "subnet";
	
	$_GET['q']
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
                        height: 700,
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
    });
</script>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h2 class="text-center">DHCP Manager (Uris) - Subnet Management</h2>
    </div>
  </div>
  <hr>
<?

	include "includes/lease2name.inc.php";
	if (!$mini) { require "includes/subnets.inc.php"; print "<br>\n";}

	if (strcmp($action, "modify_subnet") == 0){

                include "admin_check.inc.php";

	        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

		if (! $subnet){
			$subnet_full = $prefix . "." . $default_subnet . ".0";
			$subnet = $default_subnet;
		}

		else if ($subnet == "192.168.190") {
			$subnet_full = $subnet . ".0";
		}
		else {
			$subnet_full = $prefix . "." . $subnet . ".0";
		}

		$notes = trim($notes);

       		$str_sql = "UPDATE $db_tablename_declaration set lease='$lease', notes='$notes', mac_auth='$mac_auth', bootp='$bootp', vlan='$vlan' WHERE subnet='$subnet_full'";


	        $result = mysql_db_query($db_name, $str_sql, $id_link);

       		if (! $result){
                	print "Failed to submit!<br>\n";
	       	        include "$footer";
        	       	exit;
	        }

                $datetime = date("Y-m-d H:i:s");
                $ip_from = $REMOTE_ADDR;

                if (strcmp("$notes", "$old_notes") != 0){
                        $changes .= "Notes: $old_notes => $notes. ";
			$changes .= "<br>\n";
                }

                if (strcmp("$lease", "$old_lease") != 0){
                        $changes .= "Lease: $old_lease => $lease. ";
			$changes .= "<br>\n";
                }

                if (strcmp("$mac_auth", "$old_mac_auth") != 0){

			include "no2macauth.inc.php";
			$old_mac_auth = $no2macauth["$old_mac_auth"];
			$mac_auth = $no2macauth["$mac_auth"];

                        $changes .= "MAC Auth: $old_mac_auth => $mac_auth. ";
			$changes .= "<br>\n";
                }

                if (strcmp("$bootp", "$old_bootp") != 0){

			include "no2bootp.inc.php";
			$old_bootp = $no2bootp["$old_bootp"];
			$bootp = $no2bootp["$bootp"];

                        $changes .= "BOOTP: $old_bootp => $bootp. ";
			$changes .= "<br>\n";
                }

                if ($changes){
			$changes = "<b>Subnet: $prefix.$subnet.0</b><br>\n" . $changes;
                        $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'subnet', '$changes', '$datetime')";


			$result = mysql_db_query($db_name, $str_sql, $id_link);

			if (! $result){
				print "Failed to submit log!<br>\n";
                        		include "$footer";
                                	exit;
                	}

                }

		print "<center><font color=ff0000>\n";
		print "<b>Changes have been applied to the Subnet Options.</b>\n";
                print "<br><b><i><small>(Will Take Effect In About 1 Minute)</small></i></b>\n";
		print "</font></center>\n";
		mark_update("localhost");

	}

	if (! $subnet){
		$subnet_full = $prefix . "." . $default_subnet . ".0";
		$subnet = $default_subnet;
	}

	else{
		if($subnet == "192.168.190") {
			$subnet_full = $subnet . ".0";
		} else {
			$subnet_full = $prefix . "." . $subnet . ".0";
		}
	}
	
        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
       	$str_sql = "SELECT * FROM $db_tablename_declaration WHERE subnet='$subnet_full'";

        $result = mysql_db_query($db_name, $str_sql, $id_link);

       	if (! $result){
                print "Failed to submit!<br>\n";
       	        include "$footer";
               	exit;
        }

	$row = mysql_fetch_object($result);
	$mask = $row->mask;
	$authoritative = $row->authoritative;
	$mac_auth = $row->mac_auth;
	$bootp = $row->bootp;
	$router = $row->router;
	$broadcast = $row->broadcast;
	$lease = $row->lease;
	$vlan = $row->vlan;
	$notes = $row->notes;
	$notes = trim($notes);
	print "<div class=\"container-fluid\"><div class=\"row\">\n";
	print "<div class=\"col-md-12\">\n";
	print "<form method=\"post\" action=\"modify_subnet.php?q=mini\" target=\"modify\" method=\"post\" class=\"form-horizontal modify\" role=\"form\" id=\"modify\">\n";
	print "<input type=hidden name=action value=modify_subnet>\n";
	print "<input type=hidden name=subnet value='$subnet'>\n";
	print "<input type=hidden name=old_notes value='$notes'>\n";
	print "<input type=hidden name=old_lease value='$lease'>\n";
	print "<input type=hidden name=old_mac_auth value='$mac_auth'>\n";
	print "<input type=hidden name=old_bootp value='$bootp'>\n";
	print "<input type=hidden name=username value='$username'>\n";
	print "<input type=hidden name=token value='$token'>\n";
	
	print "<div id=\"row\"><div class=\"col-md-6 col-md-offset-3\">\n";
	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">IP Address</label>\n";
	print "<div class=\"col-md-4\"><div class=\"input-group\"><p \"form-control-static\" />$subnet_full</p></div></div></div></fieldset>\n";

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Notes</label>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
	        print "<div class=\"col-md-4\"><div class=\"input-group\"><textarea class=\"form-control\" name=notes rows=4>$notes</textarea></div></div></div></fieldset>\n";
	}

	else { print "<div class=\"col-md-5\"><div class=\"input-group\"><p class=\"form-control-static\" />$notes</p></div></div></div></fieldset>\n"; }

	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-1 form-control-label\">Netmask</label>\n";
        print "<div class=\"col-xs-8\"><div class=\"input-group\"><p class=\"form-control-static\" />$mask</p></div></div></div></fieldset>\n";

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-1 form-control-label\">Gateway</label>\n";
        print "<div class=\"col-xs-8\"><div class=\"input-group\"><p class=\"form-control-static\" />$router</p></div></div></div></fieldset>\n";

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-1 form-control-label\">Broadcast</label>\n";
        print "<div class=\"col-xs-8\"><div class=\"input-group\"><p class=\"form-control-static\" />$broadcast</p></div></div></div></fieldset>\n";

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-1 form-control-label\">Authoritative</label>\n";
        print "<div class=\"col-xs-8\"><div class=\"input-group\"><p class=\"form-control-static\" />";

	if ($authoritative == 1) {print "Yes\n";} else {print "No";}
	print "</p></div></div></div></fieldset>\n";

        print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-md-1 form-control-label\">VLAN ID</label>\n";
	if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ) {
        	print "<div class=\"col-xs-8\"><div class=\"input-group\"><input type=\"number\" class=\"form-control\" name=\"vlan\" value=\"$vlan\" /></div></div></div></fieldset>\n";
	} else {
		print "<div class=\"col-xs-8\"><div class=\"input-group\"><p \"form-control-static\" />$vlan</p></div></div></div></fieldset>\n";
	}
	
        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-1 form-control-label\">Lease</label>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
		print "<div class=\"col-xs-8\"><div class=\"input-group\"><select name=lease>\n";
		$tmp = "\$lease_$lease = SELECTED;";
		eval("$tmp");
		include "lease.inc.php";
		print "</select></div></div></div></fieldset>\n";
	}

	else{
		$lease_string = $lease2name["$lease"];
		print "<td>$lease_string&nbsp;</td></tr>\n";
	}

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-1 form-control-label\">MAC Authenticated</label>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){

		$tmp = "\$mac_auth_$mac_auth = SELECTED;";
		eval("$tmp");
		print "<div class=\"col-xs-8\"><div class=\"input-group\"><select name=mac_auth>\n";
		print "<option $mac_auth_0 value=0>No\n";
		print "<option $mac_auth_1 value=1>Yes\n";
		print "</select></div></div></div></fieldset>\n";

	}

	else{

		if ($mac_auth == 1){
			print "<div class=\"col-xs-8\"><div class=\"input-group\"><p \"form-control-static\" />ON</p></div></div></div></fieldset>\n";
		}

		else{
			print "<div class=\"col-xs-8\"><div class=\"input-group\"><p \"form-control-static\" />OFF<</p></div></div></div></fieldset>\n";
		}

	}

	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-1 form-control-label\">BOOTP</label>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){

		$tmp = "\$bootp_$bootp = SELECTED;";
		eval("$tmp");
		print "<div class=\"col-md-4\"><div class=\"input-group\"><select name=bootp>\n";
		print "<option $bootp_0 value=0>No\n";
		print "<option $bootp_1 value=1>Yes\n";
		print "</select></div></div></div></fieldset>\n";

	}

	else{

		if ($bootp == 1){
                        print "<div class=\"col-xs-8\"><div class=\"input-group\"><p \"form-control-static\" />ON</p></div></div></div></fieldset>\n";
                }

                else{
                        print "<div class=\"col-xs-8\"><div class=\"input-group\"><p \"form-control-static\" />OFF<</p></div></div></div></fieldset>\n";
                }
	}

	if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
                print "<fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";
	        print "<button name=\"modify\" id=\"load\" target=\"_parent\" type=\"submit\" class=\"btn btn-danger\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Saving...\">Modify Subnet Settings</button></fieldset>\n";
	}

	if ($mini) {
       		print "<div class=\"row\"><fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";
                print "<a href=\"\" class=\"btn btn-primary\" onclick=\"window.top.window.$.fancybox.close();\" ata-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Closing window...\">Close Window</a></fieldset>\n";
        };
	print "</form></div>\n";
	print "<br />\n";
	if (!$mini) { include "$footer"; }

?>

<?php

/**
 * Main IP Management page for DHCP Management Console
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

    $pageTitle = "Modify $subnet Subnet";


    /*
     * initialize the includes for functions and generate the header
     * use this in all front-end pages to ensure uniformity
     */        
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
</script>
<? if (!$mini): ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h3 class="text-center">DHCP Manager (Uris)<br /><small class="text-muted">Subnet Management</small></h3>
    </div>
  </div>
  <hr>

<? include "includes/lease2name.inc.php"; ?>
<? include "includes/subnets.inc.php"; ?>
<br />
<? endif; ?>

<?


	if (strcmp($action, "modify_subnet") == 0){

                include "admin_check.inc.php";

	        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

		if (! $subnet){
			$subnet_full = $prefix . "." . $default_subnet . ".0";
			$subnet = $default_subnet;
		}

		else if ($subnet == "192.168.190" || $subnet == "10.252.0" || $subnet == "172.18.8" || $subnet == "172.18.0" || $subnet == "10.223.32" || $subnet == "172.18.4" || $subnet == "192.168.13" || $subnet == "10.30.30") {
			$subnet_full = $subnet . ".0";
		}
		else {
			$subnet_full = $prefix . "." . $subnet . ".0";
		}

		$notes = trim($notes);

       		$str_sql = "UPDATE $db_tablename_declaration set lease='$lease', notes='$notes', mac_auth='$mac_auth', bootp='$bootp', vlan='$vlan', enabled='$enabled', odns_1='$odns_1' WHERE subnet='$subnet_full'";


	        $result = mysql_db_query($db_name, $str_sql, $id_link);

       		if (! $result){
                	exit(print "Failed to submit!<br>\n");
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

                print "<h4 class=\"text-center text-success\"><strong>Successfully updated subnet:</strong> <span class=\"label label-success\">$subnet_full</span></h4>\n";
                print "<hr />\n";
		mark_update("localhost");
		print "<div class=\"row\"><fieldset class=\"form-group\"><div class=\"form-group text-center\">\n";  
		print "<a href=\"\" class=\"btn btn-primary\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Closing window...\">Close Window</a></fieldset></div>\n"; 
		exit;

	}

	if (! $subnet){
		$subnet_full = $prefix . "." . $default_subnet . ".0";
		$subnet = $default_subnet;
	}

	else{
                 if ($subnet == "192.168.190" || $subnet == "10.252.0" || $subnet == "172.18.8" || $subnet == "172.18.0" || $subnet == "10.223.32" || $subnet =="172.18.4" || $subnet == "192.168.13" || $subnet == "10.30.30") {
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
	$pxe = $row->pxe;
	$boot_ip = $row->boot_ip;
	$enabled = $row->enabled;
	$override_dns = $row->override_dns;
	$odns_1 = $row->odns_1;
	$odns_2 = $row->odns_2;
	$notes = $row->notes;
	$notes = trim($notes);

    /* Calculate the CIDR format of the netmask */
        $bits = 0;
        $netmask = explode(".", $mask);
                foreach($netmask as $octect)
                $bits += strlen(str_replace("0", "", decbin($octect)));
        $cidr = $bits;

	if ((strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN)) { $disabled = "";}
        else { $disabled = "disabled /"; }

	if ((strcmp($action, "modify_subnet") != 0) && !$mini) {
	print "<div class=\"col-xs-8\" style=\"float: none; margin: 0 auto;\">\n";
	} elseif ($mini) {
	print "<div class=\"col-md-12\" style=\"float: none; margin: 0 auto;\">\n";
	} else {
        print "<div class=\"col-xs-10\" style=\"float: none; margin: 0 auto;\">\n";
	}
	print "<form data-async method=\"post\" action=\"modify_subnet.php?q=mini\" method=\"post\" class=\"form-horizontal modify\" role=\"form\" id=\"modify\">\n";
	print "<input type=hidden name=action value=modify_subnet>\n";
	print "<input type=hidden name=subnet value='$subnet'>\n";
	print "<input type=hidden name=old_notes value='$notes'>\n";
	print "<input type=hidden name=old_lease value='$lease'>\n";
	print "<input type=hidden name=old_mac_auth value='$mac_auth'>\n";
	print "<input type=hidden name=old_bootp value='$bootp'>\n";
	print "<input type=hidden name=username value='$username'>\n";
	print "<input type=hidden name=token value='$token'>\n";

	if (!$mini) {
                print "<div class=\"panel panel-default\">\n";
		print "<div class=\"panel-heading\">";
		print "<h4 class=\"panel-title strong\">$subnet_full Overview</h4>";
		print "</div>\n";
	        print "<div class=\"panel-body\" style=\"margin: 20px;\">\n";
	}
	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Subnet </label>\n";
        print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" name=\"subnet\" value=\"$subnet_full/$cidr\" disabled /></div></div></fieldset>\n";

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Notes</label>\n";
        print "<div class=\"col-md-10\"><textarea class=\"form-control form-control-sm\" name=notes rows=4 value=\"$notes\" $disabled>$notes</textarea></div></div></fieldset>\n";

	if (!$mini) {
		print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Netmask</label>\n";
		print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"$mask\" disabled /></div></div></fieldset>\n";
	        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Gateway</label>\n";
		print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"$router\" disabled /></div></div></fieldset>\n";
	        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Broadcast</label>\n";
		print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"$broadcast\" disabled /></div></div></fieldset>\n";
	        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Authoritative</label>\n";
	        print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"";

		if ($authoritative == 1) { print "Yes\n";} else {print "No"; }
			print "\" disabled></p></div></div></fieldset>\n";
	}

	print "<fieldset class=\"form-group\"><div class=\"form-group\"><label class=\"col-md-2 form-control-label\">VLAN ID</label>\n";
	print "<div class=\"col-md-10\"><input type=\"number\" class=\"form-control\" name=\"vlan\" value=\"$vlan\" $disabled ></div></div></fieldset>\n";
	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Lease</label>\n";

	if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
		print "<div class=\"col-md-10\"><select class=\"form-control\" name=lease>\n";
		$tmp = "\$lease_$lease = SELECTED;";
		eval("$tmp");
		include "includes/lease.inc.php";
		print "</select></div></div></fieldset>\n";
	}

	else {
		$lease_string = $lease2name["$lease"];
	        print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"$lease_string\" disabled /></div></div></fieldset>\n";
	}

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">MAC Authenticated</label>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){

		$tmp = "\$mac_auth_$mac_auth = SELECTED;";
		eval("$tmp");
		print "<div class=\"col-md-10\"><select class=\"form-control\" name=mac_auth>\n";
		print "<option $mac_auth_0 value=0>No\n";
		print "<option $mac_auth_1 value=1>Yes\n";
		print "</select></div></div></fieldset>\n";

	}

	else {
		 print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"";
		if ($mac_auth == 1) { print "Yes\n";} else {print "No"; }
	                print "\" disabled /></div></div></fieldset>\n";
		}

	if (!$mini) {
		print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">BOOTP</label>\n";

	        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){

			$tmp = "\$bootp_$bootp = SELECTED;";
			eval("$tmp");
			print "<div class=\"col-md-10\"><select class=\"form-control\" name=bootp>\n";
			print "<option $bootp_0 value=0>No\n";
			print "<option $bootp_1 value=1>Yes\n";
			print "</select></div></div></fieldset>\n";

		}

		else {
			print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"";
			if ($bootp == 1) { print "Yes\n";} else {print "No"; }
			print "\" disabled /></div></div></fieldset>\n";
		}

	}

	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">DHCP Enabled</label>\n";
	        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){

        	        $tmp = "\$enabled_$enabled = SELECTED;";
	                eval("$tmp");
			print "<div class=\"col-md-10\"><select class=\"form-control\" name=enabled>\n";
			print "<option $enabled_0 value=0>No\n";
			print "<option $enabled_1 value=1>Yes\n";
			print "</select></div></div></fieldset>\n";

		}
	        else {
                
			print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"";
			if ($enabled == 1) { print "Yes\n";} else {print "No"; }
			print "\" disabled /></div></div></fieldset>\n";
		}

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Override Default DNS</label>\n";
        print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"";

        if ($override_dns == 1) {print "Yes\" disabled /";} else {print "No\" disabled /";}
        print "></p></div></div></fieldset>\n";

	if ($override_dns != 0) {
        	print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">Override DNS #1:</label>\n";
		print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" name=\"odns_1\" value=\"$odns_1\" $disabled></p></div></div></fieldset>\n";
	}

        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">PXE Boot Enabled</label>\n";
        print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"";

        if ($pxe == 1) {print "Yes\n";} else {print "No";}
        print "\" disabled></p></div></div></fieldset>\n";

	if ($pxe != 0) {
        print "<fieldset class=\"form-group\"><div class=\"form-group row\"><label class=\"col-md-2 form-control-label\">PXE Boot IP</label>\n";
        print "<div class=\"col-md-10\"><input type=\"text\" class=\"form-control\" value=\"$boot_ip\" disabled></p></div></div></fieldset>\n";
	}
	if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
                print "<div class=\"form-group text-center\">\n";
	        print "<button name=\"modify\" data-title=\"Update $subnet_full results\" type=\"submit\" class=\"btn btn-danger\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Saving...\">Modify Subnet Settings</button>\n";
	}

	if ($mini) {
                print "<a href=\"\" class=\"btn btn-primary\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i> Closing window...\">Close Window</a></fieldset>\n";
        };
	print "</div></div></div></form></div>\n";
	print "<br />\n";
	if (!$mini) { include "$footer"; }

?>


<script>
$(document).ready(function() {
    $('form[data-async]').on('submit', function(event) {
        event.preventDefault()
        var $form = $(this);
        var $target = $($form.attr('data-target'));
	var $title = $(this).attr('data-title');

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
	})

	<? if (!$mini): ?>
        .success (function(response) {
            bootbox.dialog({
                title: $title,
                message: $(response),
                show: false, // We will show it manually later
                onEscape: function() { console.log("Escape!"); },
                backdrop: true,
                callback: function() { $('.bootbox.modal').modal('hide'); }
	<? else: ?>
        .success (function() {
            bootbox.dialog({
                message: $('.bootbox.modal').modal('hide'),
		show: false,
	<? endif; ?>
            })   
            .on('shown.bs.modal', function() {
                $('#modalBody').show()
            })

            .on('hide.bs.modal', function(e) { parent.location.reload(true); })

            .modal('show');
        });
    });

$('#myModal').on('hidden.bs.modal', function () {
	parent.location.reload(true);
})

$('.btn').on('click', function() {
    var $this = $(this);
  $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
   }, 750);
});
});
</script>

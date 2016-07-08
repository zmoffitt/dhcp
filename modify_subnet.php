<?
        include "includes/authenticate.inc.php";
	include "includes/config.inc.php";

        $access_level = access_level($username);
        $who = $username;
        $management_of = "subnet";
?>

<?php include "includes/header.inc.php"; ?>

<title>DHCP Manager</title>
<center>
<h2>DHCP Manager - Subnet Declarations</h2>
</font>
</center>
<br>

<? 

	include "includes/lease2name.inc.php";

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

       		$str_sql = "UPDATE $db_tablename_declaration set lease='$lease', notes='$notes', mac_auth='$mac_auth', bootp='$bootp' WHERE subnet='$subnet_full'";

		// print "Query: *$str_sql*<br>\n";

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

//              print "Changes: *$changes*<br>\n";

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
	$notes = $row->notes;
	$notes = trim($notes);

	print "<form method=POST action=modify_subnet.php>\n";
	print "<input type=hidden name=action value=modify_subnet>\n";
	print "<input type=hidden name=subnet value='$subnet'>\n";
	print "<input type=hidden name=old_notes value='$notes'>\n";
	print "<input type=hidden name=old_lease value='$lease'>\n";
	print "<input type=hidden name=old_mac_auth value='$mac_auth'>\n";
	print "<input type=hidden name=old_bootp value='$bootp'>\n";
	print "<input type=hidden name=username value='$username'>\n";
	print "<input type=hidden name=token value='$token'>\n";
	
	print "<center>\n";
	print "<table border=4 cellpadding=4 cellspacing=4 width=50%>\n";
	print "<tr><td><b>Subnet:</b></td>\n";
	print "<td>$subnet_full</td></tr>\n";

	print "<tr><td><b>Notes:</b></td>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
		print "<td><textarea name=notes rows=2 cols=30>$notes</textarea></td>\n";
	}

	else{
		print "<td>$notes&nbsp;</td></tr>\n";
	}

	print "<tr><td><b>Mask:</b></td>\n";
	print "<td>$mask&nbsp;</td></tr>\n";

	print "<tr><td><b>Router:</b></td>\n";
	print "<td>$router&nbsp;</td></tr>\n";

	print "<tr><td><b>Broadcast:</b></td>\n";
	print "<td>$broadcast&nbsp;</td></tr>\n";

	print "<tr><td><b>Authoritative:</b></td>\n";

	if ($authoritative == 1){
		print "<td>Yes</td></tr>\n";
	}

	else{
		print "<td>No</td></tr>\n";
	}

	print "<tr><td><b>Lease:</b></td>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
		print "<td><select name=lease>\n";
		$tmp = "\$lease_$lease = SELECTED;";
		eval("$tmp");
		include "lease.inc.php";
		print "</select></td></tr>\n";
	}

	else{
		$lease_string = $lease2name["$lease"];
		print "<td>$lease_string&nbsp;</td></tr>\n";
	}

	print "<tr><td><b>MAC Auth:</b></td>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){

		$tmp = "\$mac_auth_$mac_auth = SELECTED;";
		eval("$tmp");
		print "<td><select name=mac_auth>\n";
		print "<option $mac_auth_0 value=0>No\n";
		print "<option $mac_auth_1 value=1>Yes\n";
		print "</select></td></tr>\n";

	}

	else{

		if ($mac_auth == 1){
			print "<td>ON</td></tr>\n";
		}

		else{
			print "<td>OFF</td></tr>\n";
		}

	}

	print "<tr><td><b>BOOTP:</b></td>\n";

        if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){

		$tmp = "\$bootp_$bootp = SELECTED;";
		eval("$tmp");
		print "<td><select name=bootp>\n";
		print "<option $bootp_0 value=0>No\n";
		print "<option $bootp_1 value=1>Yes\n";
		print "</select></td></tr>\n";

	}

	else{

		if ($bootp == 1){
			print "<td>ON</td></tr>\n";
		}

		else{
			print "<td>OFF</td></tr>\n";
		}

	}

	if ( (strcmp($action, "modify_subnet") != 0) && ($access_level == $ADMIN) ){
		print "<tr><td colspan=2 align=center><input type=submit value=\"Modify Options\"></td></tr>\n";
	}

	print "</table>\n";
	print "</center>\n";
	print "</form>\n";

	include "includes/subnets.inc.php";
	include "$footer";

?>

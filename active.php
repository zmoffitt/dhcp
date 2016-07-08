<?
	include "functions.inc.php";
	include "config.inc.php";
?>

<title>DHCP Manager</title>
<center>
<font color=0000ff>
<h1>DHCP Manager - IP Management</h1>
</font>

</center>
<br>

<? 

	include "lease2name.inc.php";

        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
	$str_sql = "SELECT * FROM $db_tablename_dynamic WHERE ip = '$ip'";
        $result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_rows = mysql_num_rows($result);

        while ($row = mysql_fetch_object($result)){
		$computername = $row->computername;
		$mac = $row->mac;
		$start = $row->start;
		$end = $row->end;
	}

	print "<center>\n";

	$time_string = date("Y-m-d H:i:s");
	print "<b><font color=ff0000>Current Time: <u>$time_string</u></font></b><br><br>\n";

	print "<table border=4 cellpadding=2 cellspacing=2 width=60%>\n";
	print "<tr><td><b>IP:</b></td><td>$ip&nbsp;</td></tr>\n";

	// pull data from 'ip' table as well
	$str_sql2 = "SELECT * FROM $db_tablename_ip WHERE ip = '$ip'";
        $result2 = mysql_db_query($db_name, $str_sql2, $id_link);

	// print "Query 1: *$str_sql1*<br>\n";
	// print "Query 2: *$str_sql2*<br>\n";

        while ($row2 = mysql_fetch_object($result2)){

		// if computername field in table 'dynamic' is empty, 
		// use the username field in table 'ip'

		if (! $computername){
			$computername = $row2->username;
		}

		if (! $mac){
			$mac = $row2->mac;
		}

		if (! $ip_type){
			$ip_type = $row2->ip_type;
		}

	}

	print "<tr><td><b>IP Type:</b></td><td>" . ucfirst($ip_type) . "&nbsp;";

	if (strcmp($ip_type, "dynamic") == 0){
		print "<b>(No Reservation)</b>";
	}
	
	print "</td></tr>\n";

	if (! $computername){
		$computername = "N/A";
	}

	print "<tr><td><b>Computer Name:</b></td><td>\n";
	if ($name_lookup == 1){
		print "<a target=lookup href=$name_lookup_url?fullname=$computername>$computername&nbsp;</a>\n";
	}

	else{
		print "$computername&nbsp;\n";
	}
	print "</td></tr>\n";

	if (! $mac){
		$mac = "N/A";
	}

	print "<tr><td><b>MAC:</b></td><td>$mac&nbsp;</td></tr>\n";

	$start_est = utc2est($start);
	$end_est = utc2est($end);

	// non-dynamic IPs do NOT have entries in the "dynamic" table.
	if ($total_rows <= 0){
		$start_est = "N/A";
		$end_est = "N/A";
	}

	print "<tr><td><b>Lease Starts:</b></td><td>$start_est&nbsp;</td></tr>\n";
	print "<tr><td><b>Lease Ends:</b></td><td>$end_est&nbsp;</td></tr>\n";

	print "<tr>\n";
	print "<td align=center colspan=2>\n";

	print "<img src=green-ball.gif>\n";
	print "<a href=mac_add.php?username=$username&token=$token&username_db=$computername&mac=$mac>Register Mac</a>\n";

	print "<img src=red-ball.gif>\n";
	print "<a href=blacklist_add.php?username=$username&token=$token&username_db=$computername&mac=$mac>Blacklist Mac</a>\n";

	print "</td>\n";
	print "</tr>\n";

	print "</table>\n";
	include "$footer";

?>

<? 

$dhcp_tmpfile = "/tmp/tmp.txt";
include "includes/config.inc.php";


if (strcmp("$action", "go") == 0){

	$id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

	// dump the global options

	$str_sql = "SELECT * FROM $db_tablename_global";
	$result = mysql_db_query($db_name, $str_sql, $id_link);

	if (! $result){
		print "Failed to submit!<br>\n";
		include "$footer";
		exit;
	}

	$row = mysql_fetch_object($result);

	$tmpfile = fopen("$dhcp_tmpfile", "w");

	if (! ($tmpfile)){
		print "Temporary file could not be opened!";
		exit;
	}

	$dns_servers = $row->dns_1;
	if ($row->dns_2){
		$dns_servers .= ", ";
		$dns_servers .= $row->dns_2;
	}
	if ($row->dns_3){
		$dns_servers .= ", ";
		$dns_servers .= $row->dns_3;
	}
	if ($row->dns_4){
		$dns_servers .= ", ";
		$dns_servers .= $row->dns_4;
	}
	if ($row->dns_5){
		$dns_servers .= ", ";
		$dns_servers .= $row->dns_5;
	}

	$wins_servers = $row->wins_1;
	if ($row->wins_2){
		$wins_servers .= ", ";
		$wins_servers .= $row->wins_2;
	}

	fputs($tmpfile, "# Global Options\n\n");
	fputs($tmpfile, "option ntp-servers $row->ntp_server;\n");
        fputs($tmpfile, "option time-servers $row->ntp_server;\n");
	fputs($tmpfile, "option domain-name \"$row->domain\";\n");
	fputs($tmpfile, "option domain-search \"$row->domain\";\n");
	fputs($tmpfile, "option domain-name-servers $dns_servers;\n");
	fputs($tmpfile, "option netbios-name-servers $wins_servers;\n\n");

	// to get around the requirement of a ddns-update-style statement
	if ($ddns_update_style){
		fputs($tmpfile, "# For ddns-update-style compatibility.\n");
		fputs($tmpfile, "# Required for newer versions than 3.0b2pl11.\n");
		fputs($tmpfile, "ddns-update-style ad-hoc;\n\n");
	}

	// dump the subnet declarations
	$str_sql = "SELECT * FROM $db_tablename_declaration";
	$result = mysql_db_query($db_name, $str_sql, $id_link);

	if (! $result){
		print "Failed to submit!<br>\n";
		include "$footer";
		exit;
	}

	while ($row = mysql_fetch_object($result)){

		$subnet_pattern = ereg_replace("\.0", "", $row->subnet);
		if ($subnet_pattern == "172.18") {
			$subnet_pattern = "172.18.0";
		}
		if ($row->enabled == 0) { continue; }
		else {

		echo "subnet pattern $subnet_pattern \n";

		fputs($tmpfile, "# Subnet $row->subnet Declaration\n");
		fputs($tmpfile, "# Notes: $row->notes\n\n");
		fputs($tmpfile, "# $row->notes\n");
		fputs($tmpfile, "subnet $row->subnet netmask $row->mask {\n\n");
		if (($row->override_dns == 1) && ($row->odns_1 != null)) {
                        fputs($tmpfile, "\toption domain-name-servers $row->odns_1 $row->odns_2;\n");
                }
		if ($row->authoritative == 1){
			fputs($tmpfile, "\tauthoritative;\n");
		}

		$str_sql2 = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$subnet_pattern%' AND ip_type='dynamic' order by id";

		$result2 = mysql_db_query($db_name, $str_sql2, $id_link);

		if (! $result2){
			print "Failed to submit!<br>\n";
			include "$footer";
			exit;
		}

		$total_rows = mysql_num_rows($result2);
		$beginning = 1;
		$i = 0;
		$ip_no_last = 0;

		while ($row2 = mysql_fetch_object($result2)){

			$ip_tmp = split("\.", $row2->ip);
                        $sub_tmp = split("\.", $subnet_pattern);
			$ip_su_current = $ip_tmp[2];
                        if (($sub_tmp[2] != $ip_su_current) && ($i % 255 == '0') && ($sub_tmp[2] != '1')) { $subnet_pattern = ($ip_tmp[0].".".$ip_tmp[1].".".($ip_tmp[2])); echo "\t Matched subnet shift: ".$subnet_pattern."\n";}
			$ip_no_current = $ip_tmp[3];
			$diff = $ip_no_current - $ip_no_last;
			if(($subnet_pattern == "128.59.190") || ($subnet_pattern == "10.252.0")) {
				echo "in here $subnet_pattern.$ip_no_current\n";
			}

			if ($beginning == 1){

				echo "Evaluating: $subnet_pattern $ip_no_last \n";
				fputs($tmpfile, "\trange $subnet_pattern.$ip_no_current ");
				$continue = 1;
				$beginning = 0;
			}

			elseif ($diff == 1){
				$continue = 1;
			}

			else{
				fputs($tmpfile, "$subnet_pattern.$ip_no_last;\n");
				$subnet_pattern = ($ip_tmp[0].".".$ip_tmp[1].".".($ip_tmp[2])); 
				fputs($tmpfile, "\trange $subnet_pattern.$ip_no_current ");
				$continue = 0;
			}

			$i++;
			if ($i == $total_rows){
				fputs($tmpfile, "$subnet_pattern.$ip_no_current;\n");
			}

			$ip_no_last = $ip_no_current;

		}

		fputs($tmpfile, "\toption routers $row->router;\n");
		fputs($tmpfile, "\toption broadcast-address $row->broadcast;\n");

		if ($row->lease){

			fputs($tmpfile, "\tmin-lease-time $row->lease;\n");
			fputs($tmpfile, "\tmax-lease-time $row->lease;\n");
			fputs($tmpfile, "\tdefault-lease-time $row->lease;\n");
		}

		if ($row->mac_auth == 1){
			$mac_auth_action = "deny";
		}

		else{
			$mac_auth_action = "allow";
		}

		fputs($tmpfile, "\t$mac_auth_action unknown-clients;\n");

		if ($row->bootp == 1){
			$bootp_action = "allow";
		}

		else{
			$bootp_action = "deny";
		}
		
		if ($row->pxe == 1 && $row->boot_ip != null) {
			fputs($tmpfile, "\tnext-server $row->boot_ip;\n");
		}	

		fputs($tmpfile, "\t$bootp_action bootp;\n\n");
		fputs($tmpfile, "}\n\n");

	}
	}

	// end of subnet declarations
	fputs($tmpfile, "### End of Subnet Declarations ###\n\n");

	// dump reserved and static IPs
	fputs($tmpfile, "### Beginning of Reservations ###\n\n");

	$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip_type='reserved' OR ip_type='static'";

	$result = mysql_db_query($db_name, $str_sql, $id_link);

	if (! $result){
		print "Failed to submit!<br>\n";
		include "$footer";
		exit;
	}

	while ($row = mysql_fetch_object($result)){

		if ($row->notes){
			fputs($tmpfile, "# $row->notes\n");
		}

		// here, it's a static IP
		if (strcmp($row->ip_type, "static") == 0){

			fputs($tmpfile, "# Statically Set\n");
			$row->username = ereg_replace(" ", "_", $row->username);
			$row->username = ereg_replace("#", "", $row->username);
			if (! $row->username){
				$row->username = "Unknown";
			}

			$mac_tmp =  ereg_replace(":", "", $row->mac);

			if ($mac_tmp){
				$row->username .= "_";
				$row->username .= "$mac_tmp";
			}

			$row->username .= "_";
			$row->username .= $row->id;

			fputs($tmpfile, "host $row->username{\n");

			if ($row->mac){
				fputs($tmpfile, "#\thardware ethernet $row->mac;\n");
			}

			fputs($tmpfile, "\tfixed-address $row->ip;\n");
			fputs($tmpfile, "}\n\n");
		}

		// here, it's a reservation
		else{
			fputs($tmpfile, "# A Reservation\n");
			$row->username = ereg_replace(" ", "_", $row->username);
			$row->username = ereg_replace("#", "", $row->username);

			if (! $row->username){
				$row->username = "Unknown";
			}

			$mac_tmp = ereg_replace(":", "", $row->mac);

			$row->username .= "_";
			$row->username .= "$mac_tmp";

			$row->username .= "_";
			$row->username .= $row->id;

			fputs($tmpfile, "host $row->username{\n");

			if ($row->lease){

				fputs($tmpfile, "\tmin-lease-time $row->lease;\n");
				fputs($tmpfile, "\tmax-lease-time $row->lease;\n");
				fputs($tmpfile, "\tdefault-lease-time $row->lease;\n");
			}

			fputs($tmpfile, "\thardware ethernet $row->mac;\n");
			fputs($tmpfile, "\tfixed-address $row->ip;\n");
			fputs($tmpfile, "}\n\n");

		}

	}

	fputs($tmpfile, "### End of Reservations ###\n\n");

	// dump other registrations (NO IP)
	fputs($tmpfile, "### Beginning of Other Registrations ###\n\n");

	$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip_type='registered'";

	$result = mysql_db_query($db_name, $str_sql, $id_link);

	if (! $result){
		print "Failed to submit!<br>\n";
		include "$footer";
		exit;
	}

	while ($row = mysql_fetch_object($result)){

		$row->username = ereg_replace(" ", "_", $row->username);
		$row->username = ereg_replace("#", "", $row->username);

		$mac_tmp =  ereg_replace(":", "", $row->mac);

		$row->username .= "_";
		$row->username .= "$mac_tmp";

		$row->username .= "_";
		$row->username .= $row->id;

		fputs($tmpfile, "host $row->username{\n");

		fputs($tmpfile, "\thardware ethernet $row->mac;\n}\n\n");
	}

	fputs($tmpfile, "\n### End of Other Registrations ###\n\n");

	// dump blacklisted MACs (NO IP)
	fputs($tmpfile, "### Beginning of Blacklisted MACs ###\n\n");

	$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip_type='blacklisted'";

	$result = mysql_db_query($db_name, $str_sql, $id_link);

	if (! $result){
		print "Failed to submit!<br>\n";
		include "$footer";
		exit;
	}

	while ($row = mysql_fetch_object($result)){

		$row->username = ereg_replace(" ", "_", $row->username);
		$row->username = ereg_replace("#", "", $row->username);

		$mac_tmp =  ereg_replace(":", "", $row->mac);

		$row->username .= "_";
		$row->username .= "$mac_tmp";

		$row->username .= "_";
		$row->username .= $row->id;

		fputs($tmpfile, "host $row->username{\n");

		fputs($tmpfile, "\thardware ethernet $row->mac;\n");
		fputs($tmpfile, "\tdeny booting;\n}\n\n");

	}

	fputs($tmpfile, "\n### End of Blacklisted MACs ###\n\n");

	fputs($tmpfile, "### Beginning of MAC Database ###\n\n");
	fclose($tmpfile);

}

?>


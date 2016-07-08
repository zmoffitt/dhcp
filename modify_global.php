<?
        include "includes/authenticate.inc.php";
        include "includes/config.inc.php";
        $access_level = access_level($username);
        $who = $username;
?>

<title>DHCP Manager</title>
<center>
<font color=0000ff>
<h1>DHCP Manager - Global Options</h1>
</font>
<? include "links.inc.php"; ?>
</center>
<br>

<? 

	if (strcmp($action, "modify_global") == 0){

                include "admin_check.inc.php";

		if (! $dns_1 || ! $dns_2 || ! $wins_1){
			print "<center><font color=ff0000><b>\n";
			print "Domain name, DNS Server 1, DNS Server 2, WINS Server 1, and WINS Server 2 can NOT be empty!<br>\n";
			print "</b></font></center><br>\n";
			include "$footer";
			exit;
		}

	        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
       		$str_sql = "UPDATE $db_tablename_global set dns_1='$dns_1', dns_2='$dns_2', dns_3='$dns_3', dns_4='$dns_4', dns_5='$dns_5', wins_1='$wins_1', wins_2='$wins_2' WHERE id=1";

//		print "Query: *$str_sql*<br>\n";

	        $result = mysql_db_query($db_name, $str_sql, $id_link);

       		if (! $result){
                	print "Failed to submit!<br>\n";
	       	        include "$footer";
        	       	exit;
	        }

                $datetime = date("Y-m-d H:i:s");
                $ip_from = $REMOTE_ADDR;

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

//			print "Changes: *$changes*<br>\n";

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
	$dns_1 = $row->dns_1;
	$dns_2 = $row->dns_2;
	$dns_3 = $row->dns_3;
	$dns_4 = $row->dns_4;
	$dns_5 = $row->dns_5;
	$wins_1 = $row->wins_1;
	$wins_2 = $row->wins_2;

	print "<form method=POST action=modify_global.php>\n";
	print "<input type=hidden name=action value=modify_global>\n";
	print "<input type=hidden name=old_dns_1 value=\"$dns_1\">\n";
	print "<input type=hidden name=old_dns_2 value=\"$dns_2\">\n";
	print "<input type=hidden name=old_dns_3 value=\"$dns_3\">\n";
	print "<input type=hidden name=old_dns_4 value=\"$dns_4\">\n";
	print "<input type=hidden name=old_dns_5 value=\"$dns_5\">\n";
	print "<input type=hidden name=old_wins_1 value=\"$wins_1\">\n";
	print "<input type=hidden name=old_wins_2 value=\"$wins_2\">\n";
        print "<input type=hidden name=username value='$username'>\n";
        print "<input type=hidden name=token value='$token'>\n";

	print "<center>\n";
	print "<table border=4 cellpadding=4 cellspacing=4 width=50%>\n";
	print "<tr><td><b>Domain Name:</b></td>\n";
	print "<td>$domain</td></tr>\n";
	
	print "<tr><td><b>DNS Server 1:</b></td>\n";

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<td><input type=text name=dns_1 value=\"$dns_1\"></td></tr>\n";
	}

	else{
		print "<td>$dns_1</td></tr>\n";
	}

	print "<tr><td><b>DNS Server 2:</b></td>\n";

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<td><input type=text name=dns_2 value=\"$dns_2\"></td></tr>\n";
	}

	else{
		print "<td>$dns_2</td></tr>\n";
	}

	print "<tr><td><b>DNS Server 3:</b></td>\n";

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<td><input type=text name=dns_3 value=\"$dns_3\"></td></tr>\n";
	}

	else{
		print "<td>$dns_3</td></tr>\n";
	}

	print "<tr><td><b>DNS Server 4:</b></td>\n";

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<td><input type=text name=dns_4 value=\"$dns_4\"></td></tr>\n";
	}

	else{
		print "<td>$dns_4</td></tr>\n";
	}

	print "<tr><td><b>DNS Server 5:</b></td>\n";

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<td><input type=text name=dns_5 value=\"$dns_5\"></td></tr>\n";
	}

	else{
		print "<td>$dns_5</td></tr>\n";
	}

	print "<tr><td><b>WINS Server 1:</b></td>\n";

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<td><input type=text name=wins_1 value=\"$wins_1\"></td></tr>\n";
	}

	else{
		print "<td>$wins_1&nbsp;</td></tr>\n";
	}

	print "<tr><td><b>WINS Server 2:</b></td>\n";

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<td><input type=text name=wins_2 value=\"$wins_2\"></td></tr>\n";
	}

	else{
		print "<td>$wins_2&nbsp;</td></tr>\n";
	}

	if ( (strcmp($action, "modify_global") != 0) && ($access_level == $ADMIN) ){
		print "<tr><td colspan=2 align=center><input type=submit value=\"Modify Options\"></td></tr>\n";
	}

	print "</table>\n";
	print "</center>\n";
	print "</form>\n";
	include "$footer";

?>


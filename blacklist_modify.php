<?
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	$who = $username;
?>
	
<title>DHCP Manager</title>
<center>
<font color=0000ff>
<h1>DHCP Manager - Blacklist MAC</h1>
</font>
<? include "links.inc.php"; ?>
<br>

<? 

	$id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

	if (strcmp($action, "modify") == 0){

                // make sure it's an administrator
                include "admin_check.inc.php";

                // make sure mac is in right format
                check_mac_format($mac);

                // make sure computername only contains valid chars
                check_computername_format($username_db);

                $ip_from = $REMOTE_ADDR;

                // go through all the replication partners
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $tmp = "\$partner_$key";
                        eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                        // print "$tmp = $to_replicate<br>\n";
                        if ($to_replicate == 1){
                                mac_modify($who, $ip_from, $dhcp_partners[$key], "blacklisted", $mac, $mac_db_old, $username_db, $username_db_old, $clientname, $clientname_db_old, $notes, $notes_db_old);
				mark_update($dhcp_partners[$key]);
                        }

                }

		print "<center>\n";
		print "<font color=ff0000>\n";
		print "<h3>MAC Record Updated!</h3>\n";
                print "<b><i><small>(Will Take Effect In About 1 Minute)</small></i></b>\n";
		print "</font>\n";
		print "</center>\n";

	}
	
	else{

		$str_sql = "SELECT * FROM $db_tablename_ip WHERE username='$username_db' AND mac='$mac'";

	        $result = mysql_db_query($db_name, $str_sql, $id_link);
	       	if (! $result){
       		        print "Failed to submit!<br>\n";
        		include "$footer";
              		exit;	
       		}	

	        $row = mysql_fetch_object($result);

		print "<center>\n";
		print "<table width=35% border=4 cellpadding=2 cellspacing=2>\n";

                print "<form action=blacklist_modify.php>\n";
                print "<input type=hidden name=action value=modify>\n";
                print "<input type=hidden name=username value=$username>\n";
                print "<input type=hidden name=token value=$token>\n";
                print "<input type=hidden name=username_db_old value=$username_db>\n";
                print "<input type=hidden name=mac_db_old value=$mac>\n";
		print "<input type=hidden name=clientname_db_old value=\"$row->clientname\">\n";
		print "<input type=hidden name=notes_db_old value=\"$row->notes\">\n";

		print "<tr><td><b>Computer Name:</b></td><td><input type=text name=username_db value=\"$row->username\"</td></tr>\n";
		print "<tr><td><b>Client Name:</b></td><td><input type=text name=clientname value=\"$row->clientname\"</td></tr>\n";
		print "<tr><td><b>MAC:</b></td><td><input type=text name=mac value=\"$row->mac\"</td></tr>\n";
		print "<tr><td><b>Notes:</b></td><td><textarea name=notes cols=30 rows=4>$row->notes</textarea></td></tr>\n";

                print "<tr><td colspan=2 align=center>\n";

                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $selected = "CHECKED";

                        // Uncomment following lines to select local server
                        // only, by default.

                        // $selected = "";
                        // if (strcmp($identifier, $key) == 0){
                        //      $selected = "CHECKED";
                        // }

                        print "<input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b>\n";

                }

                print "</td></tr>\n";

		print "<tr><td colspan=2 align=center><input type=submit value=Modify></td></tr>\n";

                print "</form>\n";
		print "</td></tr>\n";
		
		print "</table>\n";
		print "</center>\n";
		print "<br>\n";

	}

	include "$footer";

?>

<?
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
        $who = $username;

	if (strcmp($selection, "Cancel") == 0){
			HEADER("Location:blacklist.php?username=$username&token=$token");
			exit;
	}

?>
	
<title>DHCP Manager</title>
<center>
<font color=0000ff>
<h1>DHCP Manager - Blacklist MAC</h1>
</font>
<? include "links.inc.php"; ?>
<br>

<? 
	if (strcmp($action, "delete") == 0){

		if (strcmp($selection, "Delete") == 0){

			include "admin_check.inc.php";

                        $ip_from = $REMOTE_ADDR;

                        // go through all the replication partners
                        for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                                $tmp = "\$partner_$key";
                                eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                                // print "$tmp = $to_replicate<br>\n";
                                if ($to_replicate == 1){
                                        mac_delete($who, $ip_from, $dhcp_partners[$key], "blacklisted", $mac, $username_db);
					mark_update($dhcp_partners[$key]);
                                }

                        }

			print "<center>\n";
			print "<font color=ff0000>\n";
			print "<h3>MAC *$mac* deleted for user *$username_db*!</h3>\n";
	                print "<b><i><small>(Will Take Effect In About 1 Minute)</small></i></b>\n";
			print "</font>\n";
			print "</center>\n";

		}

	}
	
	else{

		print "<center>\n";
		print "<font color=ff0000>\n";
		print "<h3>Are you sure you want to delete the following:</h3>\n";
		print "</font>\n";
		print "<b>Computer Name: $username_db<br>\n";
		print "Mac: $mac</b><br><br>\n";
		
		print "<table border=0>\n";
               	print "<form action=blacklist_delete.php>\n";

                print "<input type=hidden name=action value=delete>\n";
                print "<input type=hidden name=username value=$username>\n";
       	        print "<input type=hidden name=token value=$token>\n";
               	print "<input type=hidden name=username_db value=$username_db>\n";
                print "<input type=hidden name=mac value=$mac>\n";

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

                print "<td align=center>\n";
                print "<input type=submit name=selection value=Delete>\n";
                print "</td>\n";

                print "<td align=center>\n";
                print "<input type=submit name=selection value=Cancel>\n";
                print "</td></tr>\n";

                print "</form>\n";
		print "</table>\n";
		print "</center>\n";
	}

	include "$footer";

?>

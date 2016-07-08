<?
	include "includes/authenticate.inc.php";
	include "inclues/config.inc.php";
	$access_level = access_level($username);
        $who = $username;

	if (strcmp($selection, "Cancel") == 0){
			HEADER("Location:staff.php?username=$username&token=$token");
			exit;
	}

?>
	
<title>DHCP Manager</title>
<center>
<font color=0000ff>
<h1>DHCP Manager - Staff Management</h1>
</font>
<br>

<? 
	if (strcmp($action, "delete") == 0){

		if (strcmp($selection, "Delete") == 0){

			include "includes/admin_check.inc.php";

			$ip_from = $REMOTE_ADDR;

			// go through all the replication partners
	                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
	                        $tmp = "\$partner_$key";	
        	                eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                	        // print "$tmp = $to_replicate<br>\n";
                        	if ($to_replicate == 1){
                                	staff_delete($who, $ip_from, $dhcp_partners[$key], $staff, $grp);
	                        }	

        	        }

			print "<center>\n";
			print "<font color=ff0000>\n";
			print "<h3>Staff record for *$staff* deleted!</h3>\n";
	                print "<b><i><small>(Will Take Effect Immediately)</small></i></b>\n";
			print "</font>\n";
			print "</center>\n";

		}

	}
	
	else{

		print "<center>\n";
		print "<font color=ff0000>\n";
		print "<h3>Are you sure you want to delete the following:<br>(Select the server(s) you want to update)</h3>\n";
		print "</font>\n";
		print "<b>Staff Name: $staff<br>\n";
		print "Group: " . ucfirst($grp) . "</b><br><br>\n";
		
		print "<table border=0>\n";
               	print "<form action=staff_delete.php>\n";

                print "<input type=hidden name=action value=delete>\n";
                print "<input type=hidden name=username value=$username>\n";
       	        print "<input type=hidden name=token value=$token>\n";
               	print "<input type=hidden name=staff value=$staff>\n";
               	print "<input type=hidden name=grp value=$grp>\n";

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
<?php

/**
 * User add function for DHCP Management Console
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
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */

	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	$who = $username;
?>

<?php if (strcmp($action, "add") == 0):
		
		if (! $staff || ! $grp){
			print "<center><font color=ff0000>\n";
			print "<b>Staff Name and Group are required!</b>\n";
			print "</font></center>\n";
			include "$footer";
			exit;
		}

                // make sure it's an administrator
		include "admin_check.inc.php";

                // make sure staff name is in right format
                check_staff_format($staff);

                // make sure staff does not already exist in the database
                // go through all the replication partners
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
			$tmp = "\$partner_$key";
			eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
			if ($to_replicate == 1){
		        	staff_exist($dhcp_partners[$key], $key, $staff);
			}

		}	

		$ip_from = $REMOTE_ADDR;

		// go through all the replication partners
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
			$tmp = "\$partner_$key";
			eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
			// print "$tmp = $to_replicate<br>\n";
			if ($to_replicate == 1){

				staff_add($who, $ip_from, $dhcp_partners[$key], $staff, $grp);

				if (! $server_list){
					$server_list = "$key";
				}
			
				else{
					$server_list .= ", $key";
				}

			}

		}

		print "<center>\n";
		print "<font color=ff0000>\n";
		print "<b>Staff *$staff* added on: $server_list</b><br>\n";
                print "<b><i><small>(Will Take Effect Immediately)</small></i></b>\n";
		print "</font>\n";
		print "</center><br>\n";

                // reset variables
                $staff = "";
		$grp = "";		

?>

<? elseif (strcmp($action, "add") != 0): ?>
<center>

<h1>Showing legacy form:</h1>
<table border=2 cellspacing=2 cellpadding=2 width=50%>

<form METHOD=POST action=staff_add.php>
<input type=hidden name=action value=add>
<input type=hidden name=username value=<? echo "$username"; ?>>
<input type=hidden name=token value=<? echo "$token"; ?>>

<tr>
<td bgcolor=dddddd align=center colspan=2>
<font color=ff0000><b>Add a Staff</b></font>
</td>
</tr>

<tr>
<td><b>Staff Name:</b></td>
<td><input type=text name=staff></td>
</tr>

<td><b>Group:</b></td>
<td>
<select name=grp>
<option value="">[ -- Select One -- ]
<option value=support>Support
<option value=systems>Systems
</select>
</td>

</tr>

<?
	if ($dhcp_replicate == 1){

		print "<tr>\n";
		print "<td><b>Update On:</font></b></td>";
		print "<td>\n";

		for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
			$selected = "CHECKED";
		
			// Uncomment following lines to select local server 
			// only, by default.
	
			// $selected = "";
			// if (strcmp($identifier, $key) == 0){
			// 	$selected = "CHECKED";
			// }

			print "<input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b>\n";
		}

		print "</td></tr>\n";

	}

?>

<tr><td align=center colspan=2>
<input type=submit name=selection value="Add Staff">
</td></tr>

</form>
</tr>
</table>
</center>
<? endif; ?>
</body>

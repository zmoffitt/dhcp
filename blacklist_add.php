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

    if (empty($mac)) { $pageTitle = "Add new MAC to blacklist"; }
    else { $pageTitle = "Add $mac to Blacklist";} 
        
    /*  
     * initialize the includes for functions and generate the header
     * use this in all front-end pages to ensure uniformity
     */ 
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	$who = $username;


    /* Use the body include to centralize formatting */
    include "includes/body.inc.php";

	
	if (strcmp($action, "add") == 0){

		if (! $username_db || ! $mac){
			print "<center><font color=ff0000>\n";
			print "<b>Computer Name and MAC are required!</b>\n";
			print "</font></center>\n";
			include "$footer";
			exit;
		}

		// make sure it's an administrator
		include "admin_check.inc.php";

                // make sure mac is in right format
                check_mac_format($mac);

                // make sure mac does not already exist in the database
                // go through all the SELECTED replication partners
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $tmp = "\$partner_$key";
                        eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                        if ($to_replicate == 1){
		                mac_exist($dhcp_partners[$key], $key, $mac);
			}

		}

		// make sure computername only contains valid chars
		check_computername_format($username_db);

                $ip_from = $REMOTE_ADDR;

                // go through all the replication partners
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $tmp = "\$partner_$key";
                        eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                        // print "$tmp = $to_replicate<br>\n";
                        if ($to_replicate == 1){

                                mac_add($who, $ip_from, $dhcp_partners[$key], "blacklisted", $mac, $username_db, $clientname, $notes);
				mark_update($dhcp_partners[$key]);

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
		print "<b>MAC *$mac* added for <u>$username_db</u> on: $server_list</b><br>\n";
                print "<b><i><small>(Will Take Effect In About 1 Minute)</small></i></b>\n";
		print "</font>\n";
		print "</center><br>\n";

		// reset variables
		$username_db = "";
		$mac = "";

	}		

?>

<center>
<table class="table table-striped table-condensed" width="100%" id=blacklistIP"> 
<form data-async method="post" action="blacklist_add.php" method="post" class="form-horizontal modify" role="form" id="add">
<input type=hidden name=action value=add>
<input type=hidden name=username value=<? echo "$username"; ?>>
<input type=hidden name=token value=<? echo "$token"; ?>>

<tr>
<td><b>Computer Name:</b></td>
<td><input type=text name=username_db value="<? echo $username_db; ?>"></td>
</tr>

<td><b>Client Name:</b></td>
<td><input type=text name=clientname></td>
</tr>

<td><b>MAC:</b></td>
<td><input type=text name=mac placeholder="<? echo $mac; ?>"></td>

</tr>

<?

$action_date = date("Y-m-d");
$action_time = date("H:i");
$notes_string = "Blacklisted on $action_date @ $action_time.";

?>

<td><b>Notes:</b></td>
<td><textarea name=notes cols=30 rows=4><? echo $notes_string; ?></textarea></td>
</tr>

<?
        if ($dhcp_replicate == 1){

                print "<tr>\n";
                print "<td><b>Update On:</font></b></td>";
                print "<td>\n";

                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $selected = "CHECKED";
                        print "<input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b>\n";
                }

                print "</td></tr>\n";

        }

?>

<tr><td align=center colspan=2>
<input class="btn btn-danger" type="submit" name="selection" value="Blacklist MAC <? echo $mac ?>">
</td></tr>

</form>
</tr>
</table>
</center>
</body>

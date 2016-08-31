<?


        include "includes/authenticate.inc.php";
        include "includes/config.inc.php";
        $access_level = access_level($username);
        $who = $username;

    /* Use the body include to centralize formatting */
    include "includes/body.inc.php";
?>

<?	if (strcmp($action, "delete") == 0): ?>
<?

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

	
?>

<? else: ?>
<form data-async method="post" action="blacklist_delete.php" method="post" class="form-horizontal modify" role="form" id="modify">
<input type=hidden name="action" value="delete">
<input type=hidden name="username" value="<? echo $username ?>">
<input type=hidden name="token" value="<? echo $token ?>">
<input type=hidden name="username_db" value="<? echo $username_db ?>">
<input type=hidden name="mac" value=" <? echo $mac ?>">
<div class="form-group row"><label class="col-xs-4 form-control-label">Computer Name</label><div class="col-xs-8">
<? print "<samp>" . ucfirst($username_db) . "</samp>\n"; ?></div></div>
<div class="form-group row"><label class="col-xs-4 form-control-label">MAC Address</label><div class="col-xs-8">
<? print "<samp>" . ucfirst($mac) . "</samp>\n"; ?></div></div>
<div class="form-group row"><label class="col-xs-4 form-control-label">Servers</label><div class="col-xs-8">
<?
  for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
    $selected = "CHECKED";
    print "<div class=\"checkbox\"><label class=\"text-uppercase\"><input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b></label></div>\n";
    }
?>
</div></div>
<div id="doSubmitConfirm" class="form-group row text-center">
<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="window.location.reload();">Cancel</button>
<button name="modify" type="submit" class="btn btn-danger doSubmit">Delete <? echo $staff ?></button>
</div>
</form>
<? endif; ?>

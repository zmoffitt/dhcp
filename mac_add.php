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
    if (empty($mac)) { $pageTitle = "Add new MAC to registered systems";} 
    else {$pageTitle = "Add $mac to known machine list";}
                
                
    /*          
     * initialize the includes for functions and generate the header
     * use this in all front-end pages to ensure uniformity
     */   

	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	$who = $username;
	$action = $_POST['action'];

?>	

<? if (strcmp($action, "add") == 0): ?>
<?
		
		if (! $username_db || ! $mac){
			print "<center><font color=ff0000>\n";
			print "<b>Computer Name and MAC are required!</b>\n";
			print "</font></center>\n";
			include "$footer";
			exit;
		}

                // make sure it's an administrator
		include "includes/admin_check.inc.php";
		$ip_from = $REMOTE_ADDR; 

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

				mac_add($who, $ip_from, $dhcp_partners[$key], "registered", $mac, $username_db, $clientname, $notes);
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

?>

<? else: ?>
<form data-async method="post" action="mac_add.php" class="form-horizontal add" role="form" id="add">
<input type=hidden name=action value=add>
<input type=hidden name=username value="<? echo $username ?>">
<input type=hidden name=token value="<? echo $token ?>">

<div class="form-group row">
<label for="name" class="col-xs-3 control-label">Computer Name</label>
<div class="col-xs-9">
<input type="text" class="form-control" name="username_db" placeholder="<? echo $username_db ?>" required autofocus/> 
</div></div>
<div class="form-group row">
<label for="name" class="col-xs-3 control-label">Client Name</label>
<div class="col-xs-9">
<input type="text" class="form-control" name="clientname" placeholder="<? echo $clientname ?>" required />
</div></div>

<div class="form-group row">
<label for="name" class="col-xs-3 control-label">MAC Address</label>
<div class="col-xs-9">
<input type="text" class="form-control" name="mac" placeholder="<? echo $mac ?>" value="<? echo $mac; ?>" required />
</div></div>

<?

$action_date = date("Y-m-d");
$action_time = date("H:i");
$notes_string = "Registered on $action_date @ $action_time by $who.";

?>

<div class="form-group row">
<label for="name" class="col-xs-3 control-label">Notes</label>
<div class="col-xs-9">
<textarea name=notes class="form-control" rows=4><? echo $notes_string; ?></textarea>
</div></div>
<div class="form-group row">
<label for="name" class="col-xs-3 control-label">Update on:</label>
<div class="col-xs-9">

<?
	if ($dhcp_replicate == 1){

		for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
			$selected = "CHECKED";
		
			// Uncomment following lines to select local server 
			// only, by default.
	
			// $selected = "";
			// if (strcmp($identifier, $key) == 0){
			// 	$selected = "CHECKED";
			// }

			print "<input $selected type=checkbox name=partner_$key value=1><b> " . ucfirst($key) . "</b>\n";
		}

	}

?>
</div></div>
<div id="doSubmitConfirm" class="form-group row text-center">
<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="window.location.reload();">Cancel</button>  
<button class="btn btn-success" type="submit" name="add">Register MAC <? echo $mac ?></button>
</div>
</form>

<script>
$(document).ready(function() {
jQuery(function() {
    $('form[data-async]').on('submit', function(event) {
        event.preventDefault()
        var $form = $(this);

    if ( $(this).data('requestRunning') ) {
        return;
    }

    $(this).data('requestRunning', true);

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),

            success: function(data, status) {
                $("#doSubmitConfirm").addClass( "hidden" );
                $($.parseHTML(data)).appendTo(".bootbox-body");
            },

            complete: function() {
            $(this).data('requestRunning', false);
        }
        });

        event.preventDefault();
    });
});
});
</script>
</body>
<? endif; ?>

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
        $action = $_POST['action'];

?>

<? if (strcmp($action, "delete") == 0): ?>

<?php
	include "includes/admin_check.inc.php";
	$ip_from = $REMOTE_ADDR;

	// go through all the replication partners
        for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
        	$tmp = "\$partner_$key";	
        	eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                	if ($to_replicate == 1){
                        	mac_delete($who, $ip_from, $dhcp_partners[$key], "registered", $mac, $username_db);
				mark_update($dhcp_partners[$key]);
	                }	

	}
	print "<hr />";
	print "<h3 class=\"text-center text-success\">Successfully deleted: $mac!</h3>\n";
	print "<h4 class=\"text-center\">for user: $username_db</h4>\n";
?>
	
	<? else: ?>

<div id="body" class="container-fluid">
<div class="row"><div class="col-xs-12 center-block" style="float:none">
<form data-async method="post" action="mac_delete.php" method="post" class="form-horizontal modify" role="form" id="delete">
<input type=hidden name=action value="delete">
<input type=hidden name=username value="<? echo $username ?>">
<input type=hidden name=token value="<? echo $token ?>">
<input type=hidden name=username_db value="<? echo $username_db ?>">
<input type=hidden name=mac value="<? echo $mac ?>">
<div class="form-group row"><label class="col-xs-2 form-control-label">Servers</label><div class="col-xs-8">
<?
  for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
    $selected = "CHECKED";
    print "<div class=\"checkbox\"><label class=\"text-uppercase\"><input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b></label></div>\n";
    }
?>
</div></div>
<div id="doSubmitConfirm" class="form-group row text-center">
<button type="button" class="btn btn-secondary" data-dismiss="modal" onClick="window.location.reload();">Cancel</button>
<button name="delete" type="submit" class="btn btn-danger doSubmit">Delete <? echo $mac ?></button>
</div>
</form>
</div>
</div>

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
                $($.parseHTML(data)).appendTo("#body");
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
<? endif; ?>

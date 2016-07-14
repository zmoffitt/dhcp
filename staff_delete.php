<?
	include "includes/authenticate.inc.php";
	include "inclues/config.inc.php";
	$access_level = access_level($username);
        $who = $username;
	$action = $_POST['action'];

?>

<? if (strcmp($action, "delete") == 0): ?>
<?php
/*
 * Doing a delete operation and need to complete this on the request
 * Return an "ok" page via AJAX to the client
 *
 */ 
require "includes/admin_check.inc.php";

  $ip_from = $REMOTE_ADDR;

  // go through all the replication partners
  for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
    $tmp = "\$partner_$key";	
    eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
    // print "$tmp = $to_replicate<br>\n";
      if ($to_replicate == 1) {
        staff_delete($who, $ip_from, $dhcp_partners[$key], $staff, $grp);
      }	

  }

print "<hr />";
print "<h3 class=\"text-center text-success\">Successfully deleted: $staff!</h3>\n";

?>
<? else: ?>
<div id="body" class="container-fluid">
<div class="row"><div class="col-xs-12 center-block" style="float:none">
<form data-async method="post" action="staff_delete.php" method="post" class="form-horizontal modify" role="form" id="modify">
<input type=hidden name=action value="delete">
<input type=hidden name=username value="<? echo $username ?>">
<input type=hidden name=token value="<? echo $token ?>">
<input type=hidden name=staff value="<? echo $staff ?>">
<input type=hidden name=grp value=" <? echo $grp ?>">
<div class="form-group row"><label class="col-xs-2 form-control-label">Group</label><div class="col-xs-8">
<? print "<b>" . ucfirst($grp) . "</b>\n"; ?></div></div>
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
<button name="modify" type="submit" class="btn btn-danger doSubmit">Delete <? echo $staff ?></button>
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

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
                                mac_modify($who, $ip_from, $dhcp_partners[$key], "registered", $mac, $mac_db_old, $username_db, $username_db_old, $clientname, $clientname_db_old, $notes, $notes_db_old);
				mark_update($dhcp_partners[$key]);
                        }

                }

                print "<h3 class=\"text-center text-success\"><i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i> Successfully updated: $mac</h3>\n";
                print "<button type=\"button\" id=\"complete\" class=\"btn btn-success btn-block\" data-dismiss=\"modal\">Close</button></div>\n";
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
?>

<form data-async method="post" action="mac_modify.php" method="post" class="form-horizontal modify" role="form" id="modify">
<input type=hidden name=action value=modify>
<input type=hidden name=username value=<? echo $username ?>>
<input type=hidden name=token value=<? echo $token ?>>
<input type=hidden name=username_db_old value=<? echo $username_db ?>>
<input type=hidden name=mac_db_old value=<? echo $mac ?>>
<input type=hidden name=clientname_db_old value=<? echo ($row->clientname) ?>>
<input type=hidden name=notes_db_old value=<? echo ($row->notes) ?>>

<div class="form-group row"><label class="col-xs-3 form-control-label">Computer Name</label>
<div class="col-xs-9"><input type="text" class="form-control" name="username_db" value="<? echo $username_db; ?>" placeholder="<? echo $username_db ?>"></div></div>

<div class="form-group row"><label class="col-xs-3 form-control-label">Client Name</label>
<div class="col-xs-9"><input type="text" class="form-control" name="clientname" value="<? echo $clientname; ?>" placeholder="<? echo $clientname ?>"></div></div> 

<div class="form-group row"><label class="col-xs-3 form-control-label">MAC Address</label>
<div class="col-xs-9"><input type="text" class="form-control text-uppercase" name="mac" value="<? echo $mac; ?>" placeholder="<? echo $mac ?>"></div></div>

<div class="form-group row"><label class="col-xs-3 form-control-label">Computer Name</label>
<div class="col-xs-9"><textarea name="notes" class="form-control" rows="4" value="<? echo ($row->notes); ?>" placeholder="<? echo ($row->notes); ?>"><? echo ($row->notes); ?></textarea></div></div>

<div class="form-group row"><label class="col-xs-3 form-control-label">Update on</label>
<div class="col-xs-9">
<?

                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $selected = "CHECKED";

                        print "<div class=\"checkbox\"><label class=\"text-uppercase\"><input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b></label></div>\n";

                }

	}

?>
</div></div>
<div id="doSubmitConfirm" class="form-group row text-center">
<button name="cancel" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
<button name="modify" type="submit" class="btn btn-warning">Modify <? echo $mac ?></button></div>
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
                $($.parseHTML(data)).appendTo(".modal-body");
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

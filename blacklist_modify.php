<?
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	$who = $username;

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
                        if ($to_replicate == 1){
                                mac_modify($who, $ip_from, $dhcp_partners[$key], "blacklisted", $mac, $mac_db_old, $username_db, $username_db_old, $clientname, $clientname_db_old, $notes, $notes_db_old);
				mark_update($dhcp_partners[$key]);
                        }

                }

		print "<h3 class=\"text-center text-success\"><i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i> Successfully updated: $mac</h3>\n";
		print "<button type=\"button\" id=\"complete\" class=\"btn btn-success btn-block\" data-dismiss=\"modal\">Close</button></div>\n";

	}
	
	else{

		$str_sql = "SELECT * FROM $db_tablename_ip WHERE username='$username_db' AND mac='$mac'";

	        $result = mysql_db_query($db_name, $str_sql, $id_link);
	       	if (! $result) {print $top . "Failed to submit to database"; exit; }

	        $row = mysql_fetch_object($result);

                print "<div id=\"body\" class=\"container-fluid\"><div class=\"row\"><div class=\"col-xs-12 center-block\" style=\"float:none\">\n";
                print "<form data-async data-target=\"#myModal\" method=\"post\" action=\"blacklist_modify.php\" target=\"modify\" method=\"post\" class=\"form-horizontal modify\" role=\"form\" id=\"modify\">\n";
                print "<input type=hidden name=action value=modify>\n";
                print "<input type=hidden name=username value=$username>\n";
                print "<input type=hidden name=token value=$token>\n";
                print "<input type=hidden name=username_db_old value=$username_db>\n";
                print "<input type=hidden name=mac_db_old value=$mac>\n";
		print "<input type=hidden name=clientname_db_old value=\"$row->clientname\">\n";
		print "<input type=hidden name=notes_db_old value=\"$row->notes\">\n";

		/* Field: computer name */
                print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">Computer Name</label>\n";
                print "<div class=\"col-xs-10\"><input type=\"text\" class=\"form-control\" name=\"username_db\" value=\"$username_db\" placeholder=\"$username_db\"></div></div>\n";
		/* Field: client name */
                print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">Client Name</label>\n";
                print "<div class=\"col-xs-10\"><input type=\"text\" class=\"form-control\" name=\"clientname\" value=\"$row->clientname\" placeholder=\"$row->clientname\"></div></div>\n";
		/* Field: mac address */
                print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">MAC Address</label>\n";
                print "<div class=\"col-xs-10\"><input type=\"text\" class=\"form-control\" name=\"mac\" value=\"$row->mac\" placeholder=\"$row->mac\"></div></div>\n";
		/* Field: notes */
                print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">Notes</label>\n";
                print "<div class=\"col-xs-10\"><textarea class=\"form-control\" name=\"notes\" rows=\"4\" value=\"$row->notes\" placeholder=\"$row->notes\">$row->notes</textarea></div></div>\n";
		/* Field: DHCP servers */
                print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">Update on</label><div class=\"col-xs-8\">\n";
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $selected = "CHECKED";
                        print "<div class=\"checkbox\"><label class=\"text-uppercase\"><input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b></label></div>\n";
                }
		/* Print the submit/clear buttons */
                print "</div></div><div id=\"doSubmitConfirm\" class=\"form-group row text-center\">\n";
                print "<button name=\"cancel\" type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\n";
                print "<button name=\"modify\" type=\"submit\" class=\"btn btn-warning\">Modify $mac</button></div>\n";
		/* Form is complete */
                print "</form>\n";
		print "</div>\n";

	}

?>

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

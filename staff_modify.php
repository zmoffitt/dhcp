<?
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	$who = $username;
?>
<?php include "includes/header.inc.php"; ?>
<? 

	$id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

	if (strcmp($action, "modify") == 0){

                // make sure it's an administrator
                include "includes/admin_check.inc.php";

                // make sure staff is in right format
		check_staff_format($staff);

	        $ip_from = $REMOTE_ADDR;

                // go through all the replication partners
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $tmp = "\$partner_$key";
                        eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                        // print "$tmp = $to_replicate<br>\n";
                        if ($to_replicate == 1){
                                staff_modify($who, $ip_from, $dhcp_partners[$key], $staff, $staff_old, $grp, $grp_old);
                        }

                }

print "<hr />";
print "<h3 class=\"text-center text-success\"><i class=\"fa fa-check-circle\" aria-hidden=\"true\"></i> Successfully updated: $staff</h3>\n";
print "<button type=\"button\" id=\"complete\" class=\"btn btn-success btn-block\" data-dismiss=\"modal\">Close</button></div>\n";
	}
	
	else{

		$str_sql = "SELECT * FROM $db_tablename_staff WHERE username='$staff'";

	        $result = mysql_db_query($db_name, $str_sql, $id_link);
	       	if (! $result){
                print $top;
                print "Unable to insert into table: $db_tablename_logs\n";
                print $bottom;
              		exit;	
       		}	

	        $row = mysql_fetch_object($result);
		print "<div id=\"body\" class=\"container-fluid\"><div class=\"row\"><div class=\"col-xs-12 center-block\" style=\"float:none\">\n";
		print "<form data-async data-target=\"#myModal\" method=\"post\" action=\"staff_modify.php\" target=\"modify\" method=\"post\" class=\"form-horizontal modify\" role=\"form\" id=\"modify\">\n";
                print "<input type=hidden name=action value=modify>\n";
                print "<input type=hidden name=username value=$username>\n";
                print "<input type=hidden name=token value=$token>\n";
                print "<input type=hidden name=staff_old value=$staff>\n";
                print "<input type=hidden name=grp_old value=$grp>\n";
		print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">Name</label>\n";
		print "<div class=\"col-xs-8\"><input type=\"text\" class=\"form-control\" name=\"staff\" value=\"$staff\" placeholder=\"$staff\"></div></div>\n";
                print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">Group</label>\n";
		print "<div class=\"col-xs-8\"><select class=\"form-control\" name=\"grp\">\n";
		$tmp = "\$grp_$grp = SELECTED;";
		eval("$tmp");
		print "<option $grp_support value=support>Support (Read Only)\n";
		print "<option $grp_systems value=systems>Administrator (Full Access)\n";
		print "</select></div></div>\n";

                print "<div class=\"form-group row\"><label class=\"col-xs-2 form-control-label\">Servers</label><div class=\"col-xs-8\">\n";

                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $selected = "CHECKED";

                        // Uncomment following lines to select local server
                        // only, by default.

                        // $selected = "";
                        // if (strcmp($identifier, $key) == 0){
                        //      $selected = "CHECKED";
                        // }

                        print "<div class=\"checkbox\"><label class=\"text-uppercase\"><input $selected type=checkbox name=partner_$key value=1><strong>" . ucfirst($key) . "</strong></label></div>\n";

                }
		print "</div></div>\n";
		print "<div id=\"doSubmitConfirm\" class=\"form-group row text-center\">\n";
		print "<button name=\"cancel\" type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\n";
	        print "<button name=\"modify\" type=\"submit\" class=\"btn btn-warning\">Modify $staff</button></div>\n";

                print "</form>\n";
		print "<br>\n";

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

<?
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	$who = $username;
?>
<?php include "includes/header.inc.php"; ?>
<script type="text/javascript">
$(document).ready(function() {
                $(".modify").submit(function() {
                                $.ajax({        
data: $(this).serialize(), // get the form data
type: $(this).attr('method'), // GET or POST
url: $(this).attr('action'), // the file to call
success: function(response) { // on success..
$.fancybox({
content : response,
width:'450',
height: '310',
fitToView       : true,
autoSize        : false,
autoDimensions  : false,
type            : 'ajax', 
autoSize        : false,
closeClick      : false,
openEffect      : 'none',
closeEffect     : 'none',
closeBtn        : false,
helpers:
{       
overlay:
{
css: { 'background': 'rgba(255, 255, 255, 0)' }
}
}});     
},  
        });     
return false; // stop default submit event propagation
});         
});
</script>
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

		print "<center>\n";
		print "<font color=ff0000>\n";
		print "<h3>Staff record for *$staff* updated!</h3>\n";
                print "<b><i><small>(Will Take Effect Immediately)</small></i></b>\n";
		print "</font>\n";
		print "</center>\n";

	}
	
	else{

		$str_sql = "SELECT * FROM $db_tablename_staff WHERE username='$staff'";

	        $result = mysql_db_query($db_name, $str_sql, $id_link);
	       	if (! $result){
       		        print "Failed to submit!<br>\n";
        		include "$footer";
              		exit;	
       		}	

	        $row = mysql_fetch_object($result);
                print "<h4 class=\"text-center\">Editing User \"$staff\"</h4><hr />\n";
		print "<div class=\"container-fluid\"><div class=\"row\"><div class=\"col-xs-12 center-block\" style=\"float:none\">\n";
		print "<form method=\"post\" action=\"staff_modify.php\" target=\"modify\" method=\"post\" class=\"form-horizontal modify\" role=\"form\" id=\"modify\">\n";
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
		print "<div class=\"form-group row text-center\">\n";
	        print "<button name=\"modify\" target=\"_parent\" type=\"submit\" class=\"btn btn-primary\">Modify $staff</button></div>\n";

                print "</form>\n";
		print "<br>\n";

	}

?>

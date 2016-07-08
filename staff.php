<?
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
?>

<?php include "includes/header.inc.php"; ?>
<script type="text/javascript">
$(document).ready(function() {
    $(".modify").submit(function() {
        $.fancybox.showLoading(); // start fancybox loading animation
        $.ajax({
            data: $(this).serialize(), // get the form data
            type: $(this).attr('method'), // GET or POST
            url: $(this).attr('action'), // the file to call
            success: function(response) { // on success..
                $.fancybox({
                    'content': response,
                    'openEffect'  : 'none',
	            'closeEffect' : 'none',
                    'width':'450',
		    'height': '310',
                    'autoDimensions':false,
                    'type':'ajax',
                    'autoSize':false,
                    'afterClose': function () { parent.location.reload(true)},
		    'closeBtn' : false,
                });
            },
            complete: function() { // on complete...
                $.fancybox.hideLoading(); //stop fancybox loading animation
            }
        });
        return false; // stop default submit event propagation
    }); 
});

$(document).ready(function() {
    $(".delete").submit(function() {
        $.fancybox.showLoading(); // start fancybox loading animation
        $.ajax({
            data: $(this).serialize(), // get the form data
            type: $(this).attr('method'), // GET or POST
            url: $(this).attr('action'), // the file to call
            success: function(response) { // on success..
                $.fancybox({
                    'content': response,
                    'openEffect'  : 'none',
                    'closeEffect' : 'none',
                    'width':'450',
                    'height': '290',
                    'autoDimensions':false,
                    'type':'ajax',
                    'autoSize':false,
                    'afterClose': function () { parent.location.reload(true)},
                    'closeBtn' : false,
                });
            },
            complete: function() { // on complete...
                $.fancybox.hideLoading(); //stop fancybox loading animation
            }
        });
        return false; // stop default submit event propagation
    }); 
});
</script>
<h1>DHCP Manager - Staff Management</h1>
<br>

<?
	if ($access_level == $ADMIN){
?>
	
<center>
<h3>

<a href="#" onclick="window.open('staff_add.php?username=<? echo "$username"; ?>&token=<? echo "$token"; ?>', 'mac', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');">Add a Staff</a>

</h3>
</center>

<?
	}
?>

<?

        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

	$str_sql = "SELECT * FROM $db_tablename_staff";

        if ($orderby){
                $str_sql .= " ORDER BY $orderby";
        }

	else{
                $str_sql .= " ORDER BY username";
        }

        $result = mysql_db_query($db_name, $str_sql, $id_link);

       	if (! $result){
                print "Failed to submit!<br>\n";
       	        include "$footer";
               	exit;
        }

//	print "Query: *$str_sql*<br>\n";

	$total_rows = mysql_num_rows($result);

       	if ($total_rows == 0){
		print "<center>\n";
		print "<font color=ff0000>\n";
                print "<b>No staff member in the database!</b><br>\n";
		print "</font>\n";
		print "</center>\n";
       	        include "$footer";
               	exit;
        }
	print "<div class=\"container-fluid\">\n";
	print "<div id=\"row\"><div class=\"col-md-4 col-md-offset-4\">\n";
	print "<table class=\"table table-striped table-hover table-bordered table-condensed\">\n";
	print "<thead><tr>\n";
	print "<th>Staff Name</th>\n";

	print "<th>Group</th>\n";

	if ($access_level == $ADMIN){
	        print "<th>Delete</th>\n";
        	print "<th>Modify</th>\n";
	}

	print "</tr></thead>\n";
	while ($row = mysql_fetch_object($result)){

		$username_db = $row->username;
		$grp = $row->grp;

		print "<tr>\n";

		print "<td nowrap>$username_db&nbsp;</td>\n";
		print "<td nowrap>" . ucfirst($grp) . "&nbsp;</td>\n";

		if ($access_level == $ADMIN){	

			print "<td class=\"text-center\">\n";
			print "<form action=\"staff_delete.php\" target=\"delete\" method=\"post\" class=\"form-inline delete\" role=\"form\" id=\"delete\">\n";
			print "<input type=hidden name=username value=$username>\n";
			print "<input type=hidden name=token value=$token>\n";
			print "<input type=hidden name=staff value=$username_db>\n";
			print "<input type=hidden name=grp value=$grp>\n";
			print "<button name=\"delete\" data-fancybox-type=\"ajax\" class=\"btn btn-danger\" type=\"submit\">Delete</button>\n";
			print "</form>\n";
			print "</td>\n";

			print "<td align=center nowrap>\n";
                        print "<form action=\"staff_modify.php\" target=\"modify\" method=\"post\" class=\"form-inline modify\" role=\"form\" id=\"modify\">\n";
			print "<input type=hidden name=username value=$username>\n";
			print "<input type=hidden name=token value=$token>\n";
			print "<input type=hidden name=staff value=$username_db>\n";
			print "<input type=hidden name=grp value=$grp>\n";
                        print "<button name=\"modify\" data-fancybox-type=\"ajax\" class=\"btn btn-default\" type=\"submit\">Modify</button>\n";
			print "</form>\n";
			print "</td>\n";

		}

		print "</tr>\n";

	}
	
	print "</table>\n";
	print "<br><br><hr></div></div></div>\n";
?>

<?	include "$footer";?>


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

        $pageTitle = "MAC Management";


/*
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */
        require "includes/authenticate.inc.php";
        require "includes/config.inc.php";
        require "includes/header.inc.php";

	$access_level = access_level($username);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DHCP Manager</title>
<style>
td{
    max-width: 350px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
 .badge-success {
background-color: #398439;
}
.badge-warning {
background-color: #d58512;
}
</style>
<!-- Bootstrap -->
<link href="css/bootstrap.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>	

<center>
<font color=0000ff>
<h1>DHCP Manager - Register MAC</h1>
</font>
<br>

<center>
<h3>

<a href="#" onclick="window.open('mac_add.php?username=<? echo "$username"; ?>&token=<? echo "$token"; ?>', 'mac', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');">Add a MAC Address</a>

</h3>
</center>

<? 

        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

	$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip_type = 'registered'";

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
                print "<b>No MAC addresses in the database!</b><br>\n";
		print "</font>\n";
		print "</center>\n";
       	        include "$footer";
               	exit;
        }

	print "<center>\n";
	print "<table border=2 cellpadding=2 cellspacing=2 width=70%>\n";
	print "<tr><td align=center colspan=7><b><u>Static MAC Table</u></b></td></tr>\n";

	print "<tr><td align=center><b><a href=mac.php?username=$username&token=$token&orderby=username>Computer Name</a></b></td>\n";

	print "<td align=center><b><a href=mac.php?username=$username&token=$token&orderby=clientname>Client Name</a></b></td>\n";

	print "<td align=center><b><a href=mac.php?username=$username&token=$token&orderby=mac>MAC</a></b></td>\n";

	print "<td align=center><b>Notes</b></td>\n";
	print "<td align=center><b>Delete</b></td>\n";
	print "<td align=center><b>Modify</b></td></tr>\n";

	while ($row = mysql_fetch_object($result)){

		$username_db = $row->username;
		$clientname = $row->clientname;
		$mac = $row->mac;
		$notes = $row->notes;

		if (! $username_db) $username_db = "N/A";
		if (! $clientname) $clientname = "N/A";
		if (! $mac) $mac = "N/A";
		if (! $notes) $notes = "N/A";

		print "<tr>\n";

		print "<td nowrap><small>$username_db&nbsp;</small></td>\n";
		print "<td nowrap><small>$clientname&nbsp;</small></td>\n";
		print "<td nowrap>&nbsp;\n";
		print strtoupper($mac);
		print "&nbsp;</td>\n";
		print "<td><small>$notes&nbsp;</small></td>\n";

		print "<td align=center nowrap>\n";
		print "<form action=mac_delete.php>\n";
		print "<input type=hidden name=username value=$username>\n";
		print "<input type=hidden name=token value=$token>\n";
		print "<input type=hidden name=username_db value=$username_db>\n";
		print "<input type=hidden name=mac value=$mac>\n";
		print "<input type=submit value=Delete>\n";
		print "</form>\n";
		print "</td>\n";

		print "<td align=center nowrap>\n";
		print "<form action=mac_modify.php>\n";
		print "<input type=hidden name=username value=$username>\n";
		print "<input type=hidden name=token value=$token>\n";
		print "<input type=hidden name=username_db value=$username_db>\n";
		print "<input type=hidden name=mac value=$mac>\n";
		print "<input type=submit value=Modify>\n";
		print "</form>\n";
		print "</td></tr>\n";

	}
	
	print "</table>\n";
	print "<br><br>\n";
	include "$footer";

?>

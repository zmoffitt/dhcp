<?
	include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
?>
	
<title>DHCP Manager</title>
<center>
<font color=0000ff>
<h1>DHCP Manager - Blacklist MAC</h1>
</font>
<? include "links.inc.php"; ?>
<br>

<center>
<h3>

<a href="#" onclick="window.open('blacklist_add.php?username=<? echo "$username"; ?>&token=<? echo "$token"; ?>', 'mac', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');">Add a MAC Address to the blacklist</a>

</h3>
</center>

<? 

        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

	$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip_type = 'blacklisted'";

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
                print "<b>No MAC addresses in the blacklist!</b><br>\n";
		print "</font>\n";
		print "</center>\n";
       	        include "$footer";
               	exit;
        }

	print "<center>\n";
	print "<table border=2 cellpadding=2 cellspacing=2 width=70%>\n";
	print "<tr><td align=center colspan=7><b><u>Blacklisted MAC Table</u></b></td></tr>\n";

	print "<tr><td align=center><b><a href=blacklist.php?username=$username&token=$token&orderby=username>Computer Name</a></b></td>\n";

	print "<td align=center><b><a href=blacklist.php?username=$username&token=$token&orderby=clientname>Client Name</a></b></td>\n";

	print "<td align=center><b><a href=blacklist.php?username=$username&token=$token&orderby=mac>MAC</a></b></td>\n";

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
		print "<form action=blacklist_delete.php>\n";
		print "<input type=hidden name=username value=$username>\n";
		print "<input type=hidden name=token value=$token>\n";
		print "<input type=hidden name=username_db value=$username_db>\n";
		print "<input type=hidden name=mac value=$mac>\n";
		print "<input type=submit value=Delete>\n";
		print "</form>\n";
		print "</td>\n";

		print "<td align=center nowrap>\n";
		print "<form action=blacklist_modify.php>\n";
		print "<input type=hidden name=username value=$username>\n";
		print "<input type=hidden name=token value=$token>\n";
		print "<input type=hidden name=username_db value=$username_db>\n";
		print "<input type=hidden name=mac value=$mac>\n";
		print "<input type=submit value=Modify>\n";
		print "</form>\n";
		print "</td></tr>\n";

	}
	
	print "</table><br>\n";
	print "<a target=history href=logs.php?operation=post&keyword=add+blacklisted&order=datetime>See all MACs once blacklisted</a>\n"; 
	print "<br><br>\n";
	include "$footer";

?>

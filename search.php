<?
        include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
        $access_level = access_level($username);

// here, the form is submitted.
        if (strcmp($operation, "post") == 0){

		if ( (! $computername) && (! $clientname) && (! $mac) && (! $keyword) ){
			print "<center><font color=ff0000><b>\n";
			print "At least one field needs to be filled in.<br>\n";
			print "</b></font></center>\n";

			include "$footer";
			exit;
		}	

                $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
		$str_sql = "SELECT * FROM $db_tablename_ip WHERE 1 = 1";
		$str_sql2 = "SELECT * FROM $db_tablename_dynamic WHERE 1 = 1";

		if ($computername){

			$str_sql .= " AND username LIKE '%$computername%'";
			$str_sql2 .= " AND computername LIKE '%$computername%'";
		}

		if ($clientname){
			$str_sql .= " AND clientname LIKE '%$clientname%'";
		}

		if ($mac){
			check_mac_format($mac);
			$str_sql .= " AND mac = '$mac'";
			$str_sql2 .= " AND mac = '$mac'";
		}
		
		if ($keyword){

			// trim off spaces from both ends
			$keyword = trim($keyword);

			$str_sql .= " AND (username LIKE '%$keyword%' OR clientname LIKE '%$keyword%' OR notes LIKE '%$keyword%')";

			$str_sql2 .= " AND (computername LIKE '%$keyword%' OR mac LIKE '%$keyword%')";

		}

		// get rid of multiple entries in the dynamic table
		$str_sql2 .= " GROUP BY ip";

// Order by (1)
		
		if (strcmp($order, "computername") == 0){

			if ($order_str){
				$order_str .= ", username";
			}

			else{
				$order_str .= "username"; 
			}

			if ($order_str2){
				$order_str2 .= ", computername";
			}

			else{
				$order_str2 .= "computername"; 
			}

		}

		elseif (strcmp($order, "clientname") == 0){

			if ($order_str){
				$order_str .= ", clientname";
			}

			else{
				$order_str .= "clientname";
			}

		}

		elseif (strcmp($order, "ip") == 0){

			if ($order_str){
				$order_str .= ", ip";
			}

			else{
				$order_str .= "ip";
			}

			if ($order_str2){
				$order_str2 .= ", ip";
			}

			else{
				$order_str2 .= "ip";
			}

		}

		elseif (strcmp($order, "ip_type") == 0){

			if ($order_str){
				$order_str .= ", ip_type";
			}

			else{
				$order_str .= "ip_type";
			}

		}

		elseif (strcmp($order, "mac") == 0){

			if ($order_str){
				$order_str .= ", mac";
			}

			else{
				$order_str .= "mac";
			}

			if ($order_str2){
				$order_str2 .= ", mac";
			}

			else{
				$order_str2 .= "mac";
			}

		}

// Order by (2)

		if (strcmp($order2, "computername") == 0){

			if ($order_str){
				$order_str .= ", username";
			}

			else{
				$order_str .= "username"; 
			}

			if ($order_str2){
				$order_str2 .= ", computername";
			}

			else{
				$order_str2 .= "computername"; 
			}

		}

		elseif (strcmp($order2, "clientname") == 0){

			if ($order_str){
				$order_str .= ", clientname";
			}

			else{
				$order_str .= "clientname";
			}

		}

		elseif (strcmp($order2, "ip") == 0){

			if ($order_str){
				$order_str .= ", ip";
			}

			else{
				$order_str .= "ip";
			}

			if ($order_str2){
				$order_str2 .= ", ip";
			}

			else{
				$order_str2 .= "ip";
			}

		}

		elseif (strcmp($order2, "ip_type") == 0){

			if ($order_str){
				$order_str .= ", ip_type";
			}

			else{
				$order_str .= "ip_type";
			}

		}

		elseif (strcmp($order2, "mac") == 0){

			if ($order_str){
				$order_str .= ", mac";
			}

			else{
				$order_str .= "mac";
			}

			if ($order_str2){
				$order_str2 .= ", mac";
			}

			else{
				$order_str2 .= "mac";
			}

		}

// Order by (3)

		if (strcmp($order3, "computername") == 0){

			if ($order_str){
				$order_str .= ", username";
			}

			else{
				$order_str .= "username"; 
			}

			if ($order_str2){
				$order_str2 .= ", computername";
			}

			else{
				$order_str2 .= "computername"; 
			}

		}

		elseif (strcmp($order3, "clientname") == 0){

			if ($order_str){
				$order_str .= ", clientname";
			}

			else{
				$order_str .= "clientname";
			}

		}

		elseif (strcmp($order3, "ip") == 0){

			if ($order_str){
				$order_str .= ", ip";
			}

			else{
				$order_str .= "ip";
			}

			if ($order_str2){
				$order_str2 .= ", ip";
			}

			else{
				$order_str2 .= "ip";
			}

		}

		elseif (strcmp($order3, "ip_type") == 0){

			if ($order_str){
				$order_str .= ", ip_type";
			}

			else{
				$order_str .= "ip_type";
			}

		}

		elseif (strcmp($order3, "mac") == 0){

			if ($order_str){
				$order_str .= ", mac";
			}

			else{
				$order_str .= "mac";
			}

			if ($order_str2){
				$order_str2 .= ", mac";
			}

			else{
				$order_str2 .= "mac";
			}

		}

		if ($order_str){
			$str_sql .= " ORDER BY $order_str";
		}

		if ($order_str2){
			$str_sql2 .= " ORDER BY $order_str2";
		}

		// print "Query1: *$str_sql*<br><br>\n";
		// print "Query2: *$str_sql2*<br><br>\n";

                $result = mysql_db_query($db_name, $str_sql, $id_link);

                if (! $result){
                        print "Failed to submit!<br>\n";
                        exit;
                }

                $result2 = mysql_db_query($db_name, $str_sql2, $id_link);

                if (! $result2){
                        print "Failed to submit!<br>\n";
                        exit;
                }

		$total = mysql_num_rows($result);
		$total2 = mysql_num_rows($result2);
		$grand_total = $total + $total2;

	        if ($grand_total == 0){

			print "<title>DHCP Manager</title>\n";
			print "<center><h1><font color=0000ff>DHCP Manager: Search</font></h1><br>\n";

        	        print "<font color=ff0000><h3>No match for the given criteria!</h3></font><br>\n";

			print "<a href=search.php?operation=&username=$username&token=$token>Perform Another Search</a><br>\n";

	                print "</center>\n";

			include "$footer";
			exit;
        	}

	        if ($grand_total > $max_result){

			print "<title>DHCP Manager</title>\n";

			print "<center><h1><font color=0000ff>DHCP Manager: Search</font></h1><br>\n";

        	        print "<font color=ff0000><h3>Too many matches (over $max_result) for the given criteria! Please narrow your search.</h3></font><br>\n";

			print "<a href=search.php?operation=&username=$username&token=$token>Perform Another Search</a><br>\n";

	                print "</center>\n";

			include "$footer";
			exit;
        	}

		print "<title>DHCP Manager</title>\n";
		print "<center><h1><font color=0000ff>DHCP Manager: Search</font></h1></center>\n";

		print "<center>\n";
		print "<b>From Date: </b><i>$date_start</i><br>\n";
		print "<b>To Date: </b><i>$date_end</i><br>\n";
		print "<b>Order By: </b><i>";
		print(ucfirst("$order"));

                if ($order2){
                        print ", ";
                        print(ucfirst("$order2"));
                }

                if ($order3){
                        print ", ";
                        print(ucfirst("$order3"));
                }

		print "</i><br><br>\n";

		$time_string = date("M j, Y, D, h:i:s A");
		print "<i><b>Now: </b>$time_string</i><br><br>\n";

		print "<h4><u>There are <b><font color=ff0000>$grand_total</font></b> matches found!</u></h4>\n";
		print "</center><br>\n";

		print "<center><table cellspacing=2 cellpadding=3 border=2 width=80%>";
               	print "<tr bgcolor=eeeeee>\n";
		print "<td><b>IP:</b></td>\n";
		print "<td><b>Type:</b></td>\n";
		print "<td><b>Computer Name:</b></td>\n";
		print "<td><b>Client Name:</b></td>\n";
		print "<td><b>MAC:</b></td>\n";
		print "<td><b>Notes:</b></td></tr>\n";

		// data from the 'ip' table
		if ($total > 0){
			print "<tr><td colspan=6 align=center><b><u><font color=0099cc><small>From IP Table</small></font></u></b></td></tr>\n";
		}

		while ($row = mysql_fetch_object($result)){

			if (! $row->username){
				$row->username = "N/A";
			}

			if (! $row->clientname){
				$row->clientname = "N/A";
			}

			if (! $row->mac){
				$row->mac = "N/A";
			}

			print "<tr>\n";
			if ($row->ip){

		                if ($access_level == $ADMIN){
					print "<td><small><a href=# onClick=\"window.open('modify_ip.php?ip=$row->ip&username=$username&token=$token', 'ip', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');\">$row->ip</a></small></td>\n";
				}

		                else{
		                        print "<td>$row->ip</a></td>\n";
		                }

			}

			else{
				print "<td align=center>N/A</td>\n";
			}


	                $str_sql_dynamic = "SELECT * FROM $db_tablename_dynamic WHERE ip = '$row->ip'";

	                $result_dynamic = mysql_db_query($db_name, $str_sql_dynamic, $id_link);

	                if (! $result_dynamic){
        	                print "Failed to submit!<br>\n";
                	        include "$footer";
                        	exit;
			}

			$total_rows_dynamic = mysql_num_rows($result_dynamic);

			// $bgcolor_type = "ffffff";
	                if (strcmp($row->ip_type, "dynamic") == 0){
        	                $bgcolor_type = $color_dynamic;
                	}

	                elseif (strcmp($row->ip_type, "free") == 0){
        	                $bgcolor_type = $color_free;
	                }

        	        elseif (strcmp($row->ip_type, "reserved") == 0){
                	        $bgcolor_type = $color_reserved;
	                }

        	        elseif (strcmp($row->ip_type, "static") == 0){
                	        $bgcolor_type = $color_static;
	                }

	                elseif (strcmp($row->ip_type, "unknown") == 0){
        	                $bgcolor_type = $color_unknown;
                	}

	                elseif (strcmp($row->ip_type, "registered") == 0){
        	                $bgcolor_type = "33cccc";
                	}

			else{
        	                $bgcolor_type = "ff0000";
                	}

			print "<td bgcolor=$bgcolor_type nowrap><small>".ucfirst($row->ip_type)."&nbsp;</small></td>\n";


	                if ($total_rows_dynamic > 0){

        	                $row_dynamic = mysql_fetch_object($result_dynamic);
                	        print "<td bgcolor=$color_dynamic_active nowrap><small><a href=\"#\" onclick=\"window.open('active.php?username=$username&token=$token&ip=$row->ip', 'active', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');\">$row_dynamic->computername&nbsp;</a></small></td>\n";

                	}

			else{			
				print "<td><small>$row->username&nbsp;</small></td>\n";
			}

			print "<td><small>$row->clientname&nbsp;</small></td>\n";
			print "<td><small>$row->mac&nbsp;</small></td>\n";
			print "<td><small>$row->notes&nbsp;</small></td>\n";

		}

		// data from the 'dynamic' table
		if ($total2 > 0){
			print "<tr><td colspan=6 align=center><b><u><font color=0099cc><small>From Dynamic Table</i></small></font></u></b></td></tr>\n";
		}

		while ($row2 = mysql_fetch_object($result2)){

			if (! $row2->username){
				$row2->username = "N/A";
			}

			if (! $row2->mac){
				$row2->mac = "N/A";
			}

			print "<tr>\n";
			if ($row2->ip){

		                if ($access_level == $ADMIN){
					print "<td><small><a href=# onClick=\"window.open('modify_ip.php?ip=$row2->ip&username=$username&token=$token', 'ip', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');\">$row2->ip</a></small></td>\n";
				}

		                else{
		                        print "<td>$row2->ip</a></td>\n";
		                }

			}

			else{
				print "<td align=center>N/A</td>\n";
			}

			// from 'dynamic' table, so Type is always 'dynamic'

			print "<td bgcolor=$color_dynamic><small>Dynamic&nbsp;</small></td>\n";
			print "<td bgcolor=$color_dynamic_active><small><a href=\"#\" onclick=\"window.open('active.php?username=$username&token=$token&ip=$row2->ip', 'active', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');\">$row2->computername&nbsp;</a></small></td>\n";
			print "<td><small>N/A</small></td>\n";
			print "<td><small>$row2->mac&nbsp;</small></td>\n";
			print "<td><small>&nbsp;</small></td>\n";

		}

		print "</tr></table></center><br><br>\n";
                print "<center><a href=search.php?operation=&username=$username&token=$token>Perform Another Search</a></center><br>\n";

		include "$footer";
			
	}

// Here, the pag is first loaded to display the form.

	else{

?>

<title>DHCP Manager</title>
<center><h2><font color=0000ff>DHCP Manager: Search</font></h2></center>
<br>

<form method=POST action=search.php>
<input type=hidden name=operation value=post>
<input type=hidden name=username value=<? echo $username; ?>>
<input type=hidden name=token value=<? echo $token; ?>>

<center>
<table bgcolor=eeeeee cellspacing=0 cellpadding=5 border=1 width=50%>

<tr>
<td align=center>
<font color=ff0000><b>Please leave fields as default to match all.</b></font>
</td>
</tr>

<tr>
<td nowrap>
<b>Computer Name:</b>
<input type=text name=computername size=15>
<font color=ff0000>
<small><i>(Put in full or partial computer name)</i></small>
</font>
</td>
</tr>

<tr>
<td nowrap>
<b>Client Name:</b>
<input type=text name=clientname size=15>
<font color=ff0000>
<small><i>(Put in full or partial client name)</i></small>
</font>
</td>
</tr>

<tr>
<td nowrap>
<b>MAC:</b>
<input type=text name=mac size=20>
<font color=ff0000>
<small><i>(e.g. 00:80:C1:60:50:CE)</i></small>
</font>
</td>
</tr>

<tr>
<td nowrap>
<b>Keyword:</b>
<input type=text name=keyword size=20 maxlength=50>
<font color=ff0000>
<small><i>(Type any string to search various fields)</i></small> 
</font>
</td>
</tr>

<tr>
<td nowrap>
<b>Sort By (1):</b>
<select name=order>
<option value="">-- None --
<option value=clientname>Client Name
<option value=computername>Computer Name
<option value=ip>IP
<option value=ip_type>IP Type
<option value=mac>MAC
</select>
</td>
</tr>

<tr>
<td nowrap>
<b>Sort By (2):</b>
<select name=order2>
<option value="">-- None --
<option value=clientname>Client Name
<option value=computername>Computer Name
<option value=ip>IP
<option value=ip_type>IP Type
<option value=mac>MAC
</select>
</td>
</tr>

<tr>
<td nowrap>
<b>Sort By (3):</b>
<select name=order3>
<option value="">-- None --
<option value=clientname>Client Name
<option value=computername>Computer Name
<option value=ip>IP
<option value=ip_type>IP Type
<option value=mac>MAC
</select>
</td>
</tr>

<tr><td colspan=2 align=middle>
<br>
<input type=submit value="Search Logs">
</td></tr>

</table>
</center>
</form>

<?

	include "$footer";

	}

?>


<?

	include "config.inc.php";

// here, the form is submitted.
        if (strcmp($operation, "post") == 0){

                $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
		$str_sql = "SELECT * FROM $db_tablename_logs WHERE 1 = 1";

                if ($from_month || $from_day){ 

                        if ( !($from_year) ){
                                $from_year = date("Y");
                        }

                        if ( !($from_month) ){
                                $from_month = date("m");
                        }

                        if ( !($from_day) ){
				$from_day = "01";
                        }

                        $date_start = "$from_year";
                        $date_start .= "-";
                        $date_start .= "$from_month";
                        $date_start .= "-";
                        $date_start .= "$from_day";

                        if ( !($to_year) ){
                                $to_year = date("Y");
                        }

                        if ( !($to_month) ){
                                $to_month = date("m");
                        }

                        if ( !($to_day) ){
                                $to_day = date("d");
                        }

                        $date_end = "$to_year";
                        $date_end .= "-";
                        $date_end .= "$to_month";
                        $date_end .= "-";
                        $date_end .= "$to_day";

                        $str_sql .= " AND datetime BETWEEN '$date_start 00:00:00' and '$date_end 23:59:59'";
		}	

		if ($who){
			$str_sql .= " AND who LIKE '%$who%'";
		}

		if ($category){
			$str_sql .= " AND category = '$category'";
		}

		if ($ip){
			$str_sql .= " AND ip LIKE '%$ip%'";
		}

		if ($keyword){
			$keyword = strtolower($keyword);
			$str_sql .= " AND changes LIKE '%$keyword%'";
			if (ereg("blacklisted", $keyword, $match)){
				$blacklisted = 1;
			}
		}

		if ($changes){
			$str_sql .= " AND room LIKE '%$changes%'";
		}

// Order by (1)
		
		if (!strcmp($order, "datetime")){
			$str_sql .= " ORDER BY datetime";
		}

		elseif (!strcmp($order, "who")){
			$str_sql .= " ORDER BY who";
		}

		elseif (!strcmp($order, "category")){
			$str_sql .= " ORDER BY category";
		}

		elseif (!strcmp($order, "ip")){
			$str_sql .= " ORDER BY ip";
		}


// Order by (2)

		if ($order2){		
			if (!strcmp($order2, "datetime")){
				$str_sql .= ", datetime";
			}

			elseif (!strcmp($order2, "who")){
				$str_sql .= ", who";
			}

			elseif (!strcmp($order2, "category")){
				$str_sql .= ", category";
			}

			elseif (!strcmp($order2, "ip")){
				$str_sql .= ", ip";
			}
		}

// Order by (3)

		if ($order3){		
			if (!strcmp($order3, "datetime")){
				$str_sql .= ", datetime";
			}

			elseif (!strcmp($order3, "who")){
				$str_sql .= ", who";
			}

			elseif (!strcmp($order3, "category")){
				$str_sql .= ", category";
			}

			elseif (!strcmp($order3, "ip")){
				$str_sql .= ", ip";
			}
		}

		// print "Query: *$str_sql*<br><br>\n";

                $result = mysql_db_query($db_name, $str_sql, $id_link);

                if (! $result){
                        print "Failed to submit!<br>\n";
                        exit;
                }

		$total = mysql_num_rows($result);

	        if ($total == 0){

			print "<title>DHCP Manager</title>\n";
			print "<center><h1><font color=0000ff>DHCP Manager: Log Viewer</font></h1><br>\n";

        	        print "<font color=ff0000><h3>No match for the given criteria!</h3></font><br>\n";

			print "<a href=logs.php?operation=>Perform Another Search</a><br>\n";

	                print "</center>\n";

			include "$footer";
			exit;
        	}

	        if ($total > $max_result){

			print "<title>DHCP Manager</title>\n";

			print "<center><h1><font color=0000ff>DHCP Manager: Log Viewer</font></h1><br>\n";

        	        print "<font color=ff0000><h3>Too many matches (over $max_result) for the given criteria! Please narrow your search.</h3></font><br>\n";

			print "<a href=logs.php?operation=>Perform Another Search</a><br>\n";

	                print "</center>\n";

			include "$footer";
			exit;
        	}

		print "<title>DHCP Manager</title>\n";
		print "<center><h1><font color=0000ff>DHCP Manager: Log Viewer</font></h1></center>\n";

		if (! $date_start){
			$date_start = "All";
		}

		if (! $date_end){
			$date_end = "All";
		}

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

		print "<h4><u>There are <b><font color=ff0000>$total</font></b> matches found!</u></h4>\n";
		print "</center><br>\n";

		print "<center><table cellspacing=2 cellpadding=3 border=2 width=80%>";
               	print "<tr bgcolor=eeeeee>\n";
		print "<td><b>Date/Time:</b></td>\n";
		print "<td><b>Category:</b></td>\n";
		print "<td><b>Staff Username:</b></td>\n";
		print "<td><b>Changes:</b></td>\n";
		print "<td><b>IP From:</b></td></tr>\n";

		while ($row = mysql_fetch_object($result)){

			print "<tr>\n";

			print "<td><small>$row->datetime&nbsp;</small></td>\n";
			print "<td>" . ucfirst($row->category) . "&nbsp;</td>\n";

			print "<td align=center>$row->who&nbsp;</td>\n";

//			if ($blacklisted == 1){
//				$row->changes = ereg_replace("MAC: ([A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9])", 

			print "<td><i>$row->changes&nbsp;</i></td>\n";
			print "<td>$row->ip&nbsp;</td></tr>\n";
			
		}

		print "</tr></table></center><br>\n";
                print "<center><a href=logs.php?operation=>Perform Another Search</a></center><br>\n";

		include "$footer";
			
	}

// Here, the pag is first loaded to display the form.

	else{

?>

<title>DHCP Manager</title>
<center><h2><font color=0000ff>DHCP Manager: Log Viewer</font></h2></center>
<br>

<form method=POST action=logs.php>
<input type=hidden name=operation value=post>

<center>
<table bgcolor=eeeeee cellspacing=0 cellpadding=5 border=1 width=70%>

<tr>
<td align=center>
<font color=ff0000><i>Please leave fields as default to match all.</i></font>
</td>
</tr>

<tr>
<td nowrap colspan=2 align=middle><b>Dates To View:</b></td>
</tr>

<tr>
<td nowrap>

<b>Between:</b>

<select name=from_month>
<? include "months.inc.php" ?>
</select>

<select name=from_day>
<? include "days.inc.php" ?>
</select>

<?
	$current_year = date("Y");
?>

<b>,</b>
<select name=from_year>
<?
	print "<option value=\"\">[Y]\n";
	print "<option selected value=$current_year>$current_year\n";
	$current_year--;
	print "<option value=$current_year>$current_year\n";
	$current_year--;
	print "<option value=$current_year>$current_year\n";

?>
</select>
</td>
</tr>

<tr>
<td nowrap>

<b>And:</b>

<select name=to_month>
<? include "months.inc.php" ?>
</select>

<select name=to_day>
<? include "days.inc.php" ?>
</select>

<?
	$current_year = date("Y");
?>

<b>,</b>
<select name=to_year>
<?
	print "<option value=\"\">[Y]\n";
	print "<option selected value=$current_year>$current_year\n";
	$current_year--;
	print "<option value=$current_year>$current_year\n";
	$current_year--;
	print "<option value=$current_year>$current_year\n";

?>
</select>
<small><i>(Leave these blank to use current date)</i></small>
</td>
</tr>

<tr>
<td nowrap>
<b>Category:</b>
<select name=category>
<option value="">-- All --
<option value="global">Global
<option value="subnet">Subnet
<option value="ip">IP
<option value="mac">MAC
<option value="other">Other
</select>
</td>
</tr>

<tr>
<td nowrap>
<b>Staff Username:</b>
<input type=text name=who size=10 maxlength=20>
<small><i>(Put in full or partial username)</i></small> 
</td>
</tr>

<tr>
<td nowrap>
<b>IP From:</b>
<input type=text name=ip size=15 maxlength=15>
<small><i>(Put in full or partial IP address)</i></small> 
</td>
</tr>

<tr>
<td nowrap>
<b>Keyword:</b>
<input type=text name=keyword size=15 maxlength=50>
<small><i>(Put in any string to search the "Changes" field)</i></small> 
</td>
</tr>

<tr>
<td nowrap>
<b>Sort By (1):</b>
<select name=order>
<option value="">-- None --
<option value=category>Category
<option selected value=datetime>Date
<option value=ip>IP
<option value=who>Staff
</select>
</td>
</tr>

<tr>
<td nowrap>
<b>Sort By (2):</b>
<select name=order2>
<option value="">-- None --
<option value=category>Category
<option selected value=datetime>Date
<option value=ip>IP
<option value=who>Staff
</select>
</td>
</tr>

<tr>
<td nowrap>
<b>Sort By (3):</b>
<select name=order3>
<option value="">-- None --
<option value=category>Category
<option selected value=datetime>Date
<option value=ip>IP
<option value=who>Staff
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
	$day = date("d");
	$year = date("Y");
	$month = date("m");
?>

<center>
<a href=logs.php?operation=post&from_month=<? echo $month; ?>&from_day=<? echo $day; ?>&from_year=<? echo $year; ?>&to_month=<? echo $month; ?>&to_day=<? echo $day; ?>&to_year=<? echo $year; ?>&order=datetime>
Today's View
</a><br>
</center>
<br>

<?

	include "$footer";

	}

?>


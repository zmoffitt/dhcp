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

        $pageTitle = "DHCPd Logs";


/*
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */
        include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	require "includes/header.inc.php";
        $access_level = access_level($username);

// here, the form is submitted.
        if (strcmp($operation, "post") == 0){

		if (! $mac && ! $ip && ! $keyword){
			print "<center><h1><font color=0000ff>DHCP Manager: Daemon Logs</font></h1></center>\n";

			print "<br><center><font color=ff0000>\n";
			print "<b>MAC, IP, or Keyword is required for the search!</b><br>";
			print "</font></center>\n";
			include "$footer";
			exit;
		}

		if ($log_level == 0){
			$logfile = "$dhcpd_log";
		}

		else if ($log_level == 1){
			$logfile = "$dhcpd_log.1";
		}

		else if ($log_level == 2){
			$logfile = "$dhcpd_log.2";
		}

		else if ($log_level == 3){
			$logfile = "$dhcpd_log.3";
		}

		else if ($log_level == 4){
			$logfile = "$dhcpd_log.4";
		}

		if (! file_exists("$logfile")){
			print "<center><h1><font color=0000ff>DHCP Manager: Daemon Logs</font></h1></center><br>\n";
			print "<center><font color=ff0000>\n";
			print "<b>The log file *$logfile* doe NOT exist!</b><br>\n";
			print "</font></center>\n";
			include "$footer";
			exit;
		}

		$pipe = 0;

		if ($mac){

			check_mac_format($mac);
			if ($pipe == 1){
				$command .= " |grep -i $mac";
			}

			else{
				$command = "grep -i $mac $logfile";
				$pipe = 1;
			}
		
		}

		if ($ip){

			check_ip_format($ip);
			if ($pipe == 1){
				$command .= " |grep -i $ip";
			}

			else{
				$command = "grep -i $ip $logfile";
				$pipe = 1;
			}

		}

		if ($keyword){

			if ($pipe == 1){
				$command .= " |grep -i \"$keyword\"";
			}

			else{
				$command = "grep -i \"$keyword\" $logfile";
				$pipe = 1;
			}

		}

		// print "Command: *$command*<br>\n";
		$output = `$command`;
		$lines = split("[\n]", $output); 

		print "<title>DHCP Manager</title>\n";

		print "<center>\n";
		print "<h1><font color=0000ff>DHCP Manager: Daemon Logs</font></h1>\n";
		print "<br>\n";
		print "<table border=4 cellspacing=2 cellpadding=2 width=85%>\n";

		for ($i = 0; $i < count($lines); $i++){

			if ($i % 2){
				$bgcolor = $color_dhcpd_logs_1;
			}

			else{
				$bgcolor = $color_dhcpd_logs_2;
			}

			print "<tr><td bgcolor=$bgcolor>$lines[$i]</td></tr>\n";
			// ob_flush();
			// flush();

		}

		print "</td></tr></table>\n";
		print "<br><br>\n";

		print "<a href=dhcpd_logs.php?username=$username&token=$token>Do Another Search</a><br>\n";

		print "</center>\n";
		include "$footer";

	}

// Here, the pag is first loaded to display the form.

	else{

?>

<center><h2><font color=0000ff>DHCP Manager: Daemon Logs</font></h2></center>
<br>

<form method=POST action=dhcpd_logs.php>
<input type=hidden name=operation value=post>
<input type=hidden name=username value=<? echo $username; ?>>
<input type=hidden name=token value=<? echo $token; ?>>

<center>
<table bgcolor=eeeeee cellspacing=4 cellpadding=4 border=4 width=50%>

<tr>
<td colspan=2 align=center>
<font color=ff0000><b><u>Warning</u>: The Search is Very Slow!</b></font>
</td>
</tr>

<tr>
<td><b>MAC:</b></td>
<td><input type=text name=mac>
&nbsp;<small><font color=ff0000>(e.g. 00:06:5b:4b:06:80)</font></small>
</td>
</tr>

<tr>
<td><b>IP:</b></td>
<td><input type=text name=ip>
&nbsp;<small><font color=ff0000>(e.g. 128.59.39.39)</font></small>
</td>
</tr>

<tr>
<td><b>Keyword:</b></td>
<td><input type=text name=keyword>
&nbsp;<small><font color=ff0000>(Case insensitive)</font></small>
</td>
</tr>

<?

	for ($i = 0; $i < 5; $i++){

		if ($i == 0){
			$flag = "checked";
			$logfile = $dhcpd_log;
		}

		else{
			$flag = "";
			$logfile = $dhcpd_log . ".$i";
		}

		// get starting date of logfile
		$command = "head -1 $logfile";
		$out = `$command`;
		eregi("^([A-Za-z]+[ ]+[0-9]+)[ ]+([0-9]+:[0-9]+:[0-9]+)", $out, $match);
		$head = $match[1] . ", " . $match[2];

		// get end date of logfile
		$command = "tail -1 $logfile";
		$out = `$command`;
		eregi("^([A-Za-z]+[ ]+[0-9]+)[ ]+([0-9]+:[0-9]+:[0-9]+)", $out, $match);
		$tail = $match[1] . ", " . $match[2];
	
		print "<tr><td colspan=2 align=align>\n";
		print "<input $flag type=radio name=log_level value=$i><b>Log $i</b>&nbsp;&nbsp;&nbsp;<small><font color=ff0000>($head => $tail)</font></small>\n";
		print "</td></tr>\n";

	}

?>

<tr>
<td colspan=2 align=center>
<input type=submit value="Search Logs">
</td>
</tr>

</table>
</center>
</form>

<!--
<center>
<a href=logs.php?operation=post&from_month=<? echo $month; ?>&from_day=<? echo $day; ?>&from_year=<? echo $year; ?>&to_month=<? echo $month; ?>&to_day=<? echo $day; ?>&to_year=<? echo $year; ?>&order=datetime>
Today's View
</a><br>
</center>
-->

<?

	include "$footer";

	}

?>


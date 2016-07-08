<?
	include "config.inc.php";
	$auth_string = auth_string($username);
?>

<center>
<a href=modify_global.php?username=<? echo $username; ?>&token=<? echo $token; ?>>Global Option</a><br>
<a href=modify_subnet.php?username=<? echo $username; ?>&token=<? echo $token; ?>>Subnet Declarations</a><br>
<a href=main.php?username=<? echo $username; ?>&token=<? echo $token; ?>&refresh_rate=<? echo $refresh_rate; ?>>IP Management</a><br>
<img src=thumb_up.gif><a href=mac.php?username=<? echo $username; ?>&token=<? echo $token; ?>>Register MAC</a><br>
<img src=thumb_down.gif><a href=blacklist.php?username=<? echo $username; ?>&token=<? echo $token; ?>>Blacklist MAC</a><br>
<a target=log href=logs.php?action=>Log Viewer</a><br>
<a target=log href=login_logs.php?action=>Login Log Viewer</a><br>
<a target=log href=dhcpd_logs.php?username=<? echo $username; ?>&token=<? echo $token; ?>>Search Daemon Logs</a><br>
<a target=log href=search.php?operation=&username=<? echo $username; ?>&token=<? echo $token; ?>>Search Computer/Client</a><br>
<a target=log href=/stats/>DHCP Statistics</a><br>
<a target=trigger href=http://128.59.205.3/update/main.php?username=<? echo $username; ?>&auth_string=<? echo $auth_string; ?>>Trigger Student MAC Update</a><br>

<?

	include "config.inc.php";
	for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
		if (strcmp($identifier, $key) != 0){
			print "<a target=win_$key href=http://$dhcp_partners[$key]/dhcp/main.php?username=$username&token=$token>DHCP Manager: $key</a><br>\n";
		}

	}

	print "<a target=staff href=staff.php?username=$username&token=$token>Staff Management</a><br>\n";

?>

<a href=logout.php?username=<? echo $username; ?>&token=<? echo $token; ?>&ip_from=<? echo $ip_from; ?>>Log Out</a><br>
</center>
<br>

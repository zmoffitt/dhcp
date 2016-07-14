<?

        include_once "config.inc.php";
        $auth_string = auth_string($username);
        $activePage = basename($_SERVER['PHP_SELF'], ".php");
        $username = $_GET['username'];

        include_once "includes/authenticate.inc.php";
        $access_level = access_level($username);

if ($subnet == '83') {
	$s83 = "class=\"active\"";
} else if ($subnet == '190') {
	$s190 = "class=\"active\"";
} else if ($subnet == '84') {
        $s84 = "class=\"active\"";
} else if ($subnet == '199') {
        $s199 = "class=\"active\"";
} else if ($subnet == '205') {
        $s205 = "class=\"active\"";
} else if ($subnet == '218') {
	$s218 = "class=\"active\"";
} else if ($subnet == '192.168.190') {
        $swaf = "class=\"active\"";
} else if ($subnet == '172.18.8') {
        $cnat = "class=\"active\"";
} else if ($subnet == '10.223.32') {
        $voip = "class=\"active\"";
} else if ($subnet == '172.18.4') {
        $mmg = "class=\"active\"";
} else if ($subnet == '172.18.0') {
        $pxe = "class=\"active\"";
} else if ($subnet == '10.252.0') {
        $devstag = "class=\"active\"";
} else if ($subnet == '10.30.30') {
        $mgmt = "class=\"active\"";
} else if ($subnet == '192.168.13') {
        $storage = "class=\"active\"";
} else {
	$s172 = "class=\"active\"";
}


	if ($activePage == 'modify_subnet') {
	print "<div class=\"col-xs-6\" style=\"float: none; margin: 0 auto;\">\n";
	} else {
        print "<div class=\"container\">\n";
        print "<div class=\"row text-center\">\n";
	print "<div class=\"col-lg-8 col-lg-offset-2\">\n";
	}

	if (strcmp($management_of, "ip") == 0){
		$script = "main.php";
	}

	elseif (strcmp($management_of, "subnet") == 0){
		$script = "modify_subnet.php";
	}
	print "<ul class=\"nav nav-pills nav-justified\">\n";
	print "<li $s83><a href=$script?subnet=83&username=$username&token=$token&refresh_rate=$refresh_rate>Subnet 83</a></li>\n";
        print "<li $s84><a href=$script?subnet=84&username=$username&token=$token&refresh_rate=$refresh_rate>Subnet 84</a></li>\n";
        print "<li $s172><a href=$script?subnet=172&username=$username&token=$token&refresh_rate=$refresh_rate>Subnet 172</a></li>\n";
        print "<li $s190><a href=$script?subnet=190&username=$username&token=$token&refresh_rate=$refresh_rate>Subnet 190</a></li>\n";
       	print "<li $s199><a href=$script?subnet=199&username=$username&token=$token&refresh_rate=$refresh_rate>Subnet 199</a></li>\n";
        print "<li $s205><a href=$script?subnet=205&username=$username&token=$token&refresh_rate=$refresh_rate>Subnet 205</a></li>\n";
        print "<li $s218><a href=$script?subnet=218&username=$username&token=$token&refresh_rate=$refresh_rate>Subnet 218</a></li>\n";

	if ($access_level == $ADMIN) {
		if ($activePage != 'modify_subnet') {
			print "</ul>\n";
		        print "<ul class=\"nav nav-pills nav-justified\">\n";
		} else {
			print "<br />";
		}
	
		print "<li $swaf><a href=$script?subnet=192.168.190&username=$username&token=$token&refresh_rate=$refresh_rate>WAF Internal</a></li>\n";
	        print "<li $cnat><a href=$script?subnet=172.18.8&username=$username&token=$token&refresh_rate=$refresh_rate>Classroom NAT</a></li>\n";
	        print "<li $voip><a href=$script?subnet=10.223.32&username=$username&token=$token&refresh_rate=$refresh_rate>VoIP Subnet</a></li>\n";
	        print "<li $mmg><a href=$script?subnet=172.18.4&username=$username&token=$token&refresh_rate=$refresh_rate>MMG Internal</a></li>\n";
	        print "<li $pxe><a href=$script?subnet=172.18.0&username=$username&token=$token&refresh_rate=$refresh_rate>PXE Subnet</a></li>\n";
		print "<li $mgmt><a href=$script?subnet=10.30.30&username=$username&token=$token&refresh_rate=$refresh_rate>Mgmt Subnet</a></li>\n";
		print "<li $storage><a href=$script?subnet=192.168.13&username=$username&token=$token&refresh_rate=$refresh_rate>Storage Subnet</a></li>\n";
	        print "<li $devstag><a href=$script?subnet=10.252.0&username=$username&token=$token&refresh_rate=$refresh_rate>Dev/Stag Subnet</a></li>\n";;
	       	print "</ul></div></div></div>\n";
	} else {
		print "</div></div></div>\n";
	}
?>

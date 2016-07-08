<?

	include "includes/config.inc.php";

	// 0 being administrator
	// administrator means the "systems" group	
	if ($access_level != $ADMIN){

		print "<center>\n";
		print "<font color=ff0000>\n";
		print "<b><h3>You need to be an administrator to do this function!</h3></b>";
		print "</font>\n";
		print "</center>\n";

		include "$footer";
		exit;

	}

?>

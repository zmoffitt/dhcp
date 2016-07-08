<?

include "includes/functions.inc.php";
include "includes/config.inc.php";

logout($username, $token, $ip_from);
HEADER("Location: index.php");

?>

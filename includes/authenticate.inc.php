<?

include "config.inc.php";
include "functions.inc.php";

$ip_from = $REMOTE_ADDR;

$id_link = mysql_pconnect($db_hostname_auth, $db_login_username, $db_login_password);
$str_sql = "SELECT * FROM $db_table_sessions WHERE username='$username' AND token='$token' AND ip='$ip_from'";

// print "Query: *$str_sql*<br>\n";

$result = mysql_db_query($db_login_name, $str_sql, $id_link);
if (! $result){
  print "Failed to submit!<br>\n";
  include "bottom.inc";
  exit;
}

$total = mysql_num_rows($result);
 
// if login is valid, update the lastupdated field to "renew" the session
if ($total >= 1){

  $lastupdated = date("Y-m-d H:i:s");

  $str_sql = "UPDATE $db_table_sessions SET lastupdated = '$lastupdated' WHERE username='$username' AND token='$token' AND ip='$ip_from'";

  // print "Query: *$str_sql*<br>\n";

  $result = mysql_db_query($db_login_name, $str_sql, $id_link);

  if (! $result){
    print "Failed to submit!<br>\n";
    include "bottom.inc";
    exit;
  }

}

else{

  HEADER("Location: /dhcp/index.php?q=sessionTimeout");
  exit;

}

?>

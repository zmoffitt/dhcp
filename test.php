<?

include "includes/functions.inc.php";

$throttled = get_throttled();
for (reset($throttled); $key = key($throttled); next($throttled)){
  print "*$throttled[$key]*<br>\n";
}

?>

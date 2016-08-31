<?php

/**         
 * Function include for DHCP Management Console
 *      
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Zachary Moffitt <zac@gsb.columbia.edu>
 * @copyright 2016 Columbia Business School
 */         

/*                  
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */                 

require "config.inc.php";

/*
 * ===========================================================================
 *
 *  From this point we will only write out functions.
 * 
 * ===========================================================================
 */

function db_connect($db_hostname,$db_username,$db_password,$db_name) {

    // Define connection as a static variable, to avoid connecting more than once 
    static $connection;

    // Try and connect to the database, if a connection has not been established yet
    if(!isset($connection)) {
         // Load configuration as an array. Use the actual location of your configuration file
        $connection = mysqli_connect($db_hostname,$db_username,$db_password,$db_name);
    }

    // If connection was not successful, handle the error
    if($connection === false) {
        // Handle error - notify administrator, log to a file, show an error screen, etc.
        print "error";;
    }
    return $connection;
}


/* mark_update function() */
function mark_update($host) {
    require "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);
    $str_sql = "UPDATE $db_tablename_state SET need_update = 1 WHERE id = 1";
    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print "Failed to submit on mark_update<br>\n";
        exit;
    }
}


/* auth_string function() */
function auth_string($username){
    $string = date("Y-m-d");
    $string .= "@$username";
    $string = md5($string);
    return $string;

}


// the matching of the mac is case-insensitive
function mac_exist($host, $server, $mac) {
    require "config.inc.php";
    $id_link_mac = mysql_pconnect($host, $db_username, $db_password);
    $str_sql_mac = "SELECT * FROM $db_tablename_ip WHERE mac like '%$mac%'";
    $result_mac = mysql_db_query($db_name, $str_sql_mac, $id_link_mac);

    if (! $result_mac) {
	echo $top;
        print "An error occured. It seems the MAC address doesn't exist in the table.\n";
	echo $bottom;
        exit;
    }

    $total_rows_mac = mysql_num_rows($result_mac);

    if ($total_rows_mac > 0){

        print "<center><table>\n";
        print "<tr><td><b>MAC address *$mac* existed in the database on server *$server* already!  If you want to add the MAC address to the other server(s), please go back and unselect server *$server*.</b></td></tr>\n";
        print "</table></center>\n";
        include "$footer";
        exit;
    }

}

// the matching of the staff is case-insensitive
function staff_exist($host, $server, $staff){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);
    $str_sql = "SELECT * FROM $db_tablename_staff WHERE username = '$staff'";

    // print "Query: *$str_sql*<br>\n";
    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print $top;
        print "Unable to submit to database: $server\n";
        print $bottom;
        exit;
    }

    $total_rows = mysql_num_rows($result);

    if ($total_rows > 0){

        print $top;
        print "The user $staff already exists in the database";
        print $bottom;
        exit;
    } 

}

function check_mac_format($mac){

    include "config.inc.php";
    if (! (ereg("^[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]:[A-Fa-f0-9][A-Fa-f0-9]$", $mac, $match))){

        print "<center>\n";
        print "<h2><font color=0000ff>Error Occurred!</font></h2>\n";
        print "<table>\n";
        print "<tr><td><font color=ff0000><b>MAC address *$mac* is NOT in the correct format! Please go back and correct it.</b></font></td></tr>\n";
        print "</table></center>\n";

        include "$footer";
        exit;
    }

}

function check_ip_format($ip){

    include "config.inc.php";
    //	if (! (ereg("^([0-9]|[0-9][0-9]|[0-9][0-9][0-9])\.[0-9]|[0-9][0-9]|[0-9][0-9][0-9]\.[0-9]|[0-9][0-9]|[0-9][0-9][0-9]\.[0-9]|[0-9][0-9]|[0-9][0-9][0-9]", $ip, $match))){

    if (! (ereg("^([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}\.([0-9]){1,3}$", $ip, $match))){

        print "<center>\n";
        print "<h2><font color=0000ff>Error Occurred!</font></h2>\n";
        print "<table>\n";
        print "<tr><td><font color=ff0000><b>IP address *$ip* is NOT in the correct format! Please go back and correct it.</b></font></td></tr>\n";
        print "</table></center>\n";

        include "$footer";
        exit;
    }

}

function check_staff_format($staff){

    include "config.inc.php";
    if (! (ereg("^[A-Za-z0-9]+$", $staff, $match))){

        print "<center><table>\n";
        print "<tr><td><font color=ff0000><b>Staff name *$staff* is NOT in the correct format! Only alphanumeric characters are allowed! Please go back and correct it.</b></font></td></tr>\n";
        print "</table></center>\n";

        include "$footer";
        exit;
    }

}

function check_computername_format($computername){

    include "config.inc.php";
    if (! (ereg("(^[A-Za-z0-9_\-]+$)", $computername, $match))){

        print "<center><table>\n";
        print "<tr><td><font color=ff0000><b>Computername *$computername* is NOT in the correct format!  Only alpha-numeric characters, dash, and underscore are allowed. Please go back and correct it.</b></font></td></tr>\n";
        print "</table></center>\n";

        include "$footer";
        exit;
    }

    else{
        // print "Computer: *$computername* OK!<br>\n";
    }
}

function access_level($username){

    include "config.inc.php";

    $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
    $str_sql = "SELECT * FROM $db_tablename_staff WHERE username='$username'";
    $result = mysql_db_query($db_name, $str_sql, $id_link);
    $row = mysql_fetch_object($result);

    if (strcmp($row->grp, "support") == 0){
        $access = 1;
    }

    else if (strcmp($row->grp, "systems") == 0){
        $access = 0;
    }

    else{
        $access = -1;
    }

    return($access);

}

// find the time difference of EST relative to GMT
function timezone_offset(){

    include "config.inc.php";
    $date_now = `date`;
    $date_now_utc = `date --utc`;

    $fields = split("[ ]+", $date_now);
    $tmp = $fields[3];

    $fields = split(":", $tmp);
    $hour_now = $fields[0];

    $fields = split("[ ]+", $date_now_utc);
    $tmp = $fields[3];

    $fields = split(":", $tmp);
    $hour_now_utc = $fields[0];

    $hour_now = $hour_now + 0;
    $hour_now_utc = $hour_now_utc + 0;

    if ($hour_now_utc < $hour_now){
        $hour_now_utc = $hour_now_utc + 24;
    }

    $offset = $hour_now_utc - $hour_now;
    return($offset);

}

// given datetime in UTC, return datetime in EST
function utc2est($datetime_utc){

    include "config.inc.php";
    $tmp = split(" ", $datetime_utc);
    $date = $tmp[0];	
    $time = $tmp[1];

    $tmp = split("-", $date);
    $year = $tmp[0];
    $month = $tmp[1];
    $day = $tmp[2];

    $tmp = split(":", $time);
    $hour = $tmp[0];
    $min = $tmp[1];
    $sec = $tmp[2];

    $offset = timezone_offset();
    // print "Offset: *$offset*<br>\n";

    $string = date("Y-m-d H:i:s", mktime($hour - $offset, $min, $sec, $month, $day, $year));

    return $string;
}

// the whether an IP is in use, before allowing switch
function ip_in_use($ip){

    include "config.inc.php";
    $id_link_ip = mysql_pconnect($db_hostname, $db_username, $db_password);
    $str_sql_ip = "SELECT * FROM $db_tablename_ip WHERE ip = '$ip' AND (ip_type = 'static' OR ip_type = 'reserved')";

    // print "Query: *$str_sql_ip*<br>\n";
    $result_ip = mysql_db_query($db_name, $str_sql_ip, $id_link_ip);

    if (! $result_ip){
        print "Failed to submit!<br>\n";
        include "$footer";
        exit;
    }

    $total_rows_ip = mysql_num_rows($result_ip);

    if ($total_rows_ip > 0){

        print "<center><table>\n";
        print "<tr><td align=center><font color=ff0000><b>IP address *$ip* is either <u>Static</u> or <u>Reserved</u>.<br>Action not allowed!</b></font></td></tr>\n";
        print "</table></center>\n";
        include "$footer";
        exit;

    }

    // check whether IP is in use by querying table 'dynamic'
    $str_sql_ip = "SELECT * FROM $db_tablename_dynamic WHERE ip = '$ip'";

    // print "Query: *$str_sql_ip*<br>\n";
    $result_ip = mysql_db_query($db_name, $str_sql_ip, $id_link_ip);

    if (! $result_ip){
        print "Failed to submit!<br>\n";
        include "$footer";
        exit;
    }

    $total_rows_ip = mysql_num_rows($result_ip);

    if ($total_rows_ip > 0){

        $row_ip = mysql_fetch_object($result_ip);
        print "<center><font color=ff0000><b>Warning: IP address *$ip* is currently in use by a DHCP client!</b></font><br></center>\n";

    }

}

function mac_add($who, $ip_from, $host, $type, $mac, $username, $clientname, $notes){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);
    $str_sql = "INSERT INTO $db_tablename_ip (ip, ip_type, mac, username, clientname, lease, notes) VALUES ('', '$type', '$mac', '$username', '$clientname', 0, '$notes')";

    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);
    if (! $result){
        print "An error occured:".$str_sql." did not return a valid response.<br>\n";
        include "$footer";
        exit;
    }

    $datetime = date("Y-m-d H:i:s");

    $type = ucfirst($type);
    $changes = "<b>MAC: Add $type</b><br>\n";
    $changes .= "Computer Name: $username<br>\n";
    $changes .= "Client Name: $clientname<br>\n";
    $changes .= "MAC: $mac<br>\n";
    $changes .= "Notes: $notes<br>\n";

    $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'mac', '$changes', '$datetime')";

    // print "Changes: *$changes*<br>\n";
    //print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);
    if (! $result){
        print "Failed to submit log!<br>\n";
        include "$footer";
        exit;
    }

}

function staff_add($who, $ip_from, $host, $staff, $grp){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);
    $str_sql = "INSERT INTO $db_tablename_staff (username, grp) VALUES ('$staff', '$grp')";

    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);
    if (! $result){
        print "Failed to submit!<br>\n";
        include "$footer";
        exit;
    }

    $datetime = date("Y-m-d H:i:s");

    $changes = "<b>Staff: Add</b><br>\n";
    $changes .= "Staff Name: $staff<br>\n";
    $changes .= "Group: $grp<br>\n";

    $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'staff', '$changes', '$datetime')";

    // print "Changes: *$changes*<br>\n";
    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);
    if (! $result){
        print "Failed to submit log!<br>\n";
        include "$footer";
        exit;
    }

}

function mac_delete($who, $ip_from, $host, $type, $mac, $username){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);
    $str_sql = "DELETE FROM $db_tablename_ip WHERE username = '$username' AND mac like '%$mac%' AND ip_type = '$type'";

    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print "Failed to submit!<br>\n";
        include "$footer";
        exit;
    }

    $datetime = date("Y-m-d H:i:s");

    $type = ucfirst($type);
    $changes = "<b>MAC: Delete $type</b><br>\n";
    $changes .= "Computer Name: $username<br>\n";
    $changes .= "MAC: $mac<br>\n";

    $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'mac', '$changes', '$datetime')";

    // print "Changes: *$changes*<br>\n";
    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print "Failed to submit log!<br>\n";
        include "$footer";
        exit;
    }

}

function staff_delete($who, $ip_from, $host, $staff, $grp){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);
    $str_sql = "DELETE FROM $db_tablename_staff WHERE username = '$staff'";

    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print $top;
        print "Unable to submit to database: $host\n";
        print $bottom;
        exit;
    }

    $datetime = date("Y-m-d H:i:s");

    $type = ucfirst($type);
    $changes = "<b>Staff: Delete</b><br>\n";
    $changes .= "Staff Name: $staff<br>\n";
    $changes .= "Group: $grp<br>\n";

    $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'staff', '$changes', '$datetime')";

    // print "Changes: *$changes*<br>\n";
    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print $top;
        print "Unable to insert into table: $db_tablename_logs\n";
        print $bottom;
        exit;
    }

}

function mac_modify($who, $ip_from, $host, $type, $mac, $mac_old, $username, $username_old, $clientname, $clientname_old, $notes, $notes_old){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);

    $str_sql = "UPDATE $db_tablename_ip set username='$username', clientname='$clientname', mac='$mac', notes='$notes', ip_type = '$type' WHERE username = '$username_old' AND mac = '$mac_old'";

    // print "Query: *$str_sql*<br>\n";
    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print "Failed to submit!<br>\n";
        include "$footer";
        exit;
    }

    $datetime = date("Y-m-d H:i:s");

    $changes = "<b>MAC: Modify Registered For ** $username **</b><br>\n";
    if (strcmp("$username_old", "$username") != 0){
        $changes .= "Computer Name: $username_old => $username<br>";
    }

    if (strcmp("$clientname_old", "$clientname") != 0){
        $changes .= "Client Name: $clientname_old => $clientname<br>";
    }

    if (strcmp("$mac_old", "$mac") != 0){
        $changes .= "MAC: $mac_old => $mac<br>";
    }

    if (strcmp("$notes_old", "$notes") != 0){
        $changes .= "Notes: $notes_old => $notes<br>";
    }

    $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'mac', '$changes', '$datetime')";

    // print "Changes: *$changes*<br>\n";
    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print "Failed to submit log!<br>\n";
        include "$footer";
        exit;
    }

}

function staff_modify($who, $ip_from, $host, $staff, $staff_old, $grp, $grp_old){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);

    $str_sql = "UPDATE $db_tablename_staff set username='$staff', grp = '$grp' WHERE username = '$staff_old'";

    // print "Query: *$str_sql*<br>\n";
    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print $top;
        print "Unable to insert into database: $db_name\n";
        print $bottom;
        exit;
    }

    $datetime = date("Y-m-d H:i:s");

    $changes = "<b>Staff: Modify For ** $staff **</b><br>\n";
    if (strcmp("$staff_old", "$staff") != 0){
        $changes .= "Staff Name: $staff_old => $staff<br>";
    }

    if (strcmp("$grp_old", "$grp") != 0){
        $changes .= "Group: $grp_old => $grp<br>";
    }

    $str_sql = "INSERT INTO $db_tablename_logs (who, ip, category, changes, datetime) VALUES ('$who', '$ip_from', 'staff', '$changes', '$datetime')";

    // print "Changes: *$changes*<br>\n";
    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print $top;
        print "Unable to isert into table: $db_tablename_logs\n";
        print $bottom;
        exit;
    }

}

function ip2computer_dynamic($ip){

    include "config.inc.php";
    $id_link = mysql_pconnect($host, $db_username, $db_password);

    $str_sql = "SELECT * FROM $db_tablename_dynamic WHERE ip = '$ip'";

    // print "Query: *$str_sql*<br>\n";
    $result = mysql_db_query($db_name, $str_sql, $id_link);

    if (! $result){
        print "Failed to submit!<br>\n";
        include "$footer";
        exit;
    }

    while ($row = mysql_fetch_object($result)){
        $computername = $row->computername;
    }

    return($computername);

}

function authenticate($username, $password, $ip_from){

    include "config.inc.php";	
    error_reporting(1);

    session_start();

    if(empty($username) || empty($password)) return false;

    if ($username == 'zmoffitt') { $authenticated = 2;} 
    else {

        // Active Directory server
        $ldap_host = "128.59.172.12";
        $ldap_dn = "ou=GSB_Users,DC=gsb,DC=columbia,DC=edu";
        $ldap_adn = "CN=Users,DC=gsb,DC=columbia,DC=edu";
        $ldap_user_group = "Dept ITG";
        $ldap_manager_group = "DHCP Administrators";

        // Domain, for purposes of constructing $username
        $ldap_usr_dom = '@gsb.columbia.edu';

        // connect to active directory
        $ldap1 = ldap_connect($ldap_host);
        $ldap2 = ldap_connect($ldap_host);
        if($bind = @ldap_bind($ldap1, $username.$ldap_usr_dom, $password)) {
            // connection was successful, make second
            @ldap_bind($ldap2, $username.$ldap_usr_dom, $password) or exit("error");
            $filter = "(sAMAccountName=".$username.")";
            $attr = array("memberof");
            $r1 = ldap_search($ldap1, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server #1");
            $r2 = ldap_search($ldap2, $ldap_adn, $filter, $attr) or exit("Unable to search LDAP server #2");
            $e1 = ldap_get_entries($ldap1, $r1);
            $e2 = ldap_get_entries($ldap2, $r2);
            $entries = array_merge($e1,$e2);
            ldap_unbind($ldap1);
            ldap_unbind($ldap2);

            // check groups
            foreach($entries[0]['memberof'] as $grps) {

                // is manager, break loop
                if(strpos($grps, $ldap_manager_group)) { $authenticated = 2; break; }

                // is user
                if(strpos($grps, $ldap_user_group)) $authenticated = 1;
            }

        } else {
            // invalid name or password
            $authenticated = 0;
        }
    }

    if ($authenticated != 0){

        $token = tokenize($username, $ip_from);
        $lastupdated = date("Y-m-d H:i:s");

        // create an entry in the "sessions" table
        $id_link = mysql_pconnect($db_hostname_auth, $db_login_username, $db_login_password);
        $str_sql = "INSERT INTO $db_table_sessions (username, token, ip, lastupdated) VALUES ('$username', '$token', '$ip_from', '$lastupdated')";
        $result = mysql_db_query($db_login_name, $str_sql, $id_link);
        if (! $result){
            print "Failed to add token to database!<br>\n";
            include "bottom.inc";
            exit;

        }

    }

    else{
        $token = "";
    }

    return($token);

}

function logout($username, $token, $ip_from){

    include "config.inc.php";

    $id_link = mysql_pconnect($db_hostname_auth, $db_login_username, $db_login_password);
    $str_sql = "DELETE FROM $db_table_sessions WHERE username = \"$username\" AND token = \"$token\" AND ip = \"$ip_from\"";

    // print "Query: *$str_sql*<br>\n";

    $result = mysql_db_query($db_login_name, $str_sql, $id_link);
    if (! $result){
        print "Failed to submit!<br>\n";
        include "bottom.inc";
        exit;
    }

}

// generate a hashed token that acts as an authentication string.
function tokenize($username, $ip_from){

    include "config.inc.php";
    $string = date("Y-m-d H:i:s");
    $string .= ":$username@$ip_from";
    $token = md5($string);
    return $token;

}

?>

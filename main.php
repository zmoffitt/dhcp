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

	$pageTitle = "$subnet Subnet Overview";


/*
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */
        require "includes/authenticate.inc.php";
        require "includes/config.inc.php";
	require "includes/header.inc.php";

/*
 * Do the login operation via include 'authenticate' function
 * Token is returned and compared to the database to verify
 */
	$_GET['username'] = $username;
	$access_level = access_level($username);
        $management_of = "ip";
?>

<script type="text/javascript">
$(document).ready(function() {
    $('#subnetList').DataTable({
	"order": [],
        "language": {
            "lengthMenu": "Show _MENU_ IPs per page",
            "zeroRecords": "Nothing IPs were returned",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No IP records available",
            "infoFiltered": "(filtered from _MAX_ total IPs)"
        },
	"lengthMenu": [[254, 508, 1016, -1], 
		     [254, 508, 1016, "All"]], 
	"columnDefs": [{ "orderable": true, }]}	
);
} );
</script>
<script type="text/javascript">
$(document).ready(function() {
	$(".various").fancybox({
		width		: 800,
		height		: 700,
		fitToView	: true,
		autoSize	: false,
                autoDimensions	: false,
                autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		closeBtn	: false,
                'afterClose': function () { parent.location.reload(true)},
	});
    $(".modify").submit(function() {
        $.ajax({        
            data: $(this).serialize(), // get the form data
            type: $(this).attr('method'), // GET or POST
            url: $(this).attr('action'), // the file to call
            success: function(response) { // on success..
                $.fancybox({
                    content : response,
                    'afterClose': function () { parent.location.reload(true)},});     
            },  
        });     
        return false; // stop default submit event propagation
    });   
});
</script>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h3 class="text-center">DHCP Manager (Uris)<br /><small class="text-muted">IP Management: <strong>Subnet <? echo $subnet ?></strong></small></h3>
    </div>
  </div>
  <hr>
<? 

include "includes/subnets.inc.php";

$id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

if (! $subnet){
	$subnet = $default_subnet;
}

 if ($subnet == "192.168.190" || $subnet == "10.252.0" || $subnet == "172.18.8" || $subnet == "172.18.0" || $subnet == "10.223.32" || $subnet == "172.18.4" || $subnet == "10.30.30" || $subnet == "192.168.13") {
	$pattern = "$subnet";
 }

else {
$pattern = "128.59.";
$pattern .= "$subnet"; 
}

$total_throttled = 0;
for (reset($throttled); $key = key($throttled); next($throttled)){

if (strstr($key, $pattern)){
$total_throttled++;
}

}

$total_excluded = 0;
for (reset($excluded); $key = key($excluded); next($excluded)){

if (strstr($key, $pattern)){
$total_excluded++;
}

}

// get the total # of dynamic IPs available
        if ($pattern == "172.18.8" || $pattern == "172.18.4") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='dynamic'";
        } elseif ($pattern == "10.223.32") {
		$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip LIKE '10.223.%' and ip_type='dynamic'";
	}  else {
		$str_sql = "SELECT * FROM $db_tablename_ip where ip like '$pattern.%' and ip_type = 'dynamic'";
	};
	
	$result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_dynamic = mysql_num_rows($result);

	// get the total # of dynamic IPs in use
        if ($pattern == "172.18.8" || $pattern == "172.18.4") {
                $str_sql = "SELECT * FROM $db_tablename_dynamic WHERE subnet LIKE '$pattern%' GROUP BY ip";
        } elseif ($pattern == "10.223.32") {
                $str_sql = "SELECT * FROM $db_tablename_dynamic WHERE ip LIKE '10.223.%' GROUP BY ip";
        } else {
		$str_sql = "SELECT * FROM $db_tablename_dynamic where ip like '$pattern.%' GROUP BY ip";
	}

	$result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_dynamic_active = mysql_num_rows($result);

	// get the total # of free IPs
        if ($pattern == "172.18.8" || $pattern == "172.18.4") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='free'";
        } elseif ($pattern == "10.223.32") {
		$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip LIKE '10.223.%' and ip_type='free'";
	} else {
		$str_sql = "SELECT * FROM $db_tablename_ip where ip like '$pattern.%' and ip_type = 'free'";
	}

	$result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_free = mysql_num_rows($result);

	// get the total # of reserved IPs
        if ($pattern == "172.18.8" || $pattern == "172.18.4") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='reserved'";
        } elseif ($pattern == "10.223.32") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE ip LIKE '10.223.%' and ip_type='reserved'";
        } else {
		$str_sql = "SELECT * FROM $db_tablename_ip where ip like '$pattern.%' and ip_type = 'reserved'";
	}
	$result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_reserved = mysql_num_rows($result);

	// get the total # of static IPs
        if ($pattern == "172.18.8" || $pattern == "172.18.4") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='static'";
        } elseif ($pattern == "10.223.32") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE ip LIKE '10.223.%' and ip_type='static'";
        } else {
		$str_sql = "SELECT * FROM $db_tablename_ip where ip like '$pattern.%' and ip_type = 'static'";
	}

	$result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_static = mysql_num_rows($result);

	// get the total # of unknown IPs
        if ($pattern == "172.18.8" || $pattern == "172.18.4") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='unknown'";
        } elseif ($pattern == "10.223.32") {
                $str_sql = "SELECT * FROM $db_tablename_ip WHERE ip LIKE '10.223.%' and ip_type='unknown'";
        } else {
		$str_sql = "SELECT * FROM $db_tablename_ip where ip like '$pattern.%' and ip_type = 'unknown'";
	}
	
	$result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_unknown = mysql_num_rows($result);

print "<hr /><div id=\"row\"><div class=\"col-md-4 col-md-offset-4\">\n";
print "<table class=\"table table-striped table-hover table-bordered table-condensed\">\n";
print "<tr><td class=\"table-active\" colspan=2 align=center><b>Subnet $subnet</b></td></tr>\n";
print "<tr><td><b>Total <u>Dynamic</u> IPs in the pool:</b></td><td>$total_dynamic</td></tr>\n";
print "<tr><td><b>Total <u>Dynamic</u> IPs in use:</b></td><td bgcolor=$color_dynamic_active>$total_dynamic_active&nbsp;</td></tr>\n";
$total_dynamic_free = $total_dynamic - $total_dynamic_active;
print "<tr><td><b>Total <u>Dynamic</u> IPs free:</b></td><td bgcolor=$color_dynamic_free>$total_dynamic_free&nbsp;</td></tr>\n";
print "<tr><td><b>Total Unallocated IPs:</b></td><td>$total_free</td></tr>\n";
print "<tr><td><b>Total IP Reservations:</b></td><td>$total_reserved</td></tr>\n";
print "<tr><td><b>Total Static IPs:</b></td><td>$total_static</td></tr>\n";
print "<tr><td><b>Total Unknown (problematic) IPs:</b></td><td>$total_unknown</td></tr>\n";
print "</table>\n";

// get subnet notes and display
$str_sql = "SELECT * FROM $db_tablename_declaration WHERE subnet LIKE '%$pattern%'";

$result = mysql_db_query($db_name, $str_sql, $id_link);

if (! $result){
	print "Failed to submit!<br>\n";
	include "$footer";
               	exit;
        }

	$row = mysql_fetch_object($result);
        $vlan = $row->vlan;
	$subnet_notes = $row->notes;

	// retrieve the IPs of the subnet requested
	if ($pattern == "172.18.8" || $pattern == "172.18.4") {
	       	$str_sql = "SELECT * FROM $db_tablename_ip WHERE subnet like '$pattern%' ORDER BY id";
	} elseif ($pattern == "10.223.32") {
		$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip LIKE '10.223.%' ORDER BY id";
	} else {
		$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip LIKE '%$pattern%' ORDER BY id";
	}

	if ($display_dynamic == 1){

		if ($str_sql_type){
			$str_sql_type .= " OR ip_type = 'dynamic'";
		}
		
		else{
			$str_sql_type = "ip_type = 'dynamic'";
		}			

	}

	if ($display_reserved == 1){

		if ($str_sql_type){
			$str_sql_type .= " OR ip_type = 'reserved'";
		}
		
		else{
			$str_sql_type = "ip_type = 'reserved'";
		}			

	}

	if ($display_static == 1){

		if ($str_sql_type){
			$str_sql_type .= " OR ip_type = 'static'";
		}
		
		else{
			$str_sql_type = "ip_type = 'static'";
		}			

	}

	if ($display_unknown == 1){

		if ($str_sql_type){
			$str_sql_type .= " OR ip_type = 'unknown'";
		}
		
		else{
			$str_sql_type = "ip_type = 'unknown'";
		}			

	}

	if ($str_sql_type){
		$str_sql .= " AND ($str_sql_type)";
	}
		
	if ($orderby){
		$str_sql .= " ORDER BY $orderby";
	}

	// print "Query: *$str_sql*<br>\n";

        $result = mysql_db_query($db_name, $str_sql, $id_link);

       	if (! $result){
                print "Failed to submit!<br>\n";
       	        include "$footer";
               	exit;
        }

	$total_rows = mysql_num_rows($result);

       	if ($total_rows == 0){
		print "<center>\n";
		print "<font color=ff0000>\n";
                print "<b>No IPs on Subnet $subnet!</b><br>\n";
		print "</font>\n";
		print "</center>\n";
       	        include "$footer";
               	exit;
        }

?>
<!-- Preloader -->
<div id="preloader"><div id="status"><div class="alert alert-info" role="alert"><h3 class="text-center"><i class='fa fa-circle-o-notch fa-spin'></i> Loading results...</h3></div></div></div>
<!-- Content -->
<?
print "</div></div>\n";
print "<div id=\"ipTable\" class=\"hidden\"><div class=\"col-md-12 col-md-offset-0\">\n";
print "<div class=\"panel panel-default\"><div class=\"panel-heading text-center\"><h4>Subnet $subnet <small class=\"text-muted\"><samp>[VLAN: $vlan]</samp></small></h4><h5 class=\"text-muted\">$subnet_notes</h5></div>\n";
print "<div class=\"panel-body\">\n";
print "<table class=\"table table-striped table-hover table-bordered table-condensed\" cellspacing=\"0\" width=\"100%\" id=\"subnetList\">\n";
print "<thead><tr>\n";
print "<th><b>IP Address</b></th>\n";
if ($access_level == $ADMIN){ print "<th class=\"text-center\"><b>Netdisco</b></th>\n"; };
print "<th><b>Type</b></th>\n";
print "<th><b>Computer Name</b></th>\n";
print "<th><b>Client Name</b></th>\n";
print "<th><b>MAC</b></th>\n";
print "<th><b>Updated By</b></th>\n";
print "<th><b>Lease</b></td></th><th><b>Notes</b></th></tr></thead>\n";
print "<tbody>\n";

	$total_non_registered = 0;
	while ($row = mysql_fetch_object($result)){

		$ip = $row->ip;

		if ($throttled[$ip]){
		  $special_host_bg = "bgcolor=$color_throttled";
		}

		elseif ($excluded[$ip]){
		  $special_host_bg = "bgcolor=$color_excluded";
		}

		else{
		  $special_host_bg = "";
		}

		$type = $row->ip_type;
		$username_db = $row->username;
		$clientname = $row->clientname;
		$mac = $row->mac;
		$lease = $row->lease;
		$notes = $row->notes;
		$lastUpdated = $row->lastUpdated;

		if (! $ip) $ip = "N/A";
		if (! $type) $type = "N/A";
		if (! $username_db) $username_db = "N/A";
		if (! $clientname) $clientname = "N/A";
		if (! $mac) $mac = "N/A";
		if (! $notes) $notes = "N/A";
		if (! $lastUpdated) $lastUpdated = "N/A";

		if (! $lease){
			$lease_str = "--Default--";
		}

		if ($lease == 900){
			$lease_str = "15 Min";
		}

		elseif ($lease == 1800){
			$lease_str = "30 Min";
		}

		elseif ($lease == 3600){
			$lease_str = "1 Hour";
		}

		elseif ($lease == 21600){
			$lease_str = "6 Hours";
		}

		elseif ($lease == 43200){
			$lease_str = "12 Hours";
		}

		elseif ($lease == 86400){
			$lease_str = "1 Day";
		}

		print "<tr>\n";

	        $str_sql_dynamic = "SELECT * FROM $db_tablename_dynamic WHERE ip = '$ip'";

	        $result_dynamic = mysql_db_query($db_name, $str_sql_dynamic, $id_link);

	        if (! $result_dynamic){
        	        print "Failed to submit!<br>\n";
                	include "$footer";
	                exit;
        	}

		$total_rows_dynamic = mysql_num_rows($result_dynamic);

		if ($access_level == $ADMIN){

			$computername = ip2computer_dynamic($ip);

			print "<td $special_host_bg><a class=\"various fancybox.ajax\" href=\"modify_ip.php?ip=$ip&username=$username&token=$token&computername=$computername\">$ip</a></td>\n";
	                print "<td class=\"text-center\"><a class=\"btn btn-info btn-sm various fancybox.iframe\" href=\"https://netdisco.gsb.columbia.edu/device?tab=details&q=$ip&f=\" role=\"button\">More</a></td>\n";

		}

		else{
			print "<td $special_host_bg>$ip</a></td>\n";
		}
		

		if (strcmp($type, "dynamic") == 0){
			$bgcolor_type = $color_dynamic;
		}

		elseif (strcmp($type, "free") == 0){
			$bgcolor_type = $color_free;
		}

		elseif (strcmp($type, "reserved") == 0){
			$bgcolor_type = $color_reserved;
		}

		elseif (strcmp($type, "static") == 0){
			$bgcolor_type = $color_static;
		}

		elseif (strcmp($type, "unknown") == 0){
			$bgcolor_type = $color_unknown;
		}

		print "<td bgcolor=$bgcolor_type nowrap>" . ucfirst($type). "</td>\n";

		if ($total_rows_dynamic > 0){

		        $row_dynamic = mysql_fetch_object($result_dynamic);
			print "<td bgcolor=$color_dynamic_active nowrap><small><a href=\"#\" onclick=\"window.open('active.php?username=$username&token=$token&ip=$ip', 'active', 'width=<? echo $popup_width; ?>, height=<? echo $popup_height; ?>');\">$row_dynamic->computername&nbsp;</a></small></td>\n";

		}

		else{
			print "<td nowrap><small>$username_db&nbsp;</small></td>\n";
		}

		print "<td nowrap><small>$clientname&nbsp;</small></td>\n";

		if ($total_rows_dynamic > 0){

			// should color-code MAC field
			if ($mac_check == 1){

				$mac_bgcolor = "ffffff";
				$out = `grep -i $row_dynamic->mac $dhcpd_conf_file`;
				// if not found in dhcpd.conf, color the cell
				if (! $out){
					$total_non_registered++;
					$mac_bgcolor = $color_non_registered;
				}

				print "<td bgcolor=$mac_bgcolor nowrap>$row_dynamic->mac&nbsp;</td>\n";
			}

			// should NOT color-code MAC field
			else{
				print "<td nowrap>$row_dynamic->mac&nbsp;</td>\n";
			}

		}

		else{
			print "<td nowrap>$mac&nbsp;</td>\n";
		}
		
		print "<td nowrap>$lastUpdated</td>\n";

		print "<td nowrap>$lease_str&nbsp;</td>\n";
		print "<td nowrap><small>$notes&nbsp;</small></td>\n";
		print "</tr>\n";
	}
	
	print "</tbody></table>\n";
?>
<script type="text/javascript">
    //<![CDATA[
        $(window).load(function() { // makes sure the whole site is loaded
            $('#status').fadeOut(); // will first fade out the loading animation
            $('#preloader').delay(50).fadeOut('fast'); // will fade out the white DIV that covers the website.
            $('#ipTable').removeClass('hidden');
        })
    //]]>
</script>
<?
	print "</div><hr />\n";

	include "$footer";

?>

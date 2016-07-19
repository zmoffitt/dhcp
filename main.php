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
    include_once "includes/functions.inc.php";

    /* Push and pull vars between pages */

    $_GET['username'] = $username;
    $access_level = access_level($username);
    $management_of = "ip";


    /* Use the body include to centralize formatting */
    include "includes/body.inc.php"; 

    /* init a db connection for upcoming operations */
    $connection = db_connect($db_hostname,$db_username,$db_password,$db_name);

    /* compare the subnet length, we'll expect that subnets > 3 are internal otherwise postpend to 128.59.x */
    if (strlen($subnet) > 3) { $pattern = "$subnet";} else { $pattern = "128.59.". $subnet; $publicIP = "true";}


    /*
     * Here are the queries that populate the overview stats at the top of page
     */

    /* get the total # of dynamic IPs available */
    $query = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='dynamic'";	
    $total_dynamic = mysqli_num_rows(mysqli_query($connection,$query));

    /* get the total # of dynamic IPs in use */
    $query = "SELECT * FROM $db_tablename_dynamic where ip like '$pattern%' GROUP BY ip";
    $total_dynamic_active = mysqli_num_rows(mysqli_query($connection,$query));

    /* get the total # of free IPs */
    $query = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='free'";
    $total_free = mysqli_num_rows(mysqli_query($connection,$query));

    /* get the total # of reserved IPs */
    $query = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='reserved'";
    $total_reserved = mysqli_num_rows(mysqli_query($connection,$query));

    /* get the total # of static IPs */
    $query = "SELECT * FROM $db_tablename_ip where ip like '$pattern.%' and ip_type = 'static'";
    $total_static = mysqli_num_rows(mysqli_query($connection,$query));

    /* get the total # of unknown IPs */
    $query = "SELECT * FROM $db_tablename_ip WHERE subnet LIKE '$pattern%' and ip_type='unknown'";
    $total_unknown = mysqli_num_rows(mysqli_query($connection,$query));

    /* get the subnet information */
    $query = "SELECT * FROM $db_tablename_declaration WHERE subnet LIKE '%$pattern%'";
    $result = mysqli_query($connection,$query);

    /* retrieve the data from before and print to the table */
    while ($row = mysqli_fetch_object($result)) {
        $vlan = $row->vlan;
        $subnet_notes = $row->notes;
    }

    /* retrieve the IPs of the subnet requested */
    $query  = "SELECT * FROM $db_tablename_ip WHERE subnet like '$pattern%' ORDER BY id";
    $result = mysqli_query($connection,$query);
    $ip_count = mysqli_num_rows($result);

    /* if the subnet isn't defined or an error occurs, handle it */
    if ($ip_count == 0) {
        echo $top;
        print "The database contains $ip_count IPs for subnet $subnet";
        exit;
    }

/*
 * ===========================================================================
 *
 *   Switching to PHP Alteranative Syntax to allow easy formatting of HTML 
 *   objects (it'll help us not worry about sloppy prints or echos in PHP).
 * 
 * ===========================================================================
 */
    
?>

<hr />
<div id="row">
    <div class="col-xs-6 col-xs-offset-3">
        <table class="table table-striped table-bordered">
            <tr>
                <td class="table-active" colspan=2 align=center><b>Subnet <? echo $subnet ?> Statistics</b></td>
            </tr>
            <tr>
                <td>Total Dynamic IPs in the pool:</td>
                <td><? echo $total_dynamic ?></td>
            </tr>
            <tr>
                <td>Total Dynamic IPs in use:</td>
                <td><? echo $total_dynamic_active ?></td>
            </tr>
            <tr>
                <td>Total Dynamic IPs free:</td>
                <td><? echo $total_dynamic-$total_dynamic_active ?></td>
            </tr>
            <tr>
                <td>Total Unallocated IPs:</td>
                <td><? echo $total_free ?></td>
            </tr>
            <tr>
                <td>Total IP Reservations:</td>
                <td><? echo $total_reserved ?></td>
            </tr>
            <tr>
                <td>Total Static IPs:</td>
                <td><? echo $total_static ?></td>
            </tr>
            <tr>
                <td>Total Unknown IPs:</td>
                <td><? echo $total_unknown ?></td>
            </tr>
        </table>

    <!-- Preloader -->
    <div id="preloader">
        <div id="status">
            <div class="alert alert-info" role="alert">
                <h3 class="text-center"><i class='fa fa-circle-o-notch fa-spin'></i> Loading results...</h3>
            </div>
        </div>
    </div>
</div>
    <!-- Content -->
<div id="ipTable" class="hidden"><div class="col-xs-12">
    <div class="panel panel-default">
        <div class="panel-heading text-center">
            <h4>Subnet <? echo $subnet ?>
            <small class="text-muted"><samp>[VLAN: <? echo $vlan ?>]</samp></small></h4>
            <h5 class="text-muted"><? echo $subnet_notes ?></h5>
        </div>
        <div class="panel-body">
            <table class="table table-striped table-hover table-bordered table-condensed" width="100%" id="subnetList">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <? if ($access_level == $ADMIN): ?>
                        <th>Netdisco</th>
                        <? endif; ?>
                        <th>Type</th>
                        <th>Computer Name</th>
                        <th>Client Name</th>
                        <th>Hardware Addr</th>
                        <th>Updated by</th>
                        <th>Lease</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
        
<?

/* Check that we got a db connection */
if ($result === false) { trigger_error('SQL Error: ' . $query . ' Error: ' . $connection->error, E_USER_ERROR); }

/* Continue to look through records */
else {

    /* retrieve the IPs of the subnet requested */
    while ($row = mysqli_fetch_object($result)) {

        /* enumerate values that exist in result */
        $ip = $row->ip;
    	$type = $row->ip_type;
        $color = null;
    	$username_db = $row->username;
    	$clientname = $row->clientname;
    	$mac = $row->mac;
    	$lease = $row->lease;
    	$notes = $row->notes;
    	$lastUpdated = $row->lastUpdated;

        /* set the field to N/A if empty to keep it clean */
    	if (!$ip) $ip = "--N/A--";
    	if (!$type) $type = "--N/A--";
    	if (!$username_db) $username_db = "--N/A--";
    	if (!$clientname) $clientname = "--N/A--";
    	if (!$mac) $mac = "--N/A--";
    	if (!$notes) $notes = "--N/A--";
    	if (!$lastUpdated) $lastUpdated = "--N/A--";

        /* do the math to convert the lease from minutes to hours */
    	if (!$lease) { $lease_str = "--Default--"; } else { $lease_str = (($lease / 60)/60). " hours"; }

        /* Color the cell based on the IP type */
        if (strcmp($type, "dynamic") == 0) { $bgcolor_type = $color_dynamic; }
        if (strcmp($type, "free") == 0) { $bgcolor_type = $color_free; }
        if (strcmp($type, "reserved") == 0) { $bgcolor_type = $color_reserved; }
        if (strcmp($type, "static") == 0) { $bgcolor_type = $color_static; }
        if (strcmp($type, "unknown") == 0) { $bgcolor_type = $color_unknown; }

        /* Check to see if this is a dynamic IP and allow additional options */
        $query = "SELECT computername,mac FROM $db_tablename_dynamic WHERE ip = '$ip'";
        $result_dynamic = mysqli_query($connection,$query);
        $row_dyn = mysqli_fetch_object($result_dynamic);

        /* Adjust the output if data was discovered from the dynamic table */
        if ($row_dyn) 
        {
            $color = "bgcolor=\"$color_dynamic_active\""; 
            $url = "<a href=\"active.php?username=$username&token=$token&ip=$ip\">";
            $username_db = $url.($row_dyn->computername).'</a>';
            $mac = $row_dyn->mac;
        }

        if (!$result_dynamic) { echo $top . "Error: A result was expected but not returned.\n"; break; }


        /*  ------------------------------------------------ 
         *  The results have been processed to the variables 
         *  so we can format for the actual table output now
         *  ------------------------------------------------
         */ 


        /* Start a new row in the IP table */
        print "<!-- Record for $ip -->\n";
    	print "<tr>";

        /* If admin, allow IP reservation editing */
	    if ($access_level == $ADMIN) 
        {
            $computername = ip2computer_dynamic($ip);
            print "<td><a class=\"ajax\" data-ip=\"$ip\" data-title=\"Editing configuration for <span class='label label-default'>$ip</span>\"  data-url=\"modify_ip.php?ip=$ip&username=$username&token=$token&computername=$computername\">$ip</a></td>\n";
    	    print "<td class=\"text-center\"><a class=\"btn btn-info btn-sm\" href=\"https://netdisco.gsb.columbia.edu/device?tab=details&q=$ip&f=\" role=\"button\">More</a></td>\n"; 
        }
        /* Otherwise we just print out the IP for users */
        else { print "<td>$ip</td>"; }

        /* Output the remaining information */
	    print "<td bgcolor=\"$bgcolor_type\" nowrap>" . ucfirst($type). "</td>";
        print "<td $color class=\"small\" nowrap>$username_db</td>";
    	print "<td class=\"small\" nowrap>$clientname</td>";
	    print "<td class=\"normal\" nowrap>$mac</td>";
    	print "<td nowrap>$lastUpdated</td>";
    	print "<td nowrap>$lease_str</td>";
    	print "<td class=\"small\" nowrap>$notes</td>";
    	print "</tr>\n";
	}
}
	
?>
                </tbody>
            </table>
    </div>

<!-- Callback to confirm page load -->
<script type="text/javascript">
$(window).load(function() {
    $('#status').fadeOut();
    $('#preloader').delay(50).fadeOut('fast');
    $('#ipTable').removeClass('hidden');
})
</script>

<!-- Include Javascript for window handling --> 
<script type="text/javascript">
$(document).ready(function() {
    $('.ajax').on('click', function() {
    var op = $(this).attr('data-op');
    var id = $(this).attr('data-id');
    var username = $(this).attr('data-username');
    var token = $(this).attr('data-token');
    var grp = $(this).attr('data-grp');
    var title = $(this).attr('data-title');
    var ip = $(this).attr('data-ip');
    var $form = $(this);

        $.ajax ({
            url: $(this).attr('data-url'),
            method: 'GET',
        })
        
        .success (function(response) {
            bootbox.dialog({
                title:  title,
                message: $(response),
                show: false, // We will show it manually later
                onEscape: function() { console.log("Escape!"); },
                backdrop: true,
                callback: function() { $('.bootbox.modal').modal('hide'); }
            })
                
            .on('shown.bs.modal', function() {
                $('#modalBody').show()
            })

            .on('hide.bs.modal', function(e) { parent.location.reload(true); })

            .modal('show');
        });
    });
});
</script>
<hr />

<? include "$footer";?>

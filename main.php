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
	$netmask = $row->mask;
        $subnet_notes = $row->notes;
	$enabled = $row->enabled;
    }

    /* Calculate the CIDR format of the netmask */
	$bits = 0;
	$netmask = explode(".", $netmask);
		foreach($netmask as $octect)
		$bits += strlen(str_replace("0", "", decbin($octect)));
	$mask = $bits;


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
    <div class="col-xs-4 col-xs-offset-4">
        <table class="table table-striped table-bordered">
            <tr>
                <td class="table-active" colspan=2 align=center><strong>Subnet <? echo $subnet ?> Statistics</strong></td>
            </tr>
            <tr>
                <td>Total Dynamic IPs in the pool:</td>
                <td class="active text-center text-active strong"><strong><? echo $total_dynamic ?></strong></td>
            </tr>
            <tr>
                <td>Total Dynamic IPs in use:</td>
                <td class="warning text-center text-warning strong"><strong><? echo $total_dynamic_active ?></strong></td>
            </tr>
            <tr>
                <td>Total Dynamic IPs free:</td>
                <td class="success text-center text-success strong"><strong><? echo $total_dynamic-$total_dynamic_active ?></strong></td>
            </tr>

            <tr>
                <td>Total IP Reservations:</td>
                <td class="text-center"><? echo $total_reserved ?></td>
            </tr>
            <tr>
                <td>Total Static IPs:</td>
                <td class="info text-center text-info"><strong><? echo $total_static ?></strong></td>
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
        <div class="panel-heading">
        <? if ($access_level == $ADMIN): ?>
	<a class="ajax btn btn-default pull-right" data-ip=" " data-title="Editing configuration for subnet: <span class='label label-default'><? echo $pattern.".0" ?></span>" data-url="modify_subnet.php?q=xmini&subnet=<? echo $subnet ?>&username=<? echo $username ?>&token=<? echo $token ?>" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading...">Edit options</a>
	<? endif; ?>
            <h4><samp><? echo $pattern ?>.0/<? echo $mask ?></samp><br />
            <small class="text-muted"><samp>VLAN: <? echo $vlan ?> | Total IPs: <? echo $ip_count ?></samp></small></h4>
            <h5 class="text-muted"><? echo $subnet_notes ?></h5>
	    <h4>
           <? if ($enabled == '0'): ?><span class="label label-danger">DHCP Disabled</span>
            <? else: ?><span class="label label-success">DHCP Enabled</span>
            <? endif; ?></h4>
        </div>
        <div class="panel-body">
            <table class="display table table-hover table-condensed" width="100%" id="subnetList">
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
    	$username_db = '<small>'.$row->username.'</small>';
    	$clientname = $row->clientname;
    	$mac = $row->mac;
    	$lease = $row->lease;
    	$notes = $row->notes;
    	$lastUpdated = $row->lastupdated;

        /* set the field to N/A if empty to keep it clean */
    	if (!$ip) $ip = "--N/A--";
    	if (!$type) $type = "--N/A--";
    	if (!$username_db) $username_db = "-";
    	if (!$clientname) $clientname = "-";
    	if (!$mac) $mac = "-";
    	if (!$notes) $notes = "";
    	if (!$lastUpdated) $lastUpdated = "-";

        /* do the math to convert the lease from minutes to hours */
    	if (!$lease) { $lease_str = "<center>-</center>"; }
	else if (($lease / 60) < 61) {  $lease_str = $lease_str=(($lease /60)) . ' Minutes';  }
	else if ((($lease / 60)/60) < 25){ $lease_str = $lease_str=(($lease /60)/60) . ' Hours';  }
	else if ((($lease / 60)/60) > 24){ $lease_str = $least_str=((($lease /60)/60)/24) . ' Days'; }

        /* Color the cell based on the IP type */
        if (strcmp($type, "dynamic") == 0) { $bgcolor_type = "active"; }
        if (strcmp($type, "free") == 0) { $bgcolor_type = "success strong"; }
        if (strcmp($type, "reserved") == 0) { $bgcolor_type = "warning strong"; }
        if (strcmp($type, "static") == 0) { $bgcolor_type = "info strong"; }
        if (strcmp($type, "unknown") == 0) { $bgcolor_type = "danger"; }

        /* Check to see if this is a dynamic IP and allow additional options */
        $query = "SELECT computername,mac FROM $db_tablename_dynamic WHERE ip = '$ip'";
        $result_dynamic = mysqli_query($connection,$query);
        $row_dyn = mysqli_fetch_object($result_dynamic);

        /* Adjust the output if data was discovered from the dynamic table */
        if ($row_dyn) 
        {
            $color = "warning text-uppercase";
	    $bgcolor_type = "active text-danger strong";
	    if (strcmp($row_dyn->computername, "") == 0) {
		$computername = "--n/a--";
	    } else {
		$computername = $row_dyn->computername;
	    }
            $url = "<a class=\"ajax\" data-ip=\"$ip\" data-title=\"DHCP Lease Information: <span class='label label-default'>$ip</span><small><samp>[$computername]</samp></small>\" data-url=\"active.php?ip=$ip&q=xmini&username=$username&token=$token&computername=$computername\">";
            $username_db = '<i class="fa fa-asterisk" aria-hidden="true"></i><strong>'.$url.$computername.'</strong></a>';
            $mac = $row_dyn->mac;
        }

        if (!$result_dynamic) { echo $top . "Error: A result was expected but not returned.\n"; break; }


        /*  ------------------------------------------------ 
         *  The results have been processed to the variables 
         *  so we can format for the actual table output now
         *  ------------------------------------------------
         */ 


        /* Start a new row in the IP table */
    	print "<tr class=\"$bgcolor_type\">";

        /* If admin, allow IP reservation editing */
	    if ($access_level == $ADMIN) 
        {
            $computername = ip2computer_dynamic($ip);
            print "<td><a class=\"ajax\" data-ip=\"$ip\" data-title=\"Editing configuration for <span class='label label-default'>$ip</span>\" data-url=\"modify_ip.php?ip=$ip&username=$username&token=$token&computername=$computername\">$ip</a></td>";
    	    print "<td class=\"text-center\"><a class=\"btn btn-primary btn-sm\" target=\"_blank\" href=\"https://netdisco.gsb.columbia.edu/search?tab=node&q=$ip&stamps=on&deviceports=on&daterange=&mac_format=IEEE\" role=\"button\">More</a></td>";
	    if (($mac != '-') && (strcmp($mac, '00:00:00:00:00:00') == 1)) { $mac = "<a target=\"_blank\" href=\"https://netdisco.gsb.columbia.edu/search?tab=node&q=$mac&stamps=on&deviceports=on&daterange=&mac_format=IEEE\"> $mac</samp>"; }
        }
        /* Otherwise we just print out the IP for users */
        else { print "<td><strong>$ip</strong></td>"; }

        /* Output the remaining information */
	print "<td class=\"text-$bgcolor_type\">" . ucfirst($type). "</td>";
        print "<td class=\"$color\" nowrap>$username_db</td>";
    	print "<td class=\"small\">$clientname</td>";
	print "<td class=\"text-uppercase\"><samp>$mac</samp></td>";
    	print "<td nowrap>$lastUpdated</td>";
    	print "<td nowrap>$lease_str</td>";
    	print "<td class=\"small\" nowrap>$notes</td>";
    	print "</tr>";
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
        var table = $('#subnetList').DataTable({
            "processing": false,
            "serverSide": false,
	    "stateSave": false,
	    "dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
            "order": [],
            "language": {   
                "lengthMenu": "Show _MENU_ IPs per page",
                "zeroRecords": "No IPs were returned",
                "info": "Showing page _PAGE_ of _PAGES_",
                "infoEmpty": "No IP records available",
                "infoFiltered": "(filtered from _MAX_ total IPs)"
            },
            "lengthMenu": [[254, 508, 1016, -1], 
                     [254, 508, 1016, "All"]], 
            "columnDefs": [{ "orderable": true, }]} 
        );
$('#subnetList tbody').click('tr', function () {
    var tdata = table.row( this ).data();
    $('.ajax').click(function(tdata) {
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
	    crossDomain : true,
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
    $('.ajax').click(function(tdata) {
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
            crossDomain : true,
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

<?php

/**
 * IP information management page for DHCP Management Console
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

    $pageTitle = "Modify IP: $ip ";


    /*
     * initialize the includes for functions and generate the header
     * use this in all front-end pages to ensure uniformity
     */

        include "includes/authenticate.inc.php";
        include "includes/config.inc.php";
        include "includes/lease2name.inc.php";
        include "includes/header.inc.php";
    include_once "includes/functions.inc.php";

    /* Push and pull vars between pages */

    $_GET['username'] = $username;
        $access_level = access_level($username);
        $who = $username;
        $management_of = "ip";

    /* Use the body include to centralize formatting */
    include "includes/body.inc.php";


	include "lease2name.inc.php";

        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
	$str_sql = "SELECT * FROM $db_tablename_dynamic WHERE ip = '$ip'";
        $result = mysql_db_query($db_name, $str_sql, $id_link);
	$total_rows = mysql_num_rows($result);

        while ($row = mysql_fetch_object($result)){
		$computername = $row->computername;
		$mac = $row->mac;
		$start = $row->start;
		$end = $row->end;
	}

	print "<center>\n";

	$time_string = date("Y-m-d H:i:s");
	print "<h4>Current System Time: <samp>$time_string</samp></h4><hr />\n";

	print "<table class=\"table table-striped table-condensed\" width=\"100%\" id=\"subnetList\">\n";
	print "<tr><td><b>IP Address:</b></td><td>$ip</td></tr>\n";

	// pull data from 'ip' table as well
	$str_sql2 = "SELECT * FROM $db_tablename_ip WHERE ip = '$ip'";
        $result2 = mysql_db_query($db_name, $str_sql2, $id_link);

        while ($row2 = mysql_fetch_object($result2)){

		// if computername field in table 'dynamic' is empty, 
		// use the username field in table 'ip'

		if (! $computername){
			$computername = $row2->username;
		}

		if (! $mac){
			$mac = $row2->mac;
		}

		if (! $ip_type){
			$ip_type = $row2->ip_type;
		}

	}

	print "<tr><td><b>IP Type:</b></td><td>" . ucfirst($ip_type) . "&nbsp;";

	if (strcmp($ip_type, "dynamic") == 0){
		print "<mark class=\"text-danger\">(No Reservation Found!)</mark>";
	}
	
	print "</td></tr>\n";

	if (! $computername){
		$computername = "N/A";
	}

	print "<tr><td><b>Computer Name:</b></td>\n";
	print "<td class=\"normal text-uppercase\">$computername\n";
	print "</td></tr>\n";

	if (! $mac){
		$mac = "N/A";
	}

	print "<tr><td><b>MAC:</b></td><td>$mac&nbsp;</td></tr>\n";

	$start_est = utc2est($start);
	$end_est = utc2est($end);

	// non-dynamic IPs do NOT have entries in the "dynamic" table.
	if ($total_rows <= 0){
		$start_est = "N/A";
		$end_est = "N/A";
	}

	print "<tr><td><b>Lease Starts:</b></td><td>$start_est&nbsp;</td></tr>\n";
	print "<tr><td><b>Lease Ends:</b></td><td>$end_est&nbsp;</td></tr>\n";

	print "<tr>\n";
	print "<td align=center colspan=2>\n";

	print "<a class=\"ajax btn btn-success\" data-title=\"Adding Registration for: <span class='label label-default'>$mac</span>\" data-url=mac_add.php?username=$username&token=$token&username_db=$computername&mac=$mac>Register Mac</a>\n";

	print "<a class=\"ajax btn btn-danger\" data-title=\"Adding to Blacklist: <span class='label label-danger'>$mac</span>\" data-url=blacklist_add.php?username=$username&token=$token&username_db=$computername&mac=$mac>Blacklist Mac</a>\n";

	print "</td>\n";
	print "</tr>\n";

	print "</table>\n";

?>

<!-- Callback to confirm page load -->
<script type="text/javascript">
$(window).load(function() {
    $('#status').fadeOut();
    $('#preloader').delay(50).fadeOut('fast');
    $('#ipTable').removeClass('hidden');
})
</script>
<script>
$(document).ready(function() {
jQuery(function() {
    $('form[data-async]').on('submit', function(event) {
        event.preventDefault()
        var $form = $(this);

    if ( $(this).data('requestRunning') ) {
        return;
    }

    $(this).data('requestRunning', true);

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),

            success: function(data, status) {
          $(".bootbox-body").html(data);
    },
        complete: function() {
            $(this).data('requestRunning', false);
        }
        });

        event.preventDefault();
    });
});
});

$('.btn').on('click', function() {
    var $this = $(this);
  $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
   }, 1000);
});

$(document).ready(function() {
    jQuery(function() {
        $('.ajax').on('click', function(event) {
        event.preventDefault()
         if ( $(this).data('requestRunning') ) { return; }

        $(this).data('requestRunning', true);
	var title = $(this).attr('data-title');
        $.ajax({
            type: 'GET',
            url: $(this).attr('data-url'),
            data: $(this).serialize(),
            success: function(data, status) 
            {
		$('.modal-title').html(title);
                $('.bootbox-body').html(data);
            },
            complete: function() 
            {
                $(this).data('requestRunning', false);
            }
        });

        event.preventDefault();
        });
    });
});

</script>

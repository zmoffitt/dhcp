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

        $pageTitle = "Registered MAC Address Management";


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

    /* Use the body include to centralize formatting */
    include "includes/body.inc.php";


        $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);

	$str_sql = "SELECT * FROM $db_tablename_ip WHERE ip_type = 'registered' ORDER BY username";

        $result = mysql_db_query($db_name, $str_sql, $id_link);

       	if (! $result){
                print "Failed to submit!<br>\n";
       	        include "$footer";
               	exit;
        }

	$total_rows = mysql_num_rows($result);

       	if ($total_rows == 0){
                print "<b>No MAC addresses in the database!</b><br>\n";
       	        include "$footer";
               	exit;
        }
?>
    <div class="container-fluid">
        <div class="text-right">
        <button data-op="add" data-id="" data-username="<? echo $username ?>" data-token="<? echo $token ?>" data-udb="" data-title="Add new MAC to database" class="btn btn-default push-right opButton" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button">Add MAC to Registered List</button></div>
        <hr />
    <div id="row"><div class="col-xs-12">
        <table class="table table-striped table-hover table-bordered table-condensed"  id="defaultList">
    <thead>
        <th>Computer Name</th>
        <th>Client Name</th>
        <th>MAC Address</th>
        <th>Notes</th>

    <? if ($access_level == $ADMIN): ?>
        <th>Modify</th>
        <th>Delete</th>
    <? endif; ?>

        </thead><tbody>
<?
	while ($row = mysql_fetch_object($result)){

		$username_db = $row->username;
		$clientname = $row->clientname;
		$mac = $row->mac;
		$notes = $row->notes;

		if (! $username_db) $username_db = "N/A";
		if (! $clientname) $clientname = "N/A";
		if (! $mac) $mac = "N/A";
		if (! $notes) $notes = "N/A";

		print "<tr>\n";

                print "<td nowrap><small>$username_db</small></td>\n";
                print "<td nowrap><small>$clientname</small></td>\n";
                print "<td class=\"text-uppercase\"><samp>$mac</samp></td>\n";
                print "<td><small>$notes&nbsp;</small></td>\n";

                if ($access_level == $ADMIN) {
		print "<td align=center nowrap>\n";
		print "<form action=mac_modify.php>\n";
		print "<input type=hidden name=username value=$username>\n";
		print "<input type=hidden name=token value=$token>\n";
		print "<input type=hidden name=username_db value=$username_db>\n";
		print "<input type=hidden name=mac value=$mac>\n";
                print "<button data-op=\"modify\" data-id=\"$mac\" data-username=\"$username\" data-token=\"$token\" data-udb=\"$username_db\" data-title=\"Modifying attributes for <span class='label label-default'>$mac</span>\" class=\"btn btn-warning opButton\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i>\" type=\"button\">Modify</button>\n";
		print "</form>\n";
		print "</td>\n";

                print "<td align=center nowrap>\n";                                                                                                                                                                                           
                print "<form action=mac_delete.php>\n";                                                                                                                                                                                       
                print "<input type=hidden name=username value=$username>\n";                                                                                                                                                                  
                print "<input type=hidden name=token value=$token>\n";                                                                                                                                                                        
                print "<input type=hidden name=username_db value=$username_db>\n";                                                                                                                                                            
                print "<input type=hidden name=mac value=$mac>\n";                                                                                                                                                                            
                print "<button data-op=\"delete\" data-id=\"$mac\" data-username=\"$username\" data-token=\"$token\" data-udb=\"$username_db\" data-title=\"Delete MAC <samp>$mac</samp>?\" class=\"btn btn-danger opButton\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i>\" type=\"button\">Delete</button>\n";                                                                                  
                print "</form>\n";                                                                                                                                                                                                            
                print "</td></tr>\n"; 
		}
	}
	print "</tbody></table>\n";

?>
            </div>
          </div>
</div>
</div>
</div>
<script>
$(document).ready(function() {
    $('.opButton').on('click', function() {
        // Get the record's ID via attribute
        var op = $(this).attr('data-op');
        var id = $(this).attr('data-id');
        var username = $(this).attr('data-username');
        var token = $(this).attr('data-token');
        var username_db = $(this).attr('data-udb');
        var title = $(this).attr('data-title');
        var $form = $(this);

        $.ajax({
            url: 'mac_' + op + '.php?mac=' + id + '&username=' + username + '&token=' + token + '&username_db=' + username_db,
            method: 'GET',

        }).success(function(response) {
            // Show the dialog
            bootbox
                .dialog({
                    title:  title,
                    message: $(response),
                    show: false, // We will show it manually later
  onEscape: function() { console.log("Escape!"); },
  backdrop: true,
                    callback: function() {
                        $('.bootbox.modal').modal('hide');
                    }
                })
                .on('shown.bs.modal', function() {
                    $('#deleteUser')
                        .show()                             // Show the login form
                        .formValidation('resetForm'); // Reset form
                })
                .on('hide.bs.modal', function(e) {
                    // Bootbox will remove the modal (including the body which contains the login form)
                    // after hiding the modal
                    // Therefor, we need to backup the form
                        parent.location.reload(true);
                })
                .modal('show');
        });
    });
});

jQuery(function() {
    $('form[data-async]').on('submit', function(event) {
        event.preventDefault()
        var $form = $(this);
        var $target = $($form.attr('data-target'));

    if ( $(this).data('requestRunning') ) {
        return;
    }

    $(this).data('requestRunning', true);

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),

            success: function(data, status) {
            if (data == "ok"){
              $("#doSubmit").addClass( "hidden" );
              $("#myModal").addClass("text-success");
              $("#submitOK").removeClass( "hidden" );

            } else {
              $("#modal-title").html('<? print $errorHeader ?>');
              $("#modal-body").html(data);
              $("#submitError").removeClass("hidden");

            }
        },
        complete: function() {
            $(this).data('requestRunning', false);
        }
        });

        event.preventDefault();
    });
});

$('#myModal').on('hidden.bs.modal', function () {
        parent.location.reload(true);
})

$('.btn').on('click', function() {
    var $this = $(this);
  $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
   }, 750);
});
</script>
        </div>

<? include $footer ?>

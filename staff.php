<?php

/**
 * User Management page for DHCP Management Console
 * JS requested but not required - using it for form validation
 *
 * PHP version 5
 *
 * @category  PHP
 * @author    Zachary Moffitt <zac@gsb.columbia.edu>
 * @copyright 2016 Columbia Business School
 */

/*
 * Configure information about the page
 */

        $pageTitle = "User Administration";

/*
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */

    include "includes/authenticate.inc.php";
	include "includes/config.inc.php";
	$access_level = access_level($username);
	include "includes/header.inc.php";
	$who = $username;

    /* Use the body include to centralize formatting */
    include "includes/body.inc.php"; 
        
    /* init a db connection for upcoming operations */
    $connection = db_connect($db_hostname,$db_username,$db_password,$db_name); 
        

    if (strcmp($action, "add") == 0)
    {

        // make sure it's an administrator
        include "admin_check.inc.php";

        // make sure staff name is in right format
        check_staff_format($staff);

        // make sure staff does not already exist in the database
            for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners))
            {
                $tmp = "\$partner_$key";
                eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
                    if ($to_replicate == 1){
                         staff_exist($dhcp_partners[$key], $key, $staff);
                    }
             }


        // User doesn't exist in database; let complete the transaction
        $ip_from = $REMOTE_ADDR;

        // go through all the replication partners
        for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)) 
        {
            $tmp = "\$partner_$key";
            eval("if ($tmp == 1){\$to_replicate = 1;}else{\$to_replicate = 0;}");
            // print "$tmp = $to_replicate<br>\n";
                if ($to_replicate == 1)
                {
                    staff_add($who, $ip_from, $dhcp_partners[$key], $staff, $grp);
                        if (! $server_list) { $server_list = "$key"; }
                        else { $server_list .= ", $key"; }
                }
		}
		echo "ok"; // return ok, we're done
	
        // reset variables
		$staffAdded = $staff;
        $staff = "";
        $grp = "";
		exit;
    }

    $id_link = mysql_pconnect($db_hostname, $db_username, $db_password);
	$str_sql = "SELECT * FROM $db_tablename_staff ORDER BY username";

        $result = mysql_db_query($db_name, $str_sql, $id_link);

       	if (!$result) { print $top . "Failed to submit to database"; exit; }

    	$total_rows = mysql_num_rows($result);

       	if ($total_rows == 0){
		print "<center>\n";
		print "<font color=ff0000>\n";
                print "<b>No staff member in the database!</b><br>\n";
		print "</font>\n";
		print "</center>\n";
       	        include "$footer";
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

    <div class="container-fluid">
    <div id="row"><div class="col-xs-6 col-xs-offset-3">
	<table class="table table-striped table-hover table-bordered table-condensed">
        <? if ($access_level == $ADMIN): ?>
        <thead>
		<tr><th class="text-right" colspan="4"> <a data-toggle="modal" class="btn btn-default" data-toggle="modal" data-target="#myModal" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading...">Add New User</a></th></tr>
        </thead>
        <? endif; ?><div class="row">
    <tr><thead>
	<th>Staff Name</th>
	<th>Group</th>

    <? if ($access_level == $ADMIN): ?>
	<th>Delete</th>
    <th>Modify</th>
    <? endif; ?>

	</thead></tr>

<?	while ($row = mysql_fetch_object($result))
    {

		$username_db = $row->username;
		$grp = $row->grp;

		print "<tr>\n";

		print "<td nowrap>$username_db</td>\n";
		print "<td nowrap>" . ucfirst($grp) . "</td>\n";

		if ($access_level == $ADMIN)
        {	
            print "<td class=\"text-center\">\n";
    		print "<form action=\"staff_delete.php\" method=\"post\" class=\"form-inline modify\" role=\"form\" id=\"modify\">\n";
    		print "<input type=hidden name=username value=$username>\n";
    		print "<input type=hidden name=token value=$token>\n";
    		print "<input type=hidden name=staff value=$username_db>\n";
    		print "<input type=hidden name=grp value=$grp>\n";
    		print "<button data-op=\"delete\" data-id=\"$username_db\" data-username=\"$username\" data-token=\"$token\" data-grp=\"$grp\" data-title=\"Delete user $username_db?\" `
                    class=\"btn btn-danger editButton\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i>\" type=\"button\">Delete</button>\n";
    		print "</form>\n";
    		print "</td>\n";

    		print "<td align=center nowrap>\n";
            print "<form action=\"staff_modify.php\" method=\"post\" class=\"form-inline modify\" role=\"form\" id=\"modify\">\n";
    		print "<input type=hidden name=username value=$username>\n";
    		print "<input type=hidden name=token value=$token>\n";
    		print "<input type=hidden name=staff value=$username_db>\n";
    		print "<input type=hidden name=grp value=$grp>\n";
            print "<button data-op=\"modify\" data-id=\"$username_db\" data-username=\"$username\" data-token=\"$token\" data-grp=\"$grp\" data-title=\"Editing user $username_db\" `
                    class=\"btn btn-warning editButton\" data-loading-text=\"<i class='fa fa-circle-o-notch fa-spin'></i>\" type=\"button\">Modify</button>\n";
    		print "</form>\n";
    		print "</td>\n";

		}

		print "</tr>\n";

    }
	
	print "</table>\n";
	print "<br><br><hr></div></div></div>\n";
?>

<? if ($access_level == $ADMIN): ?>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <h4 id="modal-title" class="modal-title">Add new user to database</h4>
      </div>
        <div id="modal-body" class="modal-body">
          <div class="container-fluid">
            <form class="form form-horizontal" id="addUserForm" data-async data-target="#myModal" method="post" action="staff.php?q=xmini"> 
              <input type=hidden name=action value=add>
              <input type=hidden name=username value=<? echo "$username"; ?>>
              <input type=hidden name=token value=<? echo "$token"; ?>>
	      <input type=hidden name=q value="xmini">
              <div class="form-group row">
                <label for="name" class="col-sm-2 control-label">Username</label>
		<div class="col-sm-10">
                <input type="text" class="form-control" id="staff" name="staff" type="text" placeholder="GSB Account ID" required autofocus/>
		</div>
	      </div>
              <div class="form-group row">
                <label for="group" class="col-sm-2 control-label">Role</label>
		<div class="col-sm-10">
                <select name="grp" class="form-control">
                    <option value=support>Support (Read Only) [Default]
                    <option value=systems>Administrator (Full Access) </select>
		</div>
              </div>
              <div class="form-group row">
                <label for="message" class="col-sm-2 control-label">Servers</label>
		<div class="col-sm-10">
<?
        if ($dhcp_replicate == 1){
                for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                        $selected = "CHECKED";

			print "<label class=\"checkbox-inline\">\n";
                        print "<input $selected type=checkbox name=partner_$key value=1><b>" . ucfirst($key) . "</b>\n";
			print "</label>\n";
                }
        }

?>
              </div></div>
              <div class="modal-footer">
                <div id="doSubmit" class="form-group row">
		    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		    <button type="submit" class="btn btn-primary" disabled="disabled">Add user</button>
                </div>
		<div id="submitOK" class="hidden"> 
		<h3 class="text-center text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> User <? echo $staffAdded ?> added successfully!</h3>
		<button type="button" id="submitAdd" class="btn btn-success btn-block" data-dismiss="modal">Close</button></div>
		<div id="submitError" class="hidden">
		<h3 class="text-center text-danger">An error occured, please try again.</h3>
		</div>
            </form>
            </div>
          </div>
</div>
</div>
</div>

<script>
$(document).ready(function() {
    $('#userForm')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: {
                    validators: {
                        notEmpty: {
                            message: 'The full name is required'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z\s]+$/,
                            message: 'The full name can only consist of alphabetical characters'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'The email address is required'
                        },
                        emailAddress: {
                            message: 'The email address is not valid'
                        }
                    }
                },
                website: {
                    validators: {
                        notEmpty: {
                            message: 'The website address is required'
                        },
                        uri: {
                           allowEmptyProtocol: true,
                            message: 'The website address is not valid'
                        }
                    }
                }
            }
        })

    $('.editButton').on('click', function() {
        // Get the record's ID via attribute
	var op = $(this).attr('data-op');
        var id = $(this).attr('data-id');
	var username = $(this).attr('data-username');
	var token = $(this).attr('data-token');
	var grp = $(this).attr('data-grp');
	var title = $(this).attr('data-title');
	var $form = $(this);

        $.ajax({
            url: 'staff_' + op + '.php?staff=' + id + '&username=' + username + '&token=' + token + '&grp=' + grp,
            method: 'POST',

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

$(document).ready(function() {
    $('#addUserForm')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
	    autoFocus: 'enabled',
            fields: {
                staff: {
                    validators: {
                        stringLength: {
                            min: 3,
                            max: 25,
                            message: 'The username is not valid'
                        },
                        notEmpty: {
                            message: 'The full name is required'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9\s]+$/,
                            message: 'Only alphanumeric characters are permitted'
                        }
                    }
                },
            }
        })
        .on('success.field.fv', function(e, data) {
            if (data.fv.getInvalidFields().length > 0) {    // There is invalid field
                data.fv.disableSubmitButtons(true);
            }
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
<? endif; ?>
<? include $footer ?>

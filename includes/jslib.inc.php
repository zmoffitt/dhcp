<?php 

/**
 * Standard set of Javascript functions for user interface 
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
?>

<script>

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


 //
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

$('#myModal').on('hidden.bs.modal', function () {
        parent.location.reload(true);
})

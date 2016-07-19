<?php

/**             
 * Include Library: body.inc.php
 * builds the body based on page name
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

    include_once "includes/authenticate.inc.php";
    include_once "includes/config.inc.php";

    /* Collect the vars we might need */ 
    
    $activePage = basename($_SERVER['PHP_SELF'], ".php");
    $username = $_GET['username'];
    $access_level = access_level($username);

?>


<? if ($activePage == 'main'): ?>
    <!-- Define the main.php body -->
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
    });
    </script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <h3 class="text-center">DHCP Manager (Uris)<br /><small class="text-muted">IP Management: <strong>Subnet <? echo $subnet ?></strong></small></h3>
            </div>
      </div>
      <hr>

    <? include "includes/subnets.inc.php"; ?>
<? /* ------ end of main.php body ------- */ ?>    


<? elseif ($activePage == 'modify_ip'): ?>
    <!-- Define modification page body -->
        <div class="container-fluid">
        <div class="row">
        </div>

<? /* ------ end of modification page body ------- */ ?> 

<? elseif ($activePage == 'staff'): ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h3 class="text-center">DHCP Manager (Uris)<br /><small class="text-muted"><? echo $pageTitle ?></small></h3>
    </div>
  </div>
  <hr>
<? endif; ?>

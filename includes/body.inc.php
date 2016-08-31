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

    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <h3 class="text-center">DHCP Manager (Uris)<br /><small class="text-muted">IP Management: <strong>Subnet <? echo $subnet ?></strong></small></h3>
            </div>
      </div>
      <hr>

    <? include "includes/subnets.inc.php"; ?>
<? /* ------ end of main.php body ------- */ ?>    


<? elseif (in_array($activePage, array('active','modify_ip','staff_modify','staff_delete', 'blacklist_delete', 'blacklist_modify', 'mac_add', 'mac_delete'), true )): ?>
    <!-- Define modification page body -->
        <div class="container-fluid">
        <div class="row">
        </div>

<? /* ------ end of modification page body ------- */ ?> 

<? elseif (in_array($activePage, array('staff','blacklist', 'mac', 'stats'), true)): ?>
    <script type="text/javascript">         
        {$('#defaultList').DataTable({
            "lengthMenu": [[50, 100, 250, -1], 
                     [50, 100, 250, "All"]], 
            "columnDefs": [{ "orderable": true, }]} 
        );
    };
    </script>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h3 class="text-center">DHCP Manager (Uris)<br /><small class="text-muted"><? echo $pageTitle ?></small></h3>
    </div>
  </div>
  <hr>
<? if ($activePage == 'stats'): ?>
<? include "includes/subnets.inc.php"; ?>
<? endif; ?>
<? endif; ?>

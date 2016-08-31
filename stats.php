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

        $pageTitle = "System Overview";
	include "assets/charts/chart.php";

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

?>

<hr />
<div class="row text-center">
<img src="assets/images/tmp/subnet_overview.png">
<img src="assets/images/tmp/subnet_free_overview.png">
</div></div>

<footer class="footer">
<hr />
<div class="container">
<? include $footer ?>
</footer>

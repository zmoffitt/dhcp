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
	include "chart.php";
/*
 * initialize the includes for functions and generate the header
 * use this in all front-end pages to ensure uniformity
 */

    /* Use the body include to centralize formatting */
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>172 Subnet Overview | DHCP Manager</title>
<link rel="stylesheet" href="/dhcp/css/bootstrap.css">
<link rel="stylesheet" href="/dhcp/css/fa/css/font-awesome.min.css">
<link rel="stylesheet" href="/dhcp/css/formValidation.min.css">
<link rel="stylesheet" href="/dhcp/css/dataTables.bootstrap4.min.css" type="text/css">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<script type="text/javascript" src="/dhcp/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="/dhcp/js/bootstrap.js"></script>
<script type="text/javascript" src="/dhcp/js/formValidation.min.js"></script>
<script type="text/javascript" src="/dhcp/js/bootstrapFramework.min.js"></script>
<script type="text/javascript" src="/dhcp/js/bootbox.min.js"></script>
<script type="text/javascript" src="/dhcp/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/dhcp/js/dataTables.bootstrap4.min.js"></script>
<style>
td{
    max-width: 350px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

        /* Preloader */

        #preloader {
            position:relative;
            z-index:99; /* makes sure it stays on top */
        }

        #status {
	    width: 100%;
	    height: 100%;
	    margin-top: 150px;
            position:absolute;
        }
</style>
</head>
<body>
<meta HTTP-EQUIV=Refresh content=300>

<nav class="navbar navbar-default">
  <div class="container-fluid"> 
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#defaultNavbar1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
      <img src="/dhcp/assets/images/cbs.png" width="48"></div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="defaultNavbar1">
      <ul class="nav navbar-nav">
        <li role="presentation"><a href="main.php?subnet=172&username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da&refresh_rate=300">Home<span class="sr-only">(current)</span></a></li>
	        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs & Statistics<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/dhcp/logs.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da&refresh_rate=300">Log Viewer</a></li>
            <li><a href="/dhcp/login_logs.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">Login Log Viewer</a></li>
            <li class="divider"></li>
            <li><a href="/dhcp/dhcpd_logs.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">Search Daemon Logs</a></li>
            <li><a href="/dhcp/search.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">Search Client Logs</a></li>
            <li class="divider"></li>
            <li><a href="#">DHCP Statistics</a></li>
          </ul>
        </li>
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">System Administration<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
	    <li class="dropdown-header">Main Settings</li>
            <li><a href="main.php?subnet=172&username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">IP Management</a></li>
	    <li><a href="blacklist.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">MAC Blacklist Management</a></li>
	    <li><a href="mac.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">Registered MAC List Management</a></li>
            <li><a href="modify_subnet.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">Subnet Management</a></li>
	    <li class="divider"></li>
	    <li class="dropdown-header">System Management</li>
            <li ><a href="modify_global.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">Global Configuration</a></li>
            <li ><a href="staff.php?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da">User Administration</a></li>
            <li class="divider"></li>
	    <li class="dropdown-header">System Operations</li>
	    <li><a class="ajax" data-ip="restartDHCP" data-title="<h3 class='text-danger'>DHCPd is restarting<h3>" role="button" data-url="/dhcp/sys/test.php?q=mini&username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da"><span class="label label-danger">Trigger DHCPd Service Reload</a></li>
          </ul>
        </li>
 <li role="presentation" class="active" ><a href="assets/charts/"username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da&refresh_rate=300">Stats<span class="sr-only">(current)</span></a></li>
      </ul>
<ul class="nav navbar-nav navbar-right">
<li><a target="_blank" href="/dhcp/status/?username=zmoffitt&token=0398dee8115206d4a482a71b7deea9da"><span class="label label-success">DHCPd OK <i class="fa fa-check-circle" aria-hidden="true"></i></span></a></li>
        <li><a href="/dhcp/index.php?q=logout">Sign Out</a></li>
      </ul>
    </div>
    <!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>


    <!-- Define the main.php body -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <h3 class="text-center">DHCP Manager (Uris)<br /><small class="text-muted"><strong>Chart Test</strong></small></h3>
            </div>
      </div>
      <hr>
<div class="row text-center">
<img src="imagefile.png">
</div>

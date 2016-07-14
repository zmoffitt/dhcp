<?php

/**
 * Header file for DHCP Management Console
 * should be used on all pages
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

        include_once "config.inc.php";
        $auth_string = auth_string($username);
	$activePage = basename($_SERVER['PHP_SELF'], ".php");
	$mini = $_GET['q'];
	$authURL = "username=$username&token=$token&refresh_rate=$default_refresh_rate";

?>
<? if ($mini == 'xmini'): ?>
<? else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><? echo $pageTitle?> | DHCP Manager</title>
<link rel="stylesheet" href="/dhcp/css/bootstrap.css">
<link rel="stylesheet" href="/dhcp/css/fa/css/font-awesome.min.css">
<link rel="stylesheet" href="/dhcp/css/formValidation.min.css">
<link rel="stylesheet" href="/dhcp/assets/fancybox/source/jquery.fancybox.css">
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
<script type="text/javascript" src="/dhcp/assets/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
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
<? if ($refresh_rate): ?>
<meta HTTP-EQUIV=Refresh content=<? echo $refresh_rate ?>>
<? endif; ?>

<? if ( (!in_array($activePage, array('index','modify_ip','staff_modify','staff_delete'), true ) && empty($mini))): ?>
<nav class="navbar navbar-default">
  <div class="container-fluid"> 
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#defaultNavbar1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
      <img src="/dhcp/assets/images/cbs.png" width="48"></div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="defaultNavbar1">
      <ul class="nav navbar-nav">
        <li role="presentation" <?= ($activePage == 'main') ? 'class="active"':''; ?>><a href="main.php?subnet=172&<? echo $authURL; ?>">Home<span class="sr-only">(current)</span></a></li>
	<?php 
        for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                if (strcmp($identifier, $key) != 0){
                        print "<li><a target=\"blank\" href=\"http://$dhcp_partners[$key]/dhcp/main.php?$authURL\">DHCP Manager: $key</a></li>\n";
                }
        } ?>
        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs & Statistics<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/dhcp/logs.php?<? echo $authURL; ?>">Log Viewer</a></li>
            <li><a href="/dhcp/login_logs.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Login Log Viewer</a></li>
            <li class="divider"></li>
            <li><a href="/dhcp/dhcpd_logs.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Search Daemon Logs</a></li>
            <li><a href="/dhcp/search.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Search Client Logs</a></li>
            <li class="divider"></li>
            <li><a href="#">DHCP Statistics</a></li>
          </ul>
        </li>
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">System Administration<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
	    <li class="dropdown-header">Main Settings</li>
            <li><a href="main.php?subnet=172&username=<? echo $username; ?>&token=<? echo $token; ?>">IP Management</a></li>
	    <li><a href="mac.php?username=<? echo $username; ?>&token=<? echo $token; ?>">MAC Address Management</a></li>
            <li><a href="modify_subnet.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Subnet Management</a></li>
	    <li class="divider"></li>
	    <li class="dropdown-header">System Management</li>
            <li <? ($activePage == 'modify_global') ? 'class="active"':''; ?>><a href="modify_global.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Global Configuration</a></li>
            <li <? ($activePage == 'staff') ? 'class="active"':''; ?>><a href="staff.php?username=<? echo $username; ?>&token=<? echo $token; ?>">User Administration</a></li>
            <li class="divider"></li>
	    <li class="dropdown-header">System Operations</li>
            <li><a href="#"><span class="label label-danger">Trigger DHCPd Service Reload</a></li>
          </ul>
        </li>
      </ul>
<ul class="nav navbar-nav navbar-right">
<?php

$daemon = `ps auxw | grep -i dhcpd | grep -iv grep`;
$fields = split("[ ]+", $daemon);

$pid = $fields[0];
$time = $fields[8];

// if daemon is running
if ($daemon){
        $string = "<li class=\"text-success\"><a target=\"_blank\" href=\"/dhcp/status/?username=$username&token=$token\">System OK <span class=\"glyphicon glyphicon-ok-sign text-success\"></span></a></li>";
}

// if daemon is NOT running
else{
        $string = "<li class=\"text-danger\"><a class=\"_blank\" href=\"/dhcp/status/?username=$username&token=$token\">System Error <span class=\"glyphicon glyphicon-exclamation-sign text-danger\"></span></a></li>";
}

print "$string\n";
?>
        <li><a href="/dhcp/index.php?q=logout">Sign Out</a></li>
      </ul>
    </div>
    <!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>
<? endif; ?>
<? endif; ?>

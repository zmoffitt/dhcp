<?
        include "config.inc.php";
        $auth_string = auth_string($username);
	$activePage = basename($_SERVER['PHP_SELF'], ".php");
	$mini = $_GET['q'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DHCP Manager</title>
<link rel="stylesheet" href="/dhcp/css/bootstrap.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
<link rel="stylesheet" href="/dhcp/css/formValidation.min.css">
<link rel="stylesheet" href="/dhcp/assets/fancybox/source/jquery.fancybox.css">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:300,400,700">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Cardo:400,400italic,700">

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

<style>
td{
    max-width: 350px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
 .badge-success {
background-color: #398439;
}
.badge-warning {
background-color: #d58512;
}
</style>
</head>
<body>
<?
        if ($refresh_rate){
                print "<meta HTTP-EQUIV=Refresh content=$refresh_rate>\n";
        }
?>
<? if ( (!in_array($activePage, array('modify_ip','staff_modify','staff_delete'), true ) && empty($mini))) {
?>
<nav class="navbar navbar-default">
  <div class="container-fluid"> 
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#defaultNavbar1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
      <img src="http://wiki.gsb.columbia.edu/research/resources/assets/cbs.png" width="48"></div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="defaultNavbar1">
      <ul class="nav navbar-nav">
        <li role="presentation" <?= ($activePage == 'main') ? 'class="active"':''; ?>><a href="main.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Home<span class="sr-only">(current)</span></a></li>
	<?php 
        for (reset($dhcp_partners); $key = key($dhcp_partners); next($dhcp_partners)){
                if (strcmp($identifier, $key) != 0){
                        print "<li><a target=\"blank\" href=\"http://$dhcp_partners[$key]/dhcp/main.php?username=$username&token=$token\">DHCP Manager: $key</a></li>\n";
                }
        } ?>
        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Logs & Statistics<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/dhcp/logs.php">Log Viewer</a></li>
            <li><a href="/dhcp/login_logs.php">Login Log Viewer</a></li>
            <li class="divider"></li>
            <li><a href="#">Search Daemon Logs</a></li>
            <li><a href="#">Search Client Logs</a></li>
            <li class="divider"></li>
            <li><a href="#">DHCP Statistics</a></li>
          </ul>
        </li>
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">System Administration<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="main.php?username=<? echo $username; ?>&token=<? echo $token; ?>">IP Management</a></li>
            <li><a href="modify_subnet.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Subnet Management</a></li>
	    <li <? ($activePage == 'staff') ? 'class="active"':''; ?>><a href="staff.php?username=<? echo $username; ?>&token=<? echo $token; ?>">Staff Management</a></li>
            <li class="divider"></li>
            <li><a href="#"><span class="label label-danger">Trigger DHCPd Service Reload</a></li>
          </ul>
        </li>
      </ul>
<ul class="nav navbar-nav navbar-right">
<?

$daemon = `ps auxw | grep -i dhcpd | grep -iv grep`;
$fields = split("[ ]+", $daemon);

$pid = $fields[0];
$time = $fields[8];

// if daemon is running
if ($daemon){
        $string = "<li class=\"text-success\"><a href=\"/dhcp/status/?username=$username&token=$token\">System OK <span class=\"glyphicon glyphicon-ok-sign text-success\"></span></a></li>";
}

// if daemon is NOT running
else{
        $string = "<li class=\"text-danger\"><a href=\"/dhcp/status/?username=$username&token=$token\">System Error <span class=\"glyphicon glyphicon-exclamation-sign text-danger\"></span></a></li>";
}

print "$string\n";
?>
        <li><a href="/dhcp/logout.php">Sign Out</a></li>
      </ul>
    </div>
    <!-- /.navbar-collapse --> 
  </div>
  <!-- /.container-fluid --> 
</nav>

<?
 }
?>

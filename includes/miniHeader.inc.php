<?
        include "config.inc.php";
        $auth_string = auth_string($username);
	$activePage = basename($_SERVER['PHP_SELF'], ".php");
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
</head>
<body>

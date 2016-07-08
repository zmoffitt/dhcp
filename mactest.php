<head>
<link rel="stylesheet" href="/dhcp/css/bootstrap.css">
<link rel="stylesheet" href="/dhcp/css/font-awesome.min.css" />
<link rel="stylesheet" href="/dhcp/css/formValidation.min.css">

<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lato:300,400,700">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Cardo:400,400italic,700">

<!--[if lt IE 9]>
<script src="//cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//cdn.jsdelivr.net/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<script src="/dhcp/js/jquery-1.11.3.min.js"></script>
<script src="/dhcp/js/bootstrap.js"></script>
<script src="/dhcp/js/formValidation.min.js"></script>
<script src="/dhcp/js/bootstrapFramework.min.js"></script>
</head>

<body>
<div class="container">
<form METHOD=POST action="modify_ip.php" id="updateForm" class="form-inline">
<fieldset class="form-group">
    <div class="form-group">
        <label class="col-xs-3 control-label">IP address</label>
        <div class="col-xs-7">
            <input type="text" class="form-control" name="ip" />
        </div>
    </div>
</fieldset>

<fieldset class="form-group">
    <div class="form-group">
        <label class="col-xs-3 control-label">MAC address</label>
        <div class="col-xs-7">
            <input type="text" class="form-control" name="mac" />
        </div>
    </div>
</fieldset>
</form>

<script>
$(document).ready(function() {
    $('#updateForm').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            ip: {
                validators: {
                    notEmpty: {
                        message: 'The IP address is required'
                    },
                    ip: {
                        message: 'Please enter a valid IP address'
                    }
                }
            },
            mac: {
                validators: {
                    notEmpty: {
                        message: 'The MAC address is required'
                    },
                    mac: {
                        message: 'Please enter a valid MAC Address'
                    }
                }
            }
        }
    });
});
</script>

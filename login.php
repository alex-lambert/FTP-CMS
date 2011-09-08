<?php
error_reporting(E_ALL);
ob_start();
session_start();
if(isset($_POST['auth'])){
	if($_POST['auth'] == 'password'){
		$_SESSION['logged'] = true;
		header('Location: index.php');
		exit;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>XimpleCMS Editor - Login</title>
<style type="text/css" media="screen">
body {
	font-family: lucida sans, verdana, arial;
	font-size: 11px;
	color:#333333;
}

input {
	font-family: lucida sans, verdana, arial;
	font-size: 11px;
	color: #333333;
	border: 1px solid #666666;
}

a:link, a:visited {
	font-family: lucida sans, verdana, arial;
	font-size: 11px;
	color: #333333;
	text-decoration: none;
}

a:hover, a:active {
	font-family: lucida sans, verdana, arial;
	font-size: 11px;
	color: #666699;
	text-decoration: underline;
}

.notset {
	background: #CCCCCC;
	border: 1px solid #FF0000;
	font-size: 9px;
	width: 160px;
	text-align: center;
}

.set {
	background: #99FF99;
	border: 1px solid #339900;
	font-size: 9px;
	width: 160px;
	text-align: center;
}

.required {
	color: #FF0000;
}

</style>
<body>
<table align="center" width="70%" border="4" cellspacing="10" cellpadding="20">
  <tr>
    <td>
	<h1 align="center">Ximple CMS : Remote Hosted CMS Solution : Login</h1>
	</td>
  </tr>
  <tr>
    <td>
    <form action="login.php" method="post">
    	<fieldset>
      	<legend>Enter the Autherisation Password to use the FTP CMS</legend>
        <input type="text" name="auth" size="40" />&nbsp;<input type="submit" value="Login &raquo;" />
      </fieldset>
    </form>
		</td>
  </tr>
</table>
</body>
</html>
<?php ob_end_flush(); ?>
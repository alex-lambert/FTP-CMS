<?php
error_reporting(E_ALL);
ob_start();
session_start();
if(!isset($_SESSION['logged'])){
	header('Location: login.php');
	exit;	
}

if(isset($_GET['restart']) && $_GET['restart'] == 'true'){
	session_destroy();
	@unlink($_SESSION['localpath']);
	header('Location: index.php');
	exit;
}


if(!isset($_SESSION['step'])) $_SESSION['step'] = 1;

if(!isset($_GET['editor'])){
	$_SESSION['editor'] = 'Basic';
}else{
	$_SESSION['editor'] = $_GET['editor'];
}

if(isset($_POST['filename'])){
	$_SESSION['host'] = $_POST['host'];
	$_SESSION['user'] = $_POST['user'];
	$_SESSION['pass'] = $_POST['pass'];
	$_SESSION['attrib_value'] = $_POST['attrib_value'];
	$_SESSION['attrib_name'] = $_POST['attrib_name'];
	
	if(isset($_POST['directory'])){
		$_SESSION['remote_directory'] = $_POST['directory'];
	}else{
		$_SESSION['remote_directory'] = false;
	}
	
	$_SESSION['step'] = 2;
	$_SESSION['filename'] = $_POST['filename'];
	$_SESSION['localpath'] = 'tmp/'.$_SESSION['filename'];
}

if(isset($_GET['action']) && $_GET['action'] == 'save'){
	$_SESSION['step'] = 3;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>XimpleCMS Editor</title>
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
<script language="javascript" type="text/javascript">

function SwapStyle(id,newClass){
	document.getElementById(id).className = newClass;
}

function hide(id){
	if(document.getElementById(id).style.display != 'none') 
		document.getElementById(id).style.display = 'none';
}

function show(id){
	if(document.getElementById(id).style.display == 'none')	
		document.getElementById(id).style.display = 'inline';
}
</script>
</head>

<body>
<table align="center" width="70%" border="4" cellspacing="10" cellpadding="20">
  <tr>
    <td>
	<h1 align="center">Ximple CMS : Remote Hosted CMS Solution</h1>
	</td>
  </tr>
  <tr <?php if($_SESSION['step'] != 1) print(' style="display: none;"'); ?>>
    <td>
	<p align="justify">Prototype: Version 1.0.4</p>
	<p align="justify">We suggest that you use DIV elements as your editable regions. FTP CMS is not restricted to just these elements though, you can make any full html tags inner html editable by adding the classname. You can make as many elements editable as you see fit - you will get one wysiwyg editor per editable region.</p>
	<p align="justify">Fill in the required details and hit  either Next Step link or the 'Start Editor' button. Once you have started an FTP session then the Local/FTP Filesystem browser link will become available.</p></td>
  </tr>
  <tr <?php if($_SESSION['step'] == 1) print(' style="display: none;"'); ?>>
    <td>
		<p><a href="index.php?restart=true">Click here to restart editor</a>&nbsp;|&nbsp;<a href="ftp-filebrowser/index.php">Click here to open the FTP File Browser</a></p>
    </td>
  </tr>
  <tr>
    <td>
		<table width="100%" border="0" cellspacing="10" cellpadding="10">
		  <tr>
			<td id="visone" align="center" class="notset"><strong>Step One</strong><br />Enter FTP Host details</td>
			<td id="vistwo" align="center" class="notset"><strong>Step Two</strong><br />			  Enter File  details</td>
			<td id="visthree" align="center" class="notset"><strong>Step Three</strong><br />Enter block id details</td>
			<td id="editing" align="center" class="notset"><strong>Step Four</strong><br />Edit your page!</td>
		  </tr>
		</table>
	</td>
  </tr>
  <tr>
    <td>
	<?php
	switch($_SESSION['step']){
		case 1 :
			include('getfilename.tpl.php');
		break;
		
		case 2 :
			include('ftpeditor.class.php');
			global $ftped;
			$ftped = new ftpeditor($_SESSION['host'],
								   $_SESSION['user'],
								   $_SESSION['pass'],
								   $_SESSION['attrib_name'],
								   $_SESSION['attrib_value']);	
			$blocks = $ftped->prepareFTP($_SESSION['filename'])->get($_SESSION['remote_directory'])->parseDomOut($ftped->domObj);
			if(count($blocks) == 0){
				echo 'Sorry, but this page doesnt have editable regions defined.';
			}else{
				include('fckeditor/fckeditor.php');
				include('editor.tpl.php');
			}
		break;
		
		case 3 :
			$newnodes = array();
			foreach($_POST as $key => $value){
				if($key != 'submit'){		
					$newnodes[$key] = stripslashes($value);
				}
			}		
			
			include('ftpeditor.class.php');
			global $ftped;
			$ftped = new ftpeditor($_SESSION['host'],
								   $_SESSION['user'],
								   $_SESSION['pass'],
								   $_SESSION['attrib_name'],
								   $_SESSION['attrib_value']);	
			$ftped->prepareFTP($_SESSION['filename'])->get($_SESSION['remote_directory'])->parseDomIn($ftped->domObj,$newnodes)->send();
			include('complete.tpl.php');
		break;
	}
	?>
	</td>
  </tr>
</table>
</body>
</html>
<?php ob_end_flush(); ?>
<?php
	session_start();
	if(isset($_GET['restart'])){
		session_destroy();
		header('Location: index.php');
		exit;
	}
	if(!isset($_SESSION['logged'])){
		header('Location: ../login.php');
		exit;	
	}

	$dirpath = getcwd();
	$dirpath = realpath($dirpath);
	$dirpath = addslashes($dirpath);
	$dirpath = str_replace('\\\\','/',$dirpath);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>FTP Filetree</title>
		<link type="text/css" rel="stylesheet" media="all" href="css/ftp-filetree.css" />
    <script language="javascript" type="text/javascript" src="script/prototype.js"></script>
    <script language="javascript" type="text/javascript" src="script/scriptaculous.js"></script>    
    <script language="javascript" type="text/javascript" src="script/effects.js"></script>    
    <script language="javascript" type="text/javascript" src="script/prototypeFileTree.js"></script>    
</head>

<body>
    <h1>Filetree Explorer</h1>
    <p>Original credits for the filetree prototype extension go to: <a href="http://www.marzapower.com/">MarzaPower</a></p>
    <p> It works rather like the Windows Explorer, so most people should be familier with its layout and use. It should be considered a highly insecure script - there is no data validation on the request and thusly is open to infection attacks and so on - only use it as an example to build from. We built it into our custom CMS, protecting the code with the built in sanitation methods and user authentication - never leave this script open to the public!</p>
    <?php if(isset($_POST['mode'])){ ?><p><a href="index.php?restart=true" title="Restart the Filetree" onclick="javascript: return confirm('Are you sure you want to restart the Filetree?');">Click here to restart the Filetree Explorer</a> at any time.</p><?php } ?>
    <?php if(isset($_POST['mode'])){ print('<h2>Filetree mode: '.strtoupper($_POST['mode']).'</h2>'); include('includes/'.$_POST['mode'].'.inc.php'); }else{ ?>
    <form method="post" action="index.php">
    	<fieldset>
        	<legend>Choose the desired filetree mode</legend>
            <select name="mode">
            	<option value="local">Local filesystem ( root: current directory )</option>
                <option value="ftp">FTP Filesystem ( requires host, username and password )</option>
            </select>
            &nbsp;
            <input type="submit" value="Select Mode and Continue" />
      </fieldset>
    </form>
    <?php }?>
</body>
</html>

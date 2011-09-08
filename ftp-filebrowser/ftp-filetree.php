<?php
//
// prototype File Tree PHP Connector
//
// Version 1.0
//
// Copyright (c)2008 Daniele Di Bernardo
// marzapower.com (http://www.marzapower.com)
// 01 April 2008
//
// Visit http://www.marzapower.com/blog/show/211 for more information
//

#---- Change: Ben Duffin, 9/1/2009
#
# updated the original file lister
# to list from a remote FTP dir using
# login details specified below 
session_start();
if(!isset($_SESSION['settings'])){
	$_SESSION['settings'] = array('h' => $_GET['h'],
																'u' => $_GET['u'],
																'p' => $_GET['p']);
}

$oldtime = ini_set('max_execution_time',3600);

// Sets the remote host directory
function set_remote_dir($conn,$dir=false){ 

	$curdir = false;
	
	// If no dir is passed change to root directory
	$start = false;
	if(!$dir){
		$start = true;
		$dir = ftp_pwd($conn);
	}
	$curdir = ftp_pwd($conn);
	
	// Alter slashes to correct format
	$x = substr($dir, (strlen($dir)-1)); 
	if($x != "/" && $x != "\\") $dir.="/"; 
	
	// Change the FTP connection to the given directory 
	if($start){
		if(!ftp_chdir($conn,$dir)){
			die('Could not change to this directory');
		}
	}
	
	return $curdir;
} 

function raw_list($folder,$conn,$suffix,$files){  
	$suffixes = explode(",", $suffix); 
	$list     = ftp_rawlist($conn, $folder); 
	$anzlist  = count($list); 
	$i = 0; 
	while ($i < $anzlist){ 
	  $split    = preg_split("/[\s]+/", $list[$i], 9, PREG_SPLIT_NO_EMPTY); 
	  $ItemName = $split[8]; 
	  $endung   = strtolower(substr(strrchr($ItemName,"."),1)); 
	  $path     = $ItemName; 
	  if (substr($list[$i],0,1) === "d" AND substr($ItemName,0,1) != "."){ 
	     $files['dirs'][] = $path;
	  }elseif(substr($ItemName,0,2) != "._" AND in_array($endung,$suffixes)){
	  	 $modtime = ftp_mdtm($conn,$path);
		 if($modtime != -1){
		 	$modtime = date('F d y H:i:s',$modtime);
		 }else{
		 	$split[4] = round($split[4] / 1024,2);
		 	$modtime = $split[5].' '.$split[6].' - '.$split[7].", \n\rFilesize: ".$split[4].'Kb';
		 }
		 $files['files'][$path] = $modtime;
	  }; 
	  $i++; 
	} 
	return $files; 
} 

# the directory where ftp_rawlist starts 
$startdir = $_POST['dir'];


# optional Datatypefilter (leave blank if not needed) 
$suffix   = 'html,htm,phtml,phtm,shtml,shtm,asp,php,php3,php4,php5,xml'; 

# ftp-login 
$ftp_server = $_SESSION['settings']['h']; 
$ftp_user   = $_SESSION['settings']['u']; 
$ftp_pw     = $_SESSION['settings']['p']; 

// allow FTP host time to catchup
sleep(1);
$conn   	= ftp_connect($ftp_server,21,60); 
if(!$conn){
	die('Sorry, there was an error connecting to your FTP host. Please try again.');
}	
$ftp 		= ftp_login($conn, $ftp_user, $ftp_pw) 
				or die('<h1>ftp-login failed</h1>
						<p class="titletagline">Could not logon to the remote FTP host. Please try again</p>'); 
// allow FTP host time to catchup
sleep(1);
ftp_pasv($conn, true); 
set_remote_dir($conn,$startdir);

$files    = array('files' => array(),
				  				'dirs' => array());
$files    = raw_list($startdir,$conn,$suffix,$files); 

// allow FTP host time to catchup
sleep(2);

// Start pFTree
echo "<div style=\"display: none;\">
		<div>
			<ul class=\"prototypeFileTree\">";
			
// All dirs
foreach( $files['dirs'] as $idx => $dir){
	$rel = $startdir.'/'.$dir.'/';
	
	echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"$rel\">" . htmlspecialchars($dir) . "</a></li>";
}
// All files
foreach( $files['files'] as $file =>  $modtime){
	$ext = preg_replace('/^.*\./', '', $file);
	$startdir = str_replace('///','/',$startdir);
	$startdir = str_replace('//','/',$startdir);
	$rel = $startdir.$file;
	echo "<li class=\"file ext_$ext\">
		  <a href=\"#\" rel=\"{$rel}\" title=\"Last modification: $modtime\">" . htmlspecialchars($file) . "</a>
		  </li>";
}

// End pFTree
echo "</ul></div></div>";

// update dir
echo  '<script langauge="javascript" type="text/javascript"> 
		$(\'curdir\').innerHTML = \''.str_replace('//','/',$_POST['dir']).'\';
	  </script>';

ftp_close($conn); 

ini_set('max_execution_time',$oldtime);

?>
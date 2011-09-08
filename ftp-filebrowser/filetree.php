<?php
if( file_exists($_POST['dir']) ) {
	$files = scandir($_POST['dir']);
	natcasesort($files);
	if( count($files) > 2 ) { 
		echo "<div style=\"display: none;\"><div><ul class=\"prototypeFileTree\">";

		foreach( $files as $file ) {
			if( file_exists("{$_POST['dir']}/{$file}") && $file != '.' && $file != '..' && is_dir("{$_POST['dir']}/{$file}") ) {
				echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"{$_POST['dir']}/{$file}/\">" . htmlspecialchars($file) . "</a></li>";
			}
		}

		foreach( $files as $file ) {
			if( file_exists("{$_POST['dir']}/{$file}") && $file != '.' && $file != '..' && !is_dir("{$_POST['dir']}/{$file}") ) {
				$ext = preg_replace('/^.*\./', '', $file);
				echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"{$_POST['dir']}/{$file}\">" . htmlspecialchars($file) . "</a></li>";
			}
		}
		echo "</ul></div></div>";
	}
}

echo  '<script langauge="javascript" type="text/javascript"> 
		$(\'curdir\').innerHTML = \''.$_POST['dir'].'\';
	  </script>';
?>


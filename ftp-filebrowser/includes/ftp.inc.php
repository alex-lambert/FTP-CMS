   <?php if(isset($_POST['settings'])){ $dirpath = $_POST['dir']; ?>
   <h4 id="curdir"><?php echo $dirpath; ?></h4>
   <div id="fileexplorer"></div>
   <script language="javascript" type="text/javascript">
        Event.observe(window,'load',function(event){
              $('fileexplorer').fileTree({ root: '<?php print($dirpath); ?>', 
                                           script: 'ftp-filetree.php?h=<?php print($_POST['host']); ?>&u=<?php print($_POST['user']); ?>&p=<?php print($_POST['pass']); ?>&isstart=true' , 
                                           folderEvent: 'dblclick', 
                                           loadMessage: 'Please wait while the file list loads "<b><?php print($dirpath); ?></b>"'
                                          }, fileExplorerFileClick);
        });
        
        function fileExplorerFileClick(file){
            var strippedPath = file.replace(/(\/\/)+/,'/');
            alert('path: ' + strippedPath);
        }
   </script>
   <?php }else{ ?>
   	<form action="index.php" method="post">
    	<input type="hidden" name="mode" value="ftp" />
            <input type="hidden" name="settings" value="true" />
        <fieldset>
        	<legend>Enter the details for the FTP host</legend>
            <table width="300" border="1" style="border-collapse:collapse">
            	<tr>
                	<td>FTP Host:</td>
                    <td><input type="text" name="host" size="40" value="<?php print($_SESSION['host']); ?>" /></td>
                </tr>
            	<tr>
                	<td>FTP Username:</td>
                    <td><input type="text" name="user" size="40" value="<?php print($_SESSION['user']); ?>" /></td>
                </tr>
            	<tr>
                	<td>FTP Password:</td>
                    <td><input type="text" name="pass" size="40" value="<?php print($_SESSION['pass']); ?>" /></td>
                </tr>
            	<tr>
                	<td>Starting Directory:</td>
                    <td><input type="text" name="dir" size="40" value="/" /></td>
                </tr>
            	<tr>
                	<td>Start FTP Browser:</td>
                    <td><input type="submit" value="Start FTP Filetree" /></td>
                </tr>
            </table>
   <?php } ?>
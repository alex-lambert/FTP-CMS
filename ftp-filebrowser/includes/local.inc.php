   <h4 id="curdir"><?php echo $dirpath; ?></h4>
    <div id="fileexplorer"></div>
    <script language="javascript" type="text/javascript">
        Event.observe(window,'load',function(event){
              $('fileexplorer').fileTree({ root: '<?php print($dirpath); ?>', 
                                           script: 'filetree.php' , 
                                           folderEvent: 'dblclick', 
                                           loadMessage: 'Please wait while the file list loads "<b><?php print($dirpath); ?></b>"'
                                          }, fileExplorerFileClick);
        });
        
        function fileExplorerFileClick(file){
            var strippedPath = file.replace(/(\/\/)+/,'/');
            alert('path: ' + strippedPath);
        }
    </script>
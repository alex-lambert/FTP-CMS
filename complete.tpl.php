<h1>Congratulations!</h1>
<p>XimpleCMS has successfully Edited and Uploaded <strong><?php print($ftped->filename); ?></strong> to remote host <strong><?php print($ftped->server); ?>.</p>
<p><a href="index.php">Click here open the editor again</a></p>
<p><a href="index.php?restart=true">Click here to restart XimpleCMS</a></p>
<?php $_SESSION['step'] = 2; ?>
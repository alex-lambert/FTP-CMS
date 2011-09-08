<?php global $ftped; ?>
<h4>Remote file editor - Editing: <?php  print($ftped->filename);?></h4>
<?php if($_SESSION['editor'] == 'Basic'){ ?>
<a href="index.php?editor=Default">advanced editor</a>
<?php }else{ ?>
<a href="index.php?editor=Basic">simple editor</a>
<?php } ?>

<p><a href="index.php?editor=textarea">textarea only (if your having problems with Server side code try this)</a></p>
<form name="ftpeditor" id="ftpeditor" action="index.php?action=save" method="post">
<?php
foreach($blocks as $blockID => $content){ ?>
<div>
	<h3>Content block ID: <?php print($blockID); ?></h3>
	<?php
		if($_SESSION['editor'] == 'textarea'){
			$textarea = '<textarea id="'.$blockID.'" name="'.$blockID.'" rows="50" style="width: 100%">'.$content.'</textarea>';
			print($textarea);
		}else{
			$editor = new FCKeditor($blockID);
			$editor->BasePath = 'fckeditor/';
			$editor->Config['FullPage'] = 'false';
			$editor->Config['HtmlEncodeOutput'] = 'false';
			$editor->Config['ProcessHTMLEntities'] = 'false';
			$editor->Config['FormatSource'] = 'true';
			$editor->Config['EnterMode'] = 'br';
			$editor->Config['ShiftEnterMode'] = 'p';
			$editor->Config['ToolbarStartExpanded'] = 'true';
			$editor->Height = '400';
			$editor->Value = $content;
			$editor->ToolbarSet = ucfirst($_SESSION['editor']);
			$editor->Create();
		}
	?>
</div>
<hr />
<?php } ?>
<center><input type="submit" name="submit" id="submit" value="Save to Remote Host" /></center>
</form>
<script language="javascript" type="text/javascript">
	SwapStyle('visone','set');
	SwapStyle('vistwo','set');
	SwapStyle('visthree','set');
	SwapStyle('editing','set');
</script>
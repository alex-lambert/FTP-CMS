<script language="javascript" type="text/javascript">
function checkFormDetails(){

	var errors = false;
	
	var hostObj = document.getElementById('host');
	var userObj = document.getElementById('user');
	var passObj = document.getElementById('pass');
	var fileObj = document.getElementById('filename');
	var attNameObj = document.getElementById('attrib_name');
	var attValObj = document.getElementById('attrib_value');
	
	
	if(hostObj.value == ''){
		errors = true;
		hostObj.parentNode.innerHTML = hostObj.parentNode.innerHTML + '<font color="red">The host field must be filled.</font>';
	}
	
	if(userObj.value == ''){
		errors = true;
		userObj.parentNode.innerHTML = userObj.parentNode.innerHTML + '<font color="red">The user field must be filled.</font>';
	}
		
	if(passObj.value == ''){
		errors = true;
		passObj.parentNode.innerHTML = passObj.parentNode.innerHTML + '<font color="red">The password field must be filled.</font>';
	}

	if(fileObj.value == ''){
		errors = true;
		fileObj.parentNode.innerHTML = fileObj.parentNode.innerHTML + '<font color="red">The filename field must be filled.</font>';
	}
	
	if(attNameObj.value == ''){
		attNameObj.value = 'class';
		document.getElementById('anmsg').style.display = 'none';
	}
	
	if(attValObj.value == ''){
		attValObj.value = 'xcms';
		document.getElementById('avmsg').style.display = 'none';
	}		
				
	if(errors){
		show('step_one');
		show('step_two');
		show('step_three');
		hide('linkstepone');
		hide('linksteptwo');
		hide('linkstepthree');
		alert('There were errors in the editor details. Please ammend them before trying to start the XimpleCMS editor!');
		return false;
	}else{
		show('step_one');
		show('step_two');
		show('step_three');
		return true;
	}
}

</script>
<form action="index.php" method="post" onsubmit="javascript: return checkFormDetails();">
	<div id="step_one">
		<h4>Please enter the FTP details for your server</h4>
		<p>This should be your Hostname(like ftp.domain.tld or IP address), your Username and your Password</p>
		<table width="100%" border="0" cellspacing="10" cellpadding="10">
		  <tr>
			<td width="90px" class="required"><strong>Host: </strong></td>
			<td><input type="text" size="40" name="host" id="host" /></td>
		  </tr>
		  <tr>
			<td width="90px" class="required"><strong>User:</strong></td>
			<td><input type="text" size="40" name="user" id="user" /></td>
		  </tr>
		  <tr>
			<td width="90px" class="required"><strong>Pass:</strong></td>
			<td><input type="password" size="40" name="pass" id="pass" /></td>
		  </tr>
		  <tr id="linkstepone">
		    <td colspan="2">
				<a href="javascript: hide('step_one'); show('step_two'); SwapStyle('visone','set');">Next step:  enter file details &gt;</a>
			</td>
		</table>
	</div>
	
	
	<div id="step_two">
		<h4>Please type the filename and the parent directory of the Remote file you want to edit</h4>
		<p>Leave the directory field blank if it is a root document</p>
		<table width="100%" border="0" cellspacing="10" cellpadding="10">
		  <tr>
			<td width="90px" class="required"><strong>File:</strong></td>
			<td><input type="text" size="40" name="filename" id="filename" /></td>
		  </tr>
		  <tr>
			<td width="90px">Dir:</td>
			<td><label>
			  <input type="text" size="40" name="directory" id="directory" onfocus="javascript: document.getElementById('dirmsg').style.display = 'none';" />
			  <span id="dirmsg"><font color="red">Defaults to domain root directory if not set</font></span> </label></td>
		  </tr>
		  <tr id="linksteptwo">
		    <td colspan="2">
				<a href="javascript: hide('step_two'); show('step_one'); SwapStyle('visone','notset');">&lt; Previous step:  enter file details</a> | <a href="javascript: hide('step_two'); show('step_three'); SwapStyle('vistwo','set');">Next step: enter class details &gt;</a>
			</td>
		  </tr>
		</table>
	</div>
	
	
	<div id="step_three">
		<h4>Please type the Class extension to search for (ie: cushycms, xcms)</h4>
		<table width="100%" border="0" cellspacing="10" cellpadding="10">
		  <tr>
		    <td width="90px">attrib name:</td>
		    <td align="left"><input type="text" size="40" name="attrib_name" id="attrib_name" onfocus="javascript: document.getElementById('anmsg').style.display = 'none';" /><span id="anmsg"><font color="red">Defaults to class if not set</font></span> </td>
	      </tr>
		  <tr>
			<td width="90px">attrib value :</td>
			<td align="left">
			  <input type="text" size="40" name="attrib_value" id="attrib_value" onfocus="javascript: document.getElementById('avmsg').style.display = 'none';" />
			  <span id="avmsg"><font color="red">Defaults to xcms if not set</font></span></td>
		  </tr>
		  <tr id="linkstepthree">
		    <td colspan="2">
				<a href="javascript: hide('step_three'); show('step_two'); SwapStyle('vistwo','notset');">&lt; Previous step: enter class details</a>			</td>
		  </tr>
		  <tr>
		    <td>
				<input type="submit" value="start editor" onclick="javascript: SwapStyle('visthree','set');" />
				&nbsp;&nbsp;&nbsp;
				<input type="reset" value="clear FTP form" onclick="javascript: SwapStyle('visone','notset');  SwapStyle('vistwo','notset');  SwapStyle('visthree','notset'); hide('step_two'); hide('step_three'); show('step_one'); show('linkstepone'); show('linksteptwo'); show('linkstepthree');" />			</td>
		  </tr>
		</table>
	</div>
</form>
<script language="javascript" type="text/javascript">
	document.getElementById('step_two').style.display = 'none';
	document.getElementById('step_three').style.display = 'none';
</script>
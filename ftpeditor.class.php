<?php 
class ftpeditor { 

	public $filename 		= '';
	public $tmp_path 		= '';
	public $domObj 			= '';
	public $contentstack 	= array();
	public $filesource 		= '';
	public $server			= '';
	public $pagetitle		= '';
	
	private $fileext		= '';
	private $username		= ''; 
	private $password		= ''; 
	private $port 			= ''; 
	private $conn 			= '';
	private $attrib_name	= '';
	private $attrib_value	= '';
	private $tempIDs		= array();
	private $remote_dir		= ''; 
	private $serverpath 	= '';
	private $localpath 		= '';
		
		// Creates a new instance of ftpeditor, logs into FTP remote host
		public function __construct($server, $username='anonymous', $password='',$attrib_name='class', $attrib_value='xcms', $port=21){ 
			// Document Object Model - How ftpeditor grabs/replaces content within an XHTMl 1.0 Transitional document
			$this->domObj = new DOMDocument();
			$this->domObj->validateOnParse = true;
			
			// Path for temporary files
			$this->tmp_path = dirname(__FILE__).'/tmp/';
			
			// Remote / Login details
			$this->server=$server; 
			$this->username=$username; 
			$this->password=$password; 
			$this->port=$port; 
			
			// The attribute name XimpleCMS will scan for as editable content blocks
			$this->attrib_name = $attrib_name;
			$this->attrib_value = $attrib_value;
			
			// Attempt Login
			$this->conn = $this->return_connection();
			$this->set_remote_dir();
			
			// Set to passive mode
			ftp_pasv($this->conn, true);
		} 
		
		
		// Prepare the FTP class for a file access action. Chainable function
		public function prepareFTP($filename){
		
			// Filename to work with
			$this->filename = $filename;
			
			// Get the file type (extension)
			$ext = explode('.',$this->filename);
			$id = count($ext) - 1;
			$this->fileext = strtolower($ext[$id]);
			
			// Set local path to temporary file
			$this->tmp_path = $this->tmp_path.$this->filename;
			return $this;
		}
		
		
		// Send a file to the remote host
		public function send($dir=false){ 
		
			// Write the new content to the local copy
			$this->putLocalFileContents();
			
			// If a remote dir was given, switch to it
			if($dir) $this->set_remote_dir($dir); 
			
			// Attempt FTP upload
			$local_fp = fopen($this->tmp_path,'r');
			if($ret = ftp_nb_fput($this->conn, $this->filename, $local_fp, FTP_BINARY)){ 
				while($ret == FTP_MOREDATA){
					// Add Event caller?
					$ret = ftp_nb_continue($this->conn);
				}
				
				if($ret != FTP_FINISHED){
					// Bad upload attempt
					fclose($local_fp);
					die('There was an error uploading local copy to remote host');
				}else{
					// Good upload attempt
					fclose($local_fp);
					unlink($this->tmp_path);
					return true; 
				}
			}else{ 
				// Bad upload attempt
				fclose($local_fp);
				die('Couldnt upload local copy to remote host');
			} 
		} 

        
		
		// Get a file from the remote host. Chainable function
		public function get($dir=false){ 
		
			// Open / Create the local copy
			$handle = fopen($this->tmp_path, 'w'); 
			
			// If a remote dir was given, switch to it
			if($dir) $this->set_remote_dir($dir); 
			
			// Attemp FTP Download
			if(!$ret = ftp_nb_fget($this->conn, $handle, $this->filename, FTP_BINARY, FTP_AUTORESUME)){ 
				return false; 
			}else{ 
				while($ret == FTP_MOREDATA){
					// Add event caller?
					$ret = ftp_nb_continue($this->conn);
				}
				fclose($handle);
				
				// Bad FTP download attempt
				if($ret != FTP_FINISHED){
					die('There was an error downloading the file from the remote host');
				}
				
				// Good FTP download attempt, grab the files source code
				$this->getLocalFileContents();
				return $this; 
			} 
		} 

		
		// Retieves all editable blocks of content from the Dom
		public function parseDomOut($node, $level = 0){
		
			// Loop through each node
			for ($i = 0; $i < $level; $i++){
				// Working with element
				if($node->nodeType == XML_ELEMENT_NODE){
					// Check for editable region (node with required attrubte and value)
					if($node->hasAttribute($this->attrib_name)){
						if($value = $this->getDomAttrib($this->attrib_name,$node->attributes)){
							if(preg_match('/'.$this->attrib_value.'/',$value)){
								// Must be an Editable Block
								if($node->hasAttribute('id')){
									// Has Element attribute ID
									$blockID = $node->getAttribute('id');
								}else{
									// Doesnt have Element attribute ID, so we will create a temporary one
									$num = count($this->tempIDs);
									$blockID = 'tempID'.$num;
									$this->tempIDs[] = $blockID;
									$node->setAttribute('id',$blockID);
								}
								
								// Grab the content of this node
								$tempdom = new domDocument();
								$tempdom->appendChild($tempdom->importNode($node,true));
								
								// Save Block as string
								switch($this->fileext){
									// Normal HTML content
									case 'html' :
									case 'htm' :
									case 'php' :
									case 'asp' :
									case 'phtml' :
									case 'shtml' :
										$this->contentstack[$blockID] = $tempdom->saveHTML();
									break;
									
									// XML file content
									case 'xml' :
										$this->contentstack[$blockID] = $tempdom->saveXML();
									break;
								}
								break;
							}else{
								continue;
							}
						}
					}
				}
			}
			
			// Check for more childNodes
			$cNodes = $node->childNodes;
			if (count($cNodes) > 0){
				 // go one level deeper
				$level++ ;
				foreach($cNodes as $cNode){
					// Go recursive to continue parsing the document
					$this->parseDomOut($cNode, $level);
				}
			}
			
			// Passes the stack back if needed
			return $this->contentstack;
		}
		
		
		// Replaces editable blocks in Dom with new blocks from $contentarray
		public function parseDomIn($node, &$contentarray, $level = 0){

			// Loop through each node
			for ($i = 0; $i < $level; $i++){
				// Working with element
				if($node->nodeType == XML_ELEMENT_NODE){
					// get all the attributes(eg: id, class …)
					$attributes = $node->attributes;
					foreach($attributes as $attribute){
						if($attribute->name == $this->attrib_name){
							if(preg_match('/'.$this->attrib_value.'/', $attribute->value)){
								// Found xcms editable block...
								if($node->hasAttribute('id')){
									// Has Element attribute ID
									$nodeid = $node->getAttribute('id');
								}else{
									// Doesnt have Element attribute ID, so we will create a temporary one
									$num = count($this->tempIDs);
									$nodeid = 'tempID'.$num;
									$this->tempIDs[] = $nodeid;
									$node->setAttribute('id',$nodeid);
								}
								
								// Loop through the new blocks of content
								foreach($contentarray as $id => $content){
									if($id == $nodeid){
										// Build new dom object from content with matching element ID
										$tempdom = new domDocument();
										$tempdom->validateOnParse = true;
										
										// Grab the content as new Dom
										switch($this->fileext){
											case 'html' :
											case 'htm' :
											case 'php' :
											case 'asp' :
											case 'phtml' :
											case 'shtml' :
												$tempdom->loadHTML($content);
											break;
											
											case 'xml' :
												$tempdom->loadXML($content);
											break;
										}
										
										
										// Extract the node to replace with from the content dom
										$xp = new DOMXPath($tempdom);
										$newnode = $xp->query("//*[@id = '$nodeid']");
										
										// Replace old node with new node
										if(($curnode = $this->domObj->getElementById($nodeid)) != NULL){
											$curnode->parentNode->replaceChild($this->domObj->importNode($newnode->item(0),true),
																		$this->domObj->getElementById($nodeid));
										}
										// Remove the current content block from $contentarray
										unset($contentarray[$nodeid]);
										
										// Recursive call, start at the top of the Dom again
										if(count($contentarray) > 0){
											$this->parseDomIn($this->domObj,&$contentarray,$level++);
										}
									}
								}
							}
						}
					}
				}
			}
			
			// Check for more childNodes
			$cNodes = $node->childNodes;
			if (count($cNodes) > 0){
	 			// go one level deeper
				$level++ ;
				foreach($cNodes as $cNode){
					// Go recursive to continue parsing the document
					$this->parseDomIn($cNode, &$contentarray, $level);
				}
			}
			
			return $this;
		}
		
		
		// Magic method, cleanup connections on request end.
		public function __destruct(){ 
			if($this->conn) $this->disconnect(); 
			unset($this); 
		} 
	
		
		// return the name 
		private function getDomAttrib($attribname,$node){
			foreach($node as $i){
				if($i->name == $attribname) return $i->value;        
			}
			
			return false;
		}
		
		
		// Load the source from the local copy
		private function getLocalFileContents(){
		
			// Grab local copy source 
			$this->filesource = file_get_contents($this->tmp_path);
			
			// Load it as a new Dom Object
			switch($this->fileext){
				case 'html' :
				case 'htm' :
				case 'php' :
				case 'asp' :
				case 'phtml' :
				case 'shtml' :
					$this->domObj->loadHTML($this->filesource);
				break;
				
				case 'xml' :
					$this->domObj->loadXML($this->filesource);
				break;
			}			
		}
		
				
		// Write the new source to the local copy
		private function putLocalFileContents(){
			//Strip out any temporary IDs we assigned at document load
			foreach($this->tempIDs as $idx => $tmpid){
				$xp = new DOMXpath($this->domObj);
				$tnodes = $xp->query("//*[@id = '$tmpid']");
				foreach($tnodes as $tnode){
					$tnode->removeAttribute('id');
				}
			}
			
			// get new Dom as string
			switch($this->fileext){
				case 'html' :
				case 'htm' :
				case 'php' :
				case 'asp' :
				case 'phtml' :
				case 'shtml' :
					$this->filesource = $this->domObj->saveHTML();
				break;
				
				case 'xml' :
					$this->filesource = $this->domObj->saveXML();
				break;
			}
			
			// Write the new Dom to the local file
			if(($fp = fopen($this->tmp_path,'w')) == false) die('Couldnt open local file for updating');
			if(!fwrite($fp,$this->filesource,strlen($this->filesource))) die('Couldnt write HTML Source to local file copy');
			
			// Close the local file
			fclose($fp);
		}
		
		
		// Disconnects from the remote FTP  connection
		private function disconnect(){
			ftp_close($this->conn);
		}
		
	
		// Returns a remote FTP connection ID
		private function return_connection(){ 
			// Allow 2 minutes of time to initiate the FTP connection
			set_time_limit(120);
			
			// Attempt Standard FTP connection...
			if(!$conn_id = ftp_connect($this->server, $this->port)){
				// If Standard connection fails attempt SSL connection
				if(!$conn_id = ftp_ssl_connect($this->server, $this->port)){
					die('Could not connect to remote server, tried using SSL connection which failed too.');
				}
			}
			
			// Login to the FTP server
			$login_result = ftp_login($conn_id, $this->username, $this->password) or die("Could not login to FTP remote host"); 
			
			// Switch on Passive Mode
			ftp_pasv($conn_id, true);
			return $conn_id; 
		} 
		
		
		// Sets the remote host directory
		private function set_remote_dir($dir=false){ 
		
			// If no dir is passed change to root directory
			if(!$dir) $dir = ftp_pwd($this->conn);
			
			// Alter slashes to correct format
			$x = substr($dir, (strlen($dir)-1)); 
				if($x != "/" && $x != "\\") 
					$dir.="/"; 
					
			// Set the dir variable
			$this->remote_dir=$dir;
			
			// Change the FTP connection to the given directory 
			ftp_chdir($this->conn,$this->remote_dir);
		} 
} 
?>